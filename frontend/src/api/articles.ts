import { http, iri } from './http'
import type { ApiCollection, Article } from '../types'

export interface ArticleQuery {
  page?: number
  itemsPerPage?: number
  title?: string
  category?: number
  order?: Record<string, 'asc' | 'desc'>
}

export interface ArticleInput {
  title: string
  description: string
  url?: string | null
  urlImg?: string | null
  category: number
}

export async function fetchArticles(q: ArticleQuery = {}): Promise<ApiCollection<Article>> {
  const params: Record<string, unknown> = {}
  if (q.page) params.page = q.page
  if (q.itemsPerPage) params.itemsPerPage = q.itemsPerPage
  if (q.title) params.title = q.title
  if (q.category) params.category = q.category
  if (q.order) {
    for (const [key, dir] of Object.entries(q.order)) {
      params[`order[${key}]`] = dir
    }
  }
  const { data } = await http.get<ApiCollection<Article>>('/articles', { params })
  return data
}

export async function fetchArticle(id: number): Promise<Article> {
  const { data } = await http.get<Article>(`/articles/${id}`)
  return data
}

function toPayload(input: ArticleInput) {
  return {
    title: input.title,
    description: input.description,
    url: input.url || null,
    urlImg: input.urlImg || null,
    category: iri('categories', input.category),
  }
}

export async function createArticle(input: ArticleInput): Promise<Article> {
  const { data } = await http.post<Article>('/articles', toPayload(input))
  return data
}

export async function updateArticle(id: number, input: ArticleInput): Promise<Article> {
  const { data } = await http.put<Article>(`/articles/${id}`, toPayload(input))
  return data
}

export async function deleteArticle(id: number): Promise<void> {
  await http.delete(`/articles/${id}`)
}
