export interface UserRef {
  id: number
  name: string
}

export interface CategoryRef {
  id: number
  name: string
}

export interface Tag {
  '@id'?: string
  id: number
  name: string
  slug: string
}

export interface Article {
  '@id'?: string
  id: number
  title: string
  description: string
  url: string | null
  urlImg: string | null
  dateCreate: string
  featured: boolean
  author: UserRef
  category: CategoryRef
  tags: Tag[]
  nbComments: number
  comments?: Commentary[]
}

export interface Category {
  '@id'?: string
  id: number
  name: string
  defaultImage: string | null
}

export interface Commentary {
  '@id'?: string
  id: number
  title: string
  description: string
  datePosted: string
  approved: number
  disapproved: number
  author?: UserRef
}

export interface User {
  '@id'?: string
  id: number
  email: string
  name: string
  roles: string[]
}

/** Réponse d'une collection API Platform (Hydra / JSON-LD). */
export interface ApiCollection<T> {
  member: T[]
  totalItems: number
}

export interface TokenPair {
  token: string
  refresh_token: string
}
