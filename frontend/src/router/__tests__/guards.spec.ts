import { createPinia, setActivePinia } from 'pinia'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import { loginAs } from '../../__tests__/helpers'

// Les vues ne sont pas montées : on évite de charger leurs dépendances réseau.
vi.mock('../../api/http', async (importOriginal) => {
  const original = await importOriginal<typeof import('../../api/http')>()
  return { ...original, http: { get: vi.fn(() => new Promise(() => {})), post: vi.fn() } }
})

async function freshRouter() {
  vi.resetModules()
  setActivePinia(createPinia())
  const { router } = await import('../index')
  return router
}

describe('garde de navigation', () => {
  beforeEach(() => {
    localStorage.clear()
  })

  it('redirige un anonyme vers /login avec la cible en query', async () => {
    const router = await freshRouter()
    await router.push('/admin/articles')

    expect(router.currentRoute.value.name).toBe('login')
    expect(router.currentRoute.value.query.redirect).toBe('/admin/articles')
  })

  it('autorise un éditeur dans l’admin mais pas sur les pages admin-only', async () => {
    loginAs(['ROLE_EDITOR'])
    const router = await freshRouter()

    await router.push('/admin/articles')
    expect(router.currentRoute.value.name).toBe('admin-articles')

    await router.push('/admin/users')
    expect(router.currentRoute.value.name).toBe('admin-articles')
  })

  it('un admin accède aux pages admin-only', async () => {
    loginAs(['ROLE_ADMIN'])
    const router = await freshRouter()

    await router.push('/admin/users')
    expect(router.currentRoute.value.name).toBe('admin-users')
  })

  it('un utilisateur connecté ne revoit pas login / register', async () => {
    loginAs(['ROLE_USER'])
    const router = await freshRouter()

    await router.push('/login')
    expect(router.currentRoute.value.name).toBe('home')
  })

  it('route inconnue → not-found, scroll en haut', async () => {
    const router = await freshRouter()
    await router.push('/nimporte/quoi')

    expect(router.currentRoute.value.name).toBe('not-found')
    expect(router.options.scrollBehavior?.(router.currentRoute.value, router.currentRoute.value, null)).toEqual({ top: 0 })
  })
})
