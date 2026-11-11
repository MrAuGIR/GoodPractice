import { createPinia, setActivePinia } from 'pinia'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import { fakeJwt, loginAs } from '../../__tests__/helpers'

const { post } = vi.hoisted(() => ({ post: vi.fn() }))

vi.mock('../../api/http', async (importOriginal) => {
  const original = await importOriginal<typeof import('../../api/http')>()
  return { ...original, http: { post } }
})

import { useAuthStore } from '../auth'

describe('store auth', () => {
  beforeEach(() => {
    post.mockReset()
    setActivePinia(createPinia())
  })

  it('anonyme par défaut', () => {
    const auth = useAuthStore()
    expect(auth.isAuthenticated).toBe(false)
    expect(auth.username).toBe('')
    expect(auth.roles).toEqual([])
    expect(auth.isEditor).toBe(false)
  })

  it('restaure la session depuis le jeton stocké', () => {
    loginAs(['ROLE_EDITOR', 'ROLE_USER'], 'editor@exemple.test')
    const auth = useAuthStore()

    expect(auth.isAuthenticated).toBe(true)
    expect(auth.username).toBe('editor@exemple.test')
    expect(auth.isEditor).toBe(true)
    expect(auth.isAdmin).toBe(false)
    expect(auth.hasRole('ROLE_USER')).toBe(true)
  })

  it('un jeton illisible est ignoré', () => {
    localStorage.setItem('gp_token', 'pas-un-jwt')
    expect(useAuthStore().isAuthenticated).toBe(false)
  })

  it('login stocke les jetons et décode les rôles', async () => {
    const token = fakeJwt({ username: 'admin@exemple.test', roles: ['ROLE_ADMIN'] })
    post.mockResolvedValue({ data: { token, refresh_token: 'r' } })
    const auth = useAuthStore()

    await auth.login('admin@exemple.test', 'password')

    expect(post).toHaveBeenCalledWith(
      '/login_check',
      { email: 'admin@exemple.test', password: 'password' },
      expect.anything(),
    )
    expect(localStorage.getItem('gp_token')).toBe(token)
    expect(auth.isAdmin).toBe(true)
    expect(auth.isEditor).toBe(true)
  })

  it('register appelle la création de compte', async () => {
    post.mockResolvedValue({ data: {} })
    await useAuthStore().register('a@b.c', 'A', 'secret123')
    expect(post).toHaveBeenCalledWith('/users', { email: 'a@b.c', name: 'A', plainPassword: 'secret123' })
  })

  it('logout vide la session', () => {
    loginAs(['ROLE_USER'])
    const auth = useAuthStore()

    auth.logout()

    expect(auth.isAuthenticated).toBe(false)
    expect(localStorage.getItem('gp_token')).toBeNull()
  })
})
