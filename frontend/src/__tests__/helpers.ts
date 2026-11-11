import { flushPromises, mount } from '@vue/test-utils'
import { createPinia, setActivePinia, type Pinia } from 'pinia'
import type { Component } from 'vue'
import { createMemoryHistory, createRouter, type Router } from 'vue-router'
import type { ApiCollection, Article, Category, Commentary, User } from '../types'

const Dummy = { template: '<div />' }

/** Routeur de test : mêmes noms de routes que l'application, composants factices. */
export function makeRouter(): Router {
  const names = [
    ['/', 'home'],
    ['/articles/:id', 'article'],
    ['/categories', 'categories'],
    ['/login', 'login'],
    ['/register', 'register'],
    ['/admin/articles', 'admin-articles'],
    ['/admin/articles/new', 'admin-article-new'],
    ['/admin/articles/:id/edit', 'admin-article-edit'],
    ['/admin/categories', 'admin-categories'],
    ['/admin/users', 'admin-users'],
    ['/admin/import', 'admin-import'],
  ] as const
  return createRouter({
    history: createMemoryHistory(),
    routes: [
      ...names.map(([path, name]) => ({ path, name, component: Dummy })),
      { path: '/admin', component: Dummy },
      { path: '/:pathMatch(.*)*', name: 'not-found', component: Dummy },
    ],
  })
}

/** Monte un composant avec Pinia + routeur de test, puis attend les promesses. */
export async function mountView(
  component: Component,
  options: { props?: Record<string, unknown>; route?: string; pinia?: Pinia } = {},
) {
  const pinia = options.pinia ?? createPinia()
  setActivePinia(pinia)
  const router = makeRouter()
  await router.push(options.route ?? '/')
  await router.isReady()
  const wrapper = mount(component, {
    props: options.props,
    global: { plugins: [pinia, router] },
  })
  await flushPromises()
  return { wrapper, router }
}

/** Faux JWT (payload lisible par le store auth). */
export function fakeJwt(payload: { username: string; roles: string[]; exp?: number }): string {
  const encode = (o: object) => btoa(JSON.stringify(o)).replace(/=+$/, '')
  return `${encode({ alg: 'none' })}.${encode({ exp: 9999999999, ...payload })}.sig`
}

/** Connecte un utilisateur fictif (avant création du store). */
export function loginAs(roles: string[], username = 'tester@exemple.test'): void {
  localStorage.setItem('gp_token', fakeJwt({ username, roles }))
  localStorage.setItem('gp_refresh', 'refresh')
}

export function collection<T>(member: T[]): ApiCollection<T> {
  return { member, totalItems: member.length }
}

export function makeCategory(over: Partial<Category> = {}): Category {
  return { id: 1, name: 'Sécurité', defaultImage: null, ...over }
}

export function makeComment(over: Partial<Commentary> = {}): Commentary {
  return {
    id: 1,
    title: 'Super',
    description: 'Très utile',
    datePosted: '2024-03-01T10:00:00+00:00',
    approved: 0,
    disapproved: 0,
    author: { id: 1, name: 'Jane' },
    ...over,
  }
}

export function makeArticle(over: Partial<Article> = {}): Article {
  return {
    id: 1,
    title: 'Valider les entrées',
    description: 'Toujours valider les données côté serveur.',
    url: 'https://exemple.test/article',
    urlImg: null,
    dateCreate: '2024-03-01T10:00:00+00:00',
    featured: false,
    author: { id: 1, name: 'Jane' },
    category: { id: 1, name: 'Sécurité' },
    tags: [{ id: 1, name: 'PHP', slug: 'php' }],
    nbComments: 0,
    comments: [],
    ...over,
  }
}

export function makeUser(over: Partial<User> = {}): User {
  return { id: 1, email: 'jane@exemple.test', name: 'Jane', roles: ['ROLE_USER'], ...over }
}

/** Trouve un bouton par son libellé (échoue s'il est absent). */
export function button(wrapper: { findAll: (s: string) => Array<{ text: () => string }> }, label: string) {
  const found = wrapper.findAll('button').find((b) => b.text().includes(label))
  if (!found) throw new Error(`Bouton « ${label} » introuvable`)
  return found as unknown as import('@vue/test-utils').DOMWrapper<HTMLButtonElement>
}
