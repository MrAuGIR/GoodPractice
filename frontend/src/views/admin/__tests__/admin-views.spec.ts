import { AxiosError, type AxiosResponse } from 'axios'
import { flushPromises } from '@vue/test-utils'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import {
  button,
  collection,
  loginAs,
  makeArticle,
  makeCategory,
  makeUser,
  mountView,
} from '../../../__tests__/helpers'
import AdminArticleForm from '../AdminArticleForm.vue'
import AdminArticles from '../AdminArticles.vue'
import AdminCategories from '../AdminCategories.vue'
import AdminImport from '../AdminImport.vue'
import AdminLayout from '../AdminLayout.vue'
import AdminUsers from '../AdminUsers.vue'

const api = vi.hoisted(() => ({
  fetchArticles: vi.fn(),
  fetchArticle: vi.fn(),
  createArticle: vi.fn(),
  updateArticle: vi.fn(),
  deleteArticle: vi.fn(),
  fetchCategories: vi.fn(),
  createCategory: vi.fn(),
  updateCategory: vi.fn(),
  deleteCategory: vi.fn(),
  fetchTags: vi.fn(),
  fetchOpenGraph: vi.fn(),
  importBonnesPratiques: vi.fn(),
  fetchUsers: vi.fn(),
  updateUserRoles: vi.fn(),
  deleteUser: vi.fn(),
}))

vi.mock('../../../api/articles', () => ({
  fetchArticles: api.fetchArticles,
  fetchArticle: api.fetchArticle,
  createArticle: api.createArticle,
  updateArticle: api.updateArticle,
  deleteArticle: api.deleteArticle,
}))
vi.mock('../../../api/categories', () => ({
  fetchCategories: api.fetchCategories,
  createCategory: api.createCategory,
  updateCategory: api.updateCategory,
  deleteCategory: api.deleteCategory,
}))
vi.mock('../../../api/tags', () => ({ fetchTags: api.fetchTags }))
vi.mock('../../../api/enrich', () => ({ fetchOpenGraph: api.fetchOpenGraph }))
vi.mock('../../../api/import', () => ({ importBonnesPratiques: api.importBonnesPratiques }))
vi.mock('../../../api/users', () => ({
  fetchUsers: api.fetchUsers,
  updateUserRoles: api.updateUserRoles,
  deleteUser: api.deleteUser,
}))

const fail = (message = 'refusé') => () => Promise.reject(new Error(message))

function axiosError(data: unknown): AxiosError {
  return new AxiosError('422', '422', undefined, null, { data, status: 422 } as AxiosResponse)
}

beforeEach(() => {
  Object.values(api).forEach((fn) => fn.mockReset())
})

describe('AdminLayout', () => {
  it('éditeur : pas d’onglets admin-only', async () => {
    loginAs(['ROLE_EDITOR'])
    const { wrapper } = await mountView(AdminLayout)
    expect(wrapper.text()).not.toContain('Utilisateurs')
  })

  it('admin : tous les onglets', async () => {
    loginAs(['ROLE_ADMIN'])
    const { wrapper } = await mountView(AdminLayout)
    expect(wrapper.text()).toContain('Utilisateurs')
  })
})

describe('AdminArticles', () => {
  it('liste puis supprime après confirmation', async () => {
    vi.spyOn(window, 'confirm').mockReturnValueOnce(false).mockReturnValue(true)
    api.fetchArticles.mockResolvedValue(collection([makeArticle({ featured: true }), makeArticle({ id: 2, title: 'Autre' })]))
    api.deleteArticle.mockResolvedValue(undefined)
    const { wrapper } = await mountView(AdminArticles)

    expect(wrapper.text()).toContain('Valider les entrées')

    await button(wrapper, 'Suppr.').trigger('click')
    expect(api.deleteArticle).not.toHaveBeenCalled()

    await button(wrapper, 'Suppr.').trigger('click')
    await flushPromises()
    expect(api.deleteArticle).toHaveBeenCalledWith(1)
  })

  it('suppression refusée', async () => {
    vi.spyOn(window, 'confirm').mockReturnValue(true)
    api.fetchArticles.mockResolvedValue(collection([makeArticle()]))
    api.deleteArticle.mockImplementation(fail())
    const { wrapper } = await mountView(AdminArticles)

    await button(wrapper, 'Suppr.').trigger('click')
    await flushPromises()
    expect(wrapper.text()).toContain('Suppression refusée')
  })

  it('états vide et erreur', async () => {
    api.fetchArticles.mockResolvedValue(collection([]))
    expect((await mountView(AdminArticles)).wrapper.find('.state-block').exists()).toBe(true)

    api.fetchArticles.mockImplementation(fail())
    expect((await mountView(AdminArticles)).wrapper.text()).toContain('Chargement impossible')
  })
})

describe('AdminArticleForm', () => {
  const categories = collection([makeCategory({ id: 3 }), makeCategory({ id: 4, name: 'Perf' })])
  const tags = collection([
    { id: 1, name: 'PHP', slug: 'php' },
    { id: 2, name: 'Vue', slug: 'vue' },
  ])

  beforeEach(() => {
    api.fetchCategories.mockResolvedValue(categories)
    api.fetchTags.mockResolvedValue(tags)
  })

  it('création : catégorie par défaut, tags, enregistrement puis retour liste', async () => {
    api.createArticle.mockResolvedValue({})
    const { wrapper, router } = await mountView(AdminArticleForm)

    await wrapper.find('input.input').setValue('Nouveau')
    await wrapper.find('textarea').setValue('Contenu')
    await button(wrapper, '#vue').trigger('click')
    await button(wrapper, '#php').trigger('click')
    await button(wrapper, '#php').trigger('click')
    await wrapper.find('form').trigger('submit')
    await flushPromises()

    expect(api.createArticle).toHaveBeenCalledWith(
      expect.objectContaining({ title: 'Nouveau', category: 3, tags: [2] }),
    )
    expect(router.currentRoute.value.name).toBe('admin-articles')
  })

  it('édition : pré-remplit puis met à jour', async () => {
    api.fetchArticle.mockResolvedValue(makeArticle({ id: 9, category: { id: 4, name: 'Perf' }, url: null }))
    api.updateArticle.mockResolvedValue({})
    const { wrapper } = await mountView(AdminArticleForm, { props: { id: '9' } })

    expect((wrapper.find('input.input').element as HTMLInputElement).value).toBe('Valider les entrées')

    await wrapper.find('form').trigger('submit')
    await flushPromises()
    expect(api.updateArticle).toHaveBeenCalledWith(9, expect.objectContaining({ category: 4, url: '', tags: [1] }))
  })

  it('erreur d’enregistrement et de chargement', async () => {
    api.createArticle.mockImplementation(fail())
    const { wrapper } = await mountView(AdminArticleForm)
    await wrapper.find('form').trigger('submit')
    await flushPromises()
    expect(wrapper.text()).toContain('Enregistrement refusé')

    api.fetchCategories.mockImplementation(fail())
    expect((await mountView(AdminArticleForm)).wrapper.text()).toContain('Chargement impossible')
  })

  it('enrichissement OpenGraph : pré-remplit les champs vides', async () => {
    api.fetchOpenGraph.mockResolvedValue({ title: 'OG titre', description: 'OG desc', image: 'https://img', siteName: null, url: null })
    const { wrapper } = await mountView(AdminArticleForm)

    await wrapper.find('input[type="url"]').setValue('https://exemple.test')
    await button(wrapper, 'Récupérer les métadonnées').trigger('click')
    await flushPromises()

    expect(wrapper.text()).toContain('Champs pré-remplis : image, titre, description.')

    await button(wrapper, 'Récupérer les métadonnées').trigger('click')
    await flushPromises()
    expect(wrapper.text()).toContain('aucun champ vide')
  })

  it('enrichissement : message d’erreur API ou générique', async () => {
    api.fetchOpenGraph.mockImplementationOnce(() => Promise.reject(axiosError({ error: 'URL invalide' })))
    api.fetchOpenGraph.mockImplementationOnce(fail())
    const { wrapper } = await mountView(AdminArticleForm)

    await wrapper.find('input[type="url"]').setValue('https://exemple.test')
    await button(wrapper, 'Récupérer les métadonnées').trigger('click')
    await flushPromises()
    expect(wrapper.text()).toContain('URL invalide')

    await button(wrapper, 'Récupérer les métadonnées').trigger('click')
    await flushPromises()
    expect(wrapper.text()).toContain('Enrichissement impossible')
  })
})

describe('AdminCategories', () => {
  it('création, édition, annulation et suppression', async () => {
    vi.spyOn(window, 'confirm').mockReturnValueOnce(false).mockReturnValue(true)
    api.fetchCategories.mockResolvedValue(collection([makeCategory({ id: 5, defaultImage: 'https://img' })]))
    api.createCategory.mockResolvedValue({})
    api.updateCategory.mockResolvedValue({})
    api.deleteCategory.mockResolvedValue(undefined)
    const { wrapper } = await mountView(AdminCategories)

    await wrapper.find('form input').setValue('Nouvelle')
    await wrapper.find('form').trigger('submit')
    await flushPromises()
    expect(api.createCategory).toHaveBeenCalledWith({ name: 'Nouvelle', defaultImage: '' })

    await button(wrapper, 'Éditer').trigger('click')
    expect(button(wrapper, 'Modifier').exists()).toBe(true)
    await wrapper.find('form').trigger('submit')
    await flushPromises()
    expect(api.updateCategory).toHaveBeenCalledWith(5, { name: 'Sécurité', defaultImage: 'https://img' })

    await button(wrapper, 'Éditer').trigger('click')
    await button(wrapper, 'Annuler').trigger('click')
    expect(button(wrapper, 'Ajouter').exists()).toBe(true)

    await button(wrapper, 'Suppr.').trigger('click')
    expect(api.deleteCategory).not.toHaveBeenCalled()
    await button(wrapper, 'Suppr.').trigger('click')
    await flushPromises()
    expect(api.deleteCategory).toHaveBeenCalledWith(5)
  })

  it('erreurs de chargement, d’enregistrement et de suppression', async () => {
    vi.spyOn(window, 'confirm').mockReturnValue(true)
    api.fetchCategories.mockImplementationOnce(fail())
    expect((await mountView(AdminCategories)).wrapper.text()).toContain('Chargement impossible')

    api.fetchCategories.mockResolvedValue(collection([makeCategory()]))
    api.createCategory.mockImplementation(fail())
    api.deleteCategory.mockImplementation(fail())
    const { wrapper } = await mountView(AdminCategories)

    await wrapper.find('form').trigger('submit')
    await flushPromises()
    expect(wrapper.text()).toContain('Enregistrement refusé')

    await button(wrapper, 'Suppr.').trigger('click')
    await flushPromises()
    expect(wrapper.text()).toContain('Suppression refusée')
  })
})

describe('AdminUsers', () => {
  const users = collection([
    makeUser({ id: 1, roles: ['ROLE_ADMIN'] }),
    makeUser({ id: 2, name: 'Ed', roles: ['ROLE_EDITOR'] }),
    makeUser({ id: 3, name: 'Lu', roles: ['ROLE_USER'] }),
  ])

  it('change le rôle selon le niveau choisi', async () => {
    api.fetchUsers.mockResolvedValue(users)
    api.updateUserRoles.mockImplementation((_id: number, roles: string[]) => Promise.resolve({ roles }))
    const { wrapper } = await mountView(AdminUsers)
    const selects = wrapper.findAll('select')

    expect((selects[0].element as HTMLSelectElement).value).toBe('admin')
    expect((selects[1].element as HTMLSelectElement).value).toBe('editor')
    expect((selects[2].element as HTMLSelectElement).value).toBe('user')

    await selects[2].setValue('editor')
    await flushPromises()
    expect(api.updateUserRoles).toHaveBeenLastCalledWith(3, ['ROLE_EDITOR'])

    await selects[2].setValue('admin')
    await flushPromises()
    expect(api.updateUserRoles).toHaveBeenLastCalledWith(3, ['ROLE_ADMIN'])

    await selects[0].setValue('user')
    await flushPromises()
    expect(api.updateUserRoles).toHaveBeenLastCalledWith(1, [])
  })

  it('rôle refusé → message et rechargement', async () => {
    api.fetchUsers.mockResolvedValue(users)
    api.updateUserRoles.mockImplementation(fail())
    const { wrapper } = await mountView(AdminUsers)

    await wrapper.findAll('select')[2].setValue('admin')
    await flushPromises()

    expect(wrapper.text()).toContain('Mise à jour du rôle refusée')
    expect(api.fetchUsers).toHaveBeenCalledTimes(2)
  })

  it('suppression avec confirmation, refus et erreur de chargement', async () => {
    vi.spyOn(window, 'confirm').mockReturnValueOnce(false).mockReturnValue(true)
    api.fetchUsers.mockResolvedValue(users)
    api.deleteUser.mockResolvedValueOnce(undefined).mockImplementation(fail())
    const { wrapper } = await mountView(AdminUsers)

    await button(wrapper, 'Suppr.').trigger('click')
    expect(api.deleteUser).not.toHaveBeenCalled()

    await button(wrapper, 'Suppr.').trigger('click')
    await flushPromises()
    expect(api.deleteUser).toHaveBeenCalledWith(1)

    await button(wrapper, 'Suppr.').trigger('click')
    await flushPromises()
    expect(wrapper.text()).toContain('Suppression refusée')

    api.fetchUsers.mockImplementation(fail())
    expect((await mountView(AdminUsers)).wrapper.text()).toContain('Chargement impossible')
  })
})

describe('AdminImport', () => {
  async function selectFile(wrapper: Awaited<ReturnType<typeof mountView>>['wrapper'], file: File | null) {
    const input = wrapper.find('input[type="file"]')
    Object.defineProperty(input.element, 'files', { value: file ? [file] : [], configurable: true })
    await input.trigger('change')
    await new Promise((r) => setTimeout(r, 20)) // FileReader asynchrone
    await flushPromises()
  }

  const file = () => new File(['{"categories":[],"articles":[]}'], 'import.json', { type: 'application/json' })

  it('lit le fichier puis lance l’import (dry-run par défaut)', async () => {
    api.importBonnesPratiques.mockResolvedValue({
      dryRun: true,
      categoriesCreated: 1,
      tagsCreated: 2,
      articlesCreated: 3,
      articlesUpdated: 0,
      articlesSkipped: 0,
    })
    const { wrapper } = await mountView(AdminImport)

    await selectFile(wrapper, file())
    expect(wrapper.text()).toContain('import.json')

    await wrapper.find('button.btn').trigger('click')
    await flushPromises()

    expect(api.importBonnesPratiques).toHaveBeenCalledWith('{"categories":[],"articles":[]}', true)
    expect(wrapper.text()).toContain('3')
  })

  it('affiche les erreurs de validation renvoyées par l’API', async () => {
    api.importBonnesPratiques
      .mockImplementationOnce(() => Promise.reject(axiosError({ errors: ['articles[0].title : vide'] })))
      .mockImplementationOnce(fail())
    const { wrapper } = await mountView(AdminImport)
    await selectFile(wrapper, file())

    await wrapper.find('button.btn').trigger('click')
    await flushPromises()
    expect(wrapper.text()).toContain('articles[0].title : vide')

    await wrapper.find('button.btn').trigger('click')
    await flushPromises()
    expect(wrapper.text()).toContain("L'import a échoué")
  })

  it('désélection du fichier : rien à envoyer', async () => {
    const { wrapper } = await mountView(AdminImport)
    await selectFile(wrapper, null)

    expect(wrapper.find('button.btn').attributes('disabled')).toBeDefined()
  })
})
