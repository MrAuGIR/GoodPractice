import { http, iri } from './http'
import type { ApiCollection, Commentary } from '../types'

export interface CommentInput {
  title: string
  description: string
  article: number
}

export async function fetchCommentsByArticle(articleId: number): Promise<ApiCollection<Commentary>> {
  const { data } = await http.get<ApiCollection<Commentary>>('/commentaries', {
    params: { article: articleId, 'order[datePosted]': 'desc' },
  })
  return data
}

export async function createComment(input: CommentInput): Promise<Commentary> {
  const { data } = await http.post<Commentary>('/commentaries', {
    title: input.title,
    description: input.description,
    article: iri('articles', input.article),
  })
  return data
}

export async function deleteComment(id: number): Promise<void> {
  await http.delete(`/commentaries/${id}`)
}
