import { http } from './http'
import type { ApiCollection, Tag } from '../types'

export async function fetchTags(): Promise<ApiCollection<Tag>> {
  const { data } = await http.get<ApiCollection<Tag>>('/tags', {
    params: { 'order[name]': 'asc', itemsPerPage: 100 },
  })
  return data
}
