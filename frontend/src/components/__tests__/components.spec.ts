import { flushPromises, mount } from '@vue/test-utils'
import { describe, expect, it } from 'vitest'
import { loginAs, mountView } from '../../__tests__/helpers'
import App from '../../App.vue'
import ArticleDetailSkeleton from '../ArticleDetailSkeleton.vue'
import ArticleGridSkeleton from '../ArticleGridSkeleton.vue'
import HeroGraph from '../HeroGraph.vue'
import NavBar from '../NavBar.vue'
import QualityFooter from '../QualityFooter.vue'
import StateEmpty from '../StateEmpty.vue'

describe('composants de présentation', () => {
  it('StateEmpty affiche titre, message, icône et actions', () => {
    const wrapper = mount(StateEmpty, {
      props: { title: 'Rien ici', message: 'Essayez autre chose', icon: '★' },
      slots: { actions: '<button>Réessayer</button>' },
    })

    expect(wrapper.text()).toContain('Rien ici')
    expect(wrapper.text()).toContain('Essayez autre chose')
    expect(wrapper.text()).toContain('★')
    expect(wrapper.find('.actions button').exists()).toBe(true)
  })

  it('StateEmpty sans message ni actions', () => {
    const wrapper = mount(StateEmpty, { props: { title: 'Vide' } })

    expect(wrapper.find('p').exists()).toBe(false)
    expect(wrapper.find('.actions').exists()).toBe(false)
    expect(wrapper.text()).toContain('◇')
  })

  it('squelettes, footer et graphe se rendent', () => {
    expect(mount(ArticleGridSkeleton).findAll('.skeleton').length).toBeGreaterThan(0)
    expect(mount(ArticleDetailSkeleton).html()).toContain('skeleton')
    expect(mount(QualityFooter).text()).toContain('goodPractice')
    expect(mount(HeroGraph).find('svg').exists()).toBe(true)
  })
})

describe('NavBar', () => {
  it('anonyme : liens de connexion, pas d’admin', async () => {
    const { wrapper } = await mountView(NavBar)

    expect(wrapper.text()).toContain('Connexion')
    expect(wrapper.text()).toContain('Inscription')
    expect(wrapper.text()).not.toContain('Admin')
  })

  it('éditeur connecté : lien admin et déconnexion', async () => {
    loginAs(['ROLE_EDITOR'], 'editor@exemple.test')
    const { wrapper, router } = await mountView(NavBar, { route: '/categories' })

    expect(wrapper.text()).toContain('Admin')
    expect(wrapper.text()).toContain('editor@exemple.test')

    await wrapper.find('button.btn-ghost').trigger('click')
    await flushPromises()

    expect(localStorage.getItem('gp_token')).toBeNull()
    expect(router.currentRoute.value.name).toBe('home')
    expect(wrapper.text()).toContain('Connexion')
  })

  it('bascule de thème', async () => {
    const { wrapper } = await mountView(NavBar)
    const toggle = wrapper.find('button.theme-toggle')
    const before = toggle.text()

    await toggle.trigger('click')

    expect(toggle.text()).not.toBe(before)
  })
})

describe('App', () => {
  it('assemble navbar, vue et footer', async () => {
    const { wrapper } = await mountView(App)

    expect(wrapper.find('header.navbar').exists()).toBe(true)
    expect(wrapper.find('footer.site-footer').exists()).toBe(true)
  })
})
