import axios, { AxiosError, AxiosHeaders, type AxiosResponse, type InternalAxiosRequestConfig } from 'axios'
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest'
import { http, setOnAuthFailure, tokenStore } from '../http'

type Adapter = (config: InternalAxiosRequestConfig) => Promise<AxiosResponse>

function respond(config: InternalAxiosRequestConfig, status: number, data: unknown = {}): Promise<AxiosResponse> {
  const response = { data, status, statusText: '', headers: {}, config } as AxiosResponse
  if (status >= 400) {
    return Promise.reject(new AxiosError('error', String(status), config, null, response))
  }
  return Promise.resolve(response)
}

describe('client http', () => {
  const originalAdapter = http.defaults.adapter

  beforeEach(() => {
    tokenStore.clear()
  })

  afterEach(() => {
    http.defaults.adapter = originalAdapter
  })

  it('tokenStore persiste et efface la paire de jetons', () => {
    tokenStore.set({ token: 'a', refresh_token: 'r' })
    expect(tokenStore.access()).toBe('a')
    expect(tokenStore.refresh()).toBe('r')
    tokenStore.clear()
    expect(tokenStore.access()).toBeNull()
    expect(tokenStore.refresh()).toBeNull()
  })

  it('ajoute le header Authorization quand un jeton existe', async () => {
    tokenStore.set({ token: 'jwt-123', refresh_token: 'r' })
    const adapter = vi.fn<Adapter>((config) => respond(config, 200))
    http.defaults.adapter = adapter

    await http.get('/articles')

    const headers = AxiosHeaders.from(adapter.mock.calls[0][0].headers)
    expect(headers.get('Authorization')).toBe('Bearer jwt-123')
  })

  it("n'ajoute pas de header sans jeton", async () => {
    const adapter = vi.fn<Adapter>((config) => respond(config, 200))
    http.defaults.adapter = adapter

    await http.get('/articles')

    expect(AxiosHeaders.from(adapter.mock.calls[0][0].headers).has('Authorization')).toBe(false)
  })

  it('rafraîchit le jeton sur 401 puis rejoue la requête', async () => {
    tokenStore.set({ token: 'expired', refresh_token: 'refresh-1' })
    const refresh = vi.spyOn(axios, 'post').mockResolvedValue({ data: { token: 'fresh', refresh_token: 'refresh-2' } })
    const adapter = vi.fn<Adapter>((config) =>
      AxiosHeaders.from(config.headers).get('Authorization') === 'Bearer fresh'
        ? respond(config, 200, { ok: true })
        : respond(config, 401),
    )
    http.defaults.adapter = adapter

    const { data } = await http.get('/users')

    expect(data).toEqual({ ok: true })
    expect(refresh).toHaveBeenCalledWith(
      expect.stringContaining('/token/refresh'),
      { refresh_token: 'refresh-1' },
      expect.anything(),
    )
    expect(tokenStore.access()).toBe('fresh')
    expect(adapter).toHaveBeenCalledTimes(2)
  })

  it('vide la session et notifie si le rafraîchissement échoue', async () => {
    tokenStore.set({ token: 'expired', refresh_token: 'bad' })
    vi.spyOn(axios, 'post').mockRejectedValue(new Error('refresh refusé'))
    const onFailure = vi.fn()
    setOnAuthFailure(onFailure)
    http.defaults.adapter = (config) => respond(config, 401)

    await expect(http.get('/users')).rejects.toBeInstanceOf(AxiosError)

    expect(onFailure).toHaveBeenCalledOnce()
    expect(tokenStore.access()).toBeNull()
  })

  it('ne tente pas de rafraîchir sans refresh token', async () => {
    const refresh = vi.spyOn(axios, 'post')
    http.defaults.adapter = (config) => respond(config, 401)

    await expect(http.get('/users')).rejects.toBeInstanceOf(AxiosError)
    expect(refresh).not.toHaveBeenCalled()
  })
})
