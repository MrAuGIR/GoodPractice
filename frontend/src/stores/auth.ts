import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { http, tokenStore } from '../api/http'
import type { TokenPair } from '../types'

interface JwtPayload {
  username: string
  roles: string[]
  exp: number
}

function decodeJwt(token: string): JwtPayload | null {
  try {
    const payload = token.split('.')[1]
    const json = atob(payload.replace(/-/g, '+').replace(/_/g, '/'))
    return JSON.parse(json) as JwtPayload
  } catch {
    return null
  }
}

export const useAuthStore = defineStore('auth', () => {
  const payload = ref<JwtPayload | null>(null)

  const token = tokenStore.access()
  if (token) {
    payload.value = decodeJwt(token)
  }

  const isAuthenticated = computed(() => payload.value !== null)
  const username = computed(() => payload.value?.username ?? '')
  const roles = computed(() => payload.value?.roles ?? [])
  const hasRole = (role: string): boolean => roles.value.includes(role)
  const isAdmin = computed(() => hasRole('ROLE_ADMIN'))
  const isEditor = computed(() => hasRole('ROLE_EDITOR') || hasRole('ROLE_ADMIN'))

  async function login(email: string, password: string): Promise<void> {
    const { data } = await http.post<TokenPair>(
      '/login_check',
      { email, password },
      { headers: { 'Content-Type': 'application/json' } },
    )
    tokenStore.set(data)
    payload.value = decodeJwt(data.token)
  }

  async function register(email: string, name: string, plainPassword: string): Promise<void> {
    await http.post('/users', { email, name, plainPassword })
  }

  function logout(): void {
    tokenStore.clear()
    payload.value = null
  }

  return {
    isAuthenticated,
    username,
    roles,
    isAdmin,
    isEditor,
    hasRole,
    login,
    register,
    logout,
  }
})
