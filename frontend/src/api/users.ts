import { http } from './http'
import type { ApiCollection, User } from '../types'

export async function fetchUsers(): Promise<ApiCollection<User>> {
  const { data } = await http.get<ApiCollection<User>>('/users', {
    params: { itemsPerPage: 50 },
  })
  return data
}

export async function fetchUser(id: number): Promise<User> {
  const { data } = await http.get<User>(`/users/${id}`)
  return data
}

/** Édition des rôles (admin) via PATCH (merge-patch). */
export async function updateUserRoles(id: number, roles: string[]): Promise<User> {
  const { data } = await http.patch<User>(
    `/users/${id}`,
    { roles },
    { headers: { 'Content-Type': 'application/merge-patch+json' } },
  )
  return data
}

export async function deleteUser(id: number): Promise<void> {
  await http.delete(`/users/${id}`)
}
