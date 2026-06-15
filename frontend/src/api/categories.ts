import { http } from './http'
import type { ApiCollection, Category } from '../types'

export interface CategoryInput {
  name: string
  defaultImage?: string | null
}

export async function fetchCategories(): Promise<ApiCollection<Category>> {
  const { data } = await http.get<ApiCollection<Category>>('/categories', {
    params: { 'order[name]': 'asc', itemsPerPage: 50 },
  })
  return data
}

export async function fetchCategory(id: number): Promise<Category> {
  const { data } = await http.get<Category>(`/categories/${id}`)
  return data
}

function toPayload(input: CategoryInput) {
  return {
    name: input.name,
    defaultImage: input.defaultImage || null,
  }
}

export async function createCategory(input: CategoryInput): Promise<Category> {
  const { data } = await http.post<Category>('/categories', toPayload(input))
  return data
}

export async function updateCategory(id: number, input: CategoryInput): Promise<Category> {
  const { data } = await http.put<Category>(`/categories/${id}`, toPayload(input))
  return data
}

export async function deleteCategory(id: number): Promise<void> {
  await http.delete(`/categories/${id}`)
}
