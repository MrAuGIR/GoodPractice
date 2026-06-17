import { describe, it, expect, beforeEach, vi } from 'vitest'

// Mock du client HTTP : on intercepte les appels pour inspecter le payload
// construit par toPayload (fonction interne, testée via createArticle/updateArticle).
const { post, put } = vi.hoisted(() => ({ post: vi.fn(), put: vi.fn() }))

vi.mock('../http', () => ({
  http: { post, put },
  iri: (resource: string, id: number) => `/api/${resource}/${id}`,
}))

import { createArticle, updateArticle } from '../articles'

describe('articles payload', () => {
  beforeEach(() => {
    post.mockReset().mockResolvedValue({ data: {} })
    put.mockReset().mockResolvedValue({ data: {} })
  })

  it('createArticle envoie catégorie et tags en IRI + featured', async () => {
    await createArticle({
      title: 'Titre',
      description: 'Desc',
      category: 3,
      tags: [1, 2],
      featured: true,
    })

    expect(post).toHaveBeenCalledWith(
      '/articles',
      expect.objectContaining({
        title: 'Titre',
        category: '/api/categories/3',
        tags: ['/api/tags/1', '/api/tags/2'],
        featured: true,
      }),
    )
  })

  it('updateArticle normalise les champs vides et tags par défaut', async () => {
    await updateArticle(7, { title: 'T', description: 'D', category: 1 })

    expect(put).toHaveBeenCalledWith(
      '/articles/7',
      expect.objectContaining({
        url: null,
        urlImg: null,
        featured: false,
        tags: [],
      }),
    )
  })
})
