import { flushPromises } from '@vue/test-utils'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import {
  button,
  collection,
  loginAs,
  makeArticle,
  makeCategory,
  makeComment,
  mountView,
} from '../../__tests__/helpers'
import ArticleView from '../ArticleView.vue'
import CategoriesView from '../CategoriesView.vue'
import HomeView from '../HomeView.vue'

const api = vi.hoisted(() => ({
  fetchArticles: vi.fn(),
  fetchArticle: vi.fn(),
  fetchCategories: vi.fn(),
  createComment: vi.fn(),
  deleteComment: vi.fn(),
}))

vi.mock('../../api/articles', () => ({ fetchArticles: api.fetchArticles, fetchArticle: api.fetchArticle }))
vi.mock('../../api/categories', () => ({ fetchCategories: api.fetchCategories }))
vi.mock('../../api/comments', () => ({ createComment: api.createComment, deleteComment: api.deleteComment }))

beforeEach(() => {
  Object.values(api).forEach((fn) => fn.mockReset())
})

describe('HomeView', () => {
  const articles = [
    makeArticle({ id: 1, featured: true }),
    makeArticle({ id: 2, title: 'Second' }),
    makeArticle({ id: 3, title: 'Troisième', tags: [] }),
  ]

  it('affiche à la une, flux et grille', async () => {
    api.fetchArticles.mockResolvedValue({ member: articles, totalItems: 20 })
    api.fetchCategories.mockResolvedValue(collection([makeCategory(), makeCategory({ id: 2, name: 'Perf' })]))

    const { wrapper } = await mountView(HomeView)

    expect(wrapper.text()).toContain('Valider les entrées')
    expect(wrapper.text()).toContain('Sécurité')
    expect(api.fetchArticles).toHaveBeenCalledWith(expect.objectContaining({ featured: true }))
  })

  it('filtres : thème, recherche (debounce), pagination, réinitialisation', async () => {
    vi.useFakeTimers()
    api.fetchArticles.mockResolvedValue({ member: articles, totalItems: 20 })
    api.fetchCategories.mockResolvedValue(collection([makeCategory(), makeCategory({ id: 2, name: 'Perf' })]))
    const { wrapper } = await mountView(HomeView)
    api.fetchArticles.mockClear()

    await button(wrapper, 'Suivant').trigger('click')
    await flushPromises()
    expect(api.fetchArticles).toHaveBeenLastCalledWith(expect.objectContaining({ page: 2 }))
    await button(wrapper, 'Précédent').trigger('click')
    await flushPromises()

    await wrapper.find('select').setValue('2')
    await flushPromises()
    expect(api.fetchArticles).toHaveBeenLastCalledWith(expect.objectContaining({ category: 2 }))

    await wrapper.find('input').setValue('php')
    vi.advanceTimersByTime(400)
    await flushPromises()
    expect(api.fetchArticles).toHaveBeenLastCalledWith(expect.objectContaining({ title: 'php' }))

    const themeButtons = wrapper.findAll('button').filter((b) => b.text().includes('Perf'))
    if (themeButtons.length) {
      await themeButtons[0].trigger('click')
      await flushPromises()
    }
    vi.useRealTimers()
  })

  it('état vide avec filtres → réinitialisation', async () => {
    api.fetchArticles.mockResolvedValue(collection([]))
    api.fetchCategories.mockResolvedValue(collection([makeCategory()]))
    const { wrapper } = await mountView(HomeView, { route: '/?category=1' })

    await button(wrapper, 'Réinitialiser les filtres').trigger('click')
    await flushPromises()

    expect(api.fetchArticles).toHaveBeenLastCalledWith(expect.not.objectContaining({ category: 1 }))
  })

  it('état d’erreur', async () => {
    api.fetchArticles.mockImplementation(() => Promise.reject(new Error('500')))
    api.fetchCategories.mockImplementation(() => Promise.reject(new Error('500')))
    const { wrapper } = await mountView(HomeView)

    expect(wrapper.text()).toContain("Le serveur n'a pas répondu")
  })
})

describe('CategoriesView', () => {
  it('liste les catégories', async () => {
    api.fetchCategories.mockResolvedValue(collection([makeCategory(), makeCategory({ id: 2, name: 'Perf', defaultImage: 'https://img' })]))
    const { wrapper } = await mountView(CategoriesView)

    expect(wrapper.text()).toContain('Sécurité')
    expect(wrapper.text()).toContain('Perf')
  })

  it('état vide', async () => {
    api.fetchCategories.mockResolvedValue(collection([]))
    const { wrapper } = await mountView(CategoriesView)
    expect(wrapper.find('.state-block').exists()).toBe(true)
  })

  it('état d’erreur puis nouvel essai', async () => {
    api.fetchCategories.mockImplementationOnce(() => Promise.reject(new Error('500'))).mockResolvedValue(collection([makeCategory()]))
    const { wrapper } = await mountView(CategoriesView)
    expect(wrapper.text()).toContain("Le serveur n'a pas répondu")

    await wrapper.find('.state-block button').trigger('click')
    await flushPromises()
    expect(wrapper.text()).toContain('Sécurité')
  })
})

describe('ArticleView', () => {
  const article = makeArticle({
    id: 7,
    comments: [makeComment({ id: 11 })],
    nbComments: 1,
    featured: true,
  })

  it('affiche l’article, ses commentaires et les articles liés', async () => {
    api.fetchArticle.mockResolvedValue(article)
    api.fetchArticles.mockResolvedValue(collection([article, makeArticle({ id: 8, title: 'Lié' })]))

    const { wrapper } = await mountView(ArticleView, { props: { id: '7' }, route: '/articles/7' })

    expect(api.fetchArticle).toHaveBeenCalledWith(7)
    expect(api.fetchArticles).toHaveBeenCalledWith(expect.objectContaining({ tag: 'php' }))
    expect(wrapper.text()).toContain('Valider les entrées')
    expect(wrapper.text()).toContain('Très utile')
    expect(wrapper.text()).toContain('Lié')
    expect(wrapper.text()).toContain('Connectez-vous')

    window.dispatchEvent(new Event('scroll'))
    wrapper.unmount()
  })

  it('sans tag : articles liés par catégorie ; changement d’id recharge', async () => {
    api.fetchArticle.mockResolvedValue(makeArticle({ id: 7, tags: [] }))
    api.fetchArticles.mockImplementation(() => Promise.reject(new Error('non bloquant')))

    const { wrapper } = await mountView(ArticleView, { props: { id: '7' } })
    expect(api.fetchArticles).toHaveBeenCalledWith(expect.objectContaining({ category: 1 }))

    await wrapper.setProps({ id: '8' })
    await flushPromises()
    expect(api.fetchArticle).toHaveBeenLastCalledWith(8)
  })

  it('article introuvable', async () => {
    api.fetchArticle.mockImplementation(() => Promise.reject(new Error('404')))
    const { wrapper } = await mountView(ArticleView, { props: { id: '999' } })

    expect(wrapper.text()).toContain('Cette pratique est introuvable')
  })

  it('utilisateur connecté : publie un commentaire', async () => {
    loginAs(['ROLE_USER'])
    api.fetchArticle.mockResolvedValue(article)
    api.fetchArticles.mockResolvedValue(collection([]))
    api.createComment.mockResolvedValue({})
    const { wrapper } = await mountView(ArticleView, { props: { id: '7' } })

    await wrapper.find('form input').setValue('Mon avis')
    await wrapper.find('form textarea').setValue('Excellent')
    await wrapper.find('form').trigger('submit')
    await flushPromises()

    expect(api.createComment).toHaveBeenCalledWith({ title: 'Mon avis', description: 'Excellent', article: 7 })
  })

  it('erreur à la publication du commentaire', async () => {
    loginAs(['ROLE_USER'])
    api.fetchArticle.mockResolvedValue(article)
    api.fetchArticles.mockResolvedValue(collection([]))
    api.createComment.mockImplementation(() => Promise.reject(new Error('422')))
    const { wrapper } = await mountView(ArticleView, { props: { id: '7' } })

    await wrapper.find('form').trigger('submit')
    await flushPromises()

    expect(wrapper.text()).toContain("Impossible d'envoyer le commentaire")
  })

  it('admin : supprime un commentaire après confirmation', async () => {
    loginAs(['ROLE_ADMIN'])
    vi.spyOn(window, 'confirm').mockReturnValueOnce(false).mockReturnValueOnce(true)
    api.fetchArticle.mockResolvedValue(article)
    api.fetchArticles.mockResolvedValue(collection([]))
    api.deleteComment.mockResolvedValue(undefined)
    const { wrapper } = await mountView(ArticleView, { props: { id: '7' } })

    await wrapper.find('button.btn-danger').trigger('click')
    await flushPromises()
    expect(api.deleteComment).not.toHaveBeenCalled()

    await wrapper.find('button.btn-danger').trigger('click')
    await flushPromises()
    expect(api.deleteComment).toHaveBeenCalledWith(11)
  })
})
