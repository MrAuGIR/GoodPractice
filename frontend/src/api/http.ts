import axios, {
  AxiosError,
  type AxiosInstance,
  type InternalAxiosRequestConfig,
} from 'axios'
import type { TokenPair } from '../types'

const ACCESS_KEY = 'gp_token'
const REFRESH_KEY = 'gp_refresh'

export const tokenStore = {
  access: (): string | null => localStorage.getItem(ACCESS_KEY),
  refresh: (): string | null => localStorage.getItem(REFRESH_KEY),
  set(tokens: TokenPair): void {
    localStorage.setItem(ACCESS_KEY, tokens.token)
    localStorage.setItem(REFRESH_KEY, tokens.refresh_token)
  },
  clear(): void {
    localStorage.removeItem(ACCESS_KEY)
    localStorage.removeItem(REFRESH_KEY)
  },
}

/** Appelé quand le rafraîchissement échoue (session expirée). */
let onAuthFailure: (() => void) | null = null
export function setOnAuthFailure(cb: () => void): void {
  onAuthFailure = cb
}

/**
 * Base des appels HTTP vers l'API. `/api` en dev (proxy Vite / même origine).
 * En prod sans mod_rewrite (IONOS), VITE_API_BASE vaut `/index.php/api`
 * pour router via PATH_INFO. Les IRIs (cf. `iri()`) restent eux en `/api/...`.
 */
export const API_BASE = import.meta.env.VITE_API_BASE ?? '/api'

export const http: AxiosInstance = axios.create({
  baseURL: API_BASE,
  headers: {
    Accept: 'application/ld+json',
    'Content-Type': 'application/ld+json',
  },
})

// Ajoute le Bearer token sur chaque requête
http.interceptors.request.use((config: InternalAxiosRequestConfig) => {
  const token = tokenStore.access()
  if (token) {
    config.headers.set('Authorization', `Bearer ${token}`)
  }
  return config
})

// Rafraîchit automatiquement le token sur 401 (une seule tentative)
let refreshing: Promise<string> | null = null

async function refreshAccessToken(): Promise<string> {
  const refresh = tokenStore.refresh()
  if (!refresh) {
    throw new Error('no refresh token')
  }
  // axios "nu" pour éviter la boucle d'intercepteurs
  const { data } = await axios.post<TokenPair>(
    `${API_BASE}/token/refresh`,
    { refresh_token: refresh },
    { headers: { 'Content-Type': 'application/json' } },
  )
  tokenStore.set(data)
  return data.token
}

http.interceptors.response.use(
  (response) => response,
  async (error: AxiosError) => {
    const original = error.config as (InternalAxiosRequestConfig & { _retry?: boolean }) | undefined

    if (error.response?.status === 401 && original && !original._retry && tokenStore.refresh()) {
      original._retry = true
      try {
        refreshing ??= refreshAccessToken().finally(() => {
          refreshing = null
        })
        const newToken = await refreshing
        original.headers.set('Authorization', `Bearer ${newToken}`)
        return http(original)
      } catch {
        tokenStore.clear()
        onAuthFailure?.()
      }
    }
    return Promise.reject(error)
  },
)

/** Construit l'IRI d'une ressource (relations en écriture). */
export const iri = (resource: string, id: number): string => `/api/${resource}/${id}`
