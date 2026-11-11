import { flushPromises } from '@vue/test-utils'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import { fakeJwt, mountView } from '../../__tests__/helpers'
import LoginView from '../LoginView.vue'
import NotFoundView from '../NotFoundView.vue'
import RegisterView from '../RegisterView.vue'

const { post } = vi.hoisted(() => ({ post: vi.fn() }))

vi.mock('../../api/http', async (importOriginal) => {
  const original = await importOriginal<typeof import('../../api/http')>()
  return { ...original, http: { post } }
})

const token = fakeJwt({ username: 'jane@exemple.test', roles: ['ROLE_USER'] })

describe('LoginView', () => {
  beforeEach(() => {
    post.mockReset()
  })

  it('valide les champs avant envoi', async () => {
    const { wrapper } = await mountView(LoginView, { route: '/login' })

    await wrapper.find('form').trigger('submit')
    expect(wrapper.text()).toContain('Adresse e-mail requise.')
    expect(wrapper.text()).toContain('Mot de passe requis.')

    await wrapper.find('#login-email').setValue('jane@')
    await wrapper.find('#login-password').setValue('court')
    await wrapper.find('#login-email').trigger('blur')
    expect(wrapper.text()).toContain('Adresse e-mail incomplète.')
    expect(wrapper.text()).toContain('Au moins 8 caractères.')
    expect(post).not.toHaveBeenCalled()
  })

  it('connecte puis redirige vers la cible demandée', async () => {
    post.mockResolvedValue({ data: { token, refresh_token: 'r' } })
    const { wrapper, router } = await mountView(LoginView, { route: '/login?redirect=/categories' })

    await wrapper.find('#login-email').setValue('jane@exemple.test')
    await wrapper.find('#login-password').setValue('password123')
    await wrapper.find('form').trigger('submit')
    await flushPromises()

    expect(router.currentRoute.value.fullPath).toBe('/categories')
  })

  it('affiche une erreur si les identifiants sont refusés', async () => {
    post.mockImplementation(() => Promise.reject(new Error('401')))
    const { wrapper } = await mountView(LoginView, { route: '/login' })

    await wrapper.find('#login-email').setValue('jane@exemple.test')
    await wrapper.find('#login-password').setValue('mauvais-mdp')
    await wrapper.find('form').trigger('submit')
    await flushPromises()

    expect(wrapper.find('[role="alert"]').text()).toContain('Identifiants incorrects')
  })
})

describe('RegisterView', () => {
  beforeEach(() => {
    post.mockReset()
  })

  async function fill(wrapper: Awaited<ReturnType<typeof mountView>>['wrapper']) {
    const inputs = wrapper.findAll('input')
    await inputs[0].setValue('Jane')
    await inputs[1].setValue('jane@exemple.test')
    await inputs[2].setValue('secret123')
    await wrapper.find('form').trigger('submit')
    await flushPromises()
  }

  it('inscrit, connecte et redirige vers l’accueil', async () => {
    post.mockImplementation((url: string) =>
      Promise.resolve({ data: url === '/login_check' ? { token, refresh_token: 'r' } : {} }),
    )
    const { wrapper, router } = await mountView(RegisterView, { route: '/register' })

    await fill(wrapper)

    expect(post).toHaveBeenCalledWith('/users', { email: 'jane@exemple.test', name: 'Jane', plainPassword: 'secret123' })
    expect(router.currentRoute.value.name).toBe('home')
  })

  it('affiche une erreur en cas d’échec', async () => {
    post.mockImplementation(() => Promise.reject(new Error('422')))
    const { wrapper } = await mountView(RegisterView, { route: '/register' })

    await fill(wrapper)

    expect(wrapper.text()).toContain("L'inscription a échoué")
  })
})

describe('NotFoundView', () => {
  it('propose un retour à l’accueil', async () => {
    const { wrapper } = await mountView(NotFoundView)
    expect(wrapper.find('a').exists()).toBe(true)
  })
})
