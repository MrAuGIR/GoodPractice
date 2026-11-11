import { beforeEach, describe, expect, it, vi } from 'vitest'

const { get, post, put, patch, del } = vi.hoisted(() => ({
  get: vi.fn(),
  post: vi.fn(),
  put: vi.fn(),
  patch: vi.fn(),
  del: vi.fn(),
}))

vi.mock('../http', () => ({
  http: { get, post, put, patch, delete: del },
  iri: (resource: string, id: number) => `/api/${resource}/${id}`,
}))

import { deleteArticle, fetchArticle, fetchArticles } from '../articles'
import {
  createCategory,
  deleteCategory,
  fetchCategories,
  fetchCategory,
  updateCategory,
} from '../categories'
import { createComment, deleteComment, fetchCommentsByArticle } from '../comments'
import { fetchOpenGraph } from '../enrich'
import { importBonnesPratiques } from '../import'
import { fetchTags } from '../tags'
import { deleteUser, fetchUser, fetchUsers, updateUserRoles } from '../users'

describe('modules API', () => {
  beforeEach(() => {
    for (const fn of [get, post, put, patch, del]) {
      fn.mockReset().mockResolvedValue({ data: { ok: true } })
    }
  })

  it('fetchArticles traduit les filtres en paramètres API Platform', async () => {
    await fetchArticles({
      page: 2,
      itemsPerPage: 9,
      title: 'php',
      category: 3,
      tag: 'securite',
      featured: true,
      order: { dateCreate: 'desc' },
    })

    expect(get).toHaveBeenCalledWith('/articles', {
      params: {
        page: 2,
        itemsPerPage: 9,
        title: 'php',
        category: 3,
        'tags.slug': 'securite',
        featured: true,
        'order[dateCreate]': 'desc',
      },
    })
  })

  it('fetchArticles sans filtre envoie des paramètres vides', async () => {
    await fetchArticles()
    expect(get).toHaveBeenCalledWith('/articles', { params: {} })
  })

  it('article : lecture unitaire et suppression', async () => {
    expect(await fetchArticle(4)).toEqual({ ok: true })
    expect(get).toHaveBeenCalledWith('/articles/4')
    await deleteArticle(4)
    expect(del).toHaveBeenCalledWith('/articles/4')
  })

  it('catégories : CRUD et normalisation de l’image', async () => {
    await fetchCategories()
    expect(get).toHaveBeenCalledWith('/categories', { params: { 'order[name]': 'asc', itemsPerPage: 50 } })
    await fetchCategory(2)
    expect(get).toHaveBeenCalledWith('/categories/2')

    await createCategory({ name: 'Nouvelle', defaultImage: '' })
    expect(post).toHaveBeenCalledWith('/categories', { name: 'Nouvelle', defaultImage: null })

    await updateCategory(2, { name: 'Maj', defaultImage: 'https://img' })
    expect(put).toHaveBeenCalledWith('/categories/2', { name: 'Maj', defaultImage: 'https://img' })

    await deleteCategory(2)
    expect(del).toHaveBeenCalledWith('/categories/2')
  })

  it('commentaires : liste par article, création en IRI, suppression', async () => {
    await fetchCommentsByArticle(5)
    expect(get).toHaveBeenCalledWith('/commentaries', {
      params: { article: 5, 'order[datePosted]': 'desc' },
    })

    await createComment({ title: 'T', description: 'D', article: 5 })
    expect(post).toHaveBeenCalledWith('/commentaries', {
      title: 'T',
      description: 'D',
      article: '/api/articles/5',
    })

    await deleteComment(9)
    expect(del).toHaveBeenCalledWith('/commentaries/9')
  })

  it('tags triés par nom', async () => {
    await fetchTags()
    expect(get).toHaveBeenCalledWith('/tags', { params: { 'order[name]': 'asc', itemsPerPage: 100 } })
  })

  it('utilisateurs : liste, détail, rôles en merge-patch, suppression', async () => {
    await fetchUsers()
    expect(get).toHaveBeenCalledWith('/users', { params: { itemsPerPage: 50 } })
    await fetchUser(3)
    expect(get).toHaveBeenCalledWith('/users/3')

    await updateUserRoles(3, ['ROLE_EDITOR'])
    expect(patch).toHaveBeenCalledWith(
      '/users/3',
      { roles: ['ROLE_EDITOR'] },
      { headers: { 'Content-Type': 'application/merge-patch+json' } },
    )

    await deleteUser(3)
    expect(del).toHaveBeenCalledWith('/users/3')
  })

  it('enrichissement OpenGraph', async () => {
    await fetchOpenGraph('https://exemple.test')
    expect(post).toHaveBeenCalledWith('/enrich/opengraph', { url: 'https://exemple.test' })
  })

  it('import : dry-run en query string, JSON brut en corps', async () => {
    await importBonnesPratiques('{"a":1}', true)
    expect(post).toHaveBeenCalledWith('/import/bonnes-pratiques', '{"a":1}', {
      params: { 'dry-run': 1 },
      headers: { 'Content-Type': 'application/json' },
    })

    await importBonnesPratiques('{}', false)
    expect(post).toHaveBeenLastCalledWith('/import/bonnes-pratiques', '{}', {
      params: {},
      headers: { 'Content-Type': 'application/json' },
    })
  })
})
