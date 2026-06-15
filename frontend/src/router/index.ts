import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'home',
    component: () => import('../views/HomeView.vue'),
  },
  {
    path: '/articles/:id',
    name: 'article',
    component: () => import('../views/ArticleView.vue'),
    props: true,
  },
  {
    path: '/categories',
    name: 'categories',
    component: () => import('../views/CategoriesView.vue'),
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/LoginView.vue'),
    meta: { guestOnly: true },
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('../views/RegisterView.vue'),
    meta: { guestOnly: true },
  },
  {
    path: '/admin',
    component: () => import('../views/admin/AdminLayout.vue'),
    meta: { requiresEditor: true },
    children: [
      { path: '', redirect: { name: 'admin-articles' } },
      {
        path: 'articles',
        name: 'admin-articles',
        component: () => import('../views/admin/AdminArticles.vue'),
      },
      {
        path: 'articles/new',
        name: 'admin-article-new',
        component: () => import('../views/admin/AdminArticleForm.vue'),
      },
      {
        path: 'articles/:id/edit',
        name: 'admin-article-edit',
        component: () => import('../views/admin/AdminArticleForm.vue'),
        props: true,
      },
      {
        path: 'categories',
        name: 'admin-categories',
        component: () => import('../views/admin/AdminCategories.vue'),
        meta: { requiresAdmin: true },
      },
      {
        path: 'users',
        name: 'admin-users',
        component: () => import('../views/admin/AdminUsers.vue'),
        meta: { requiresAdmin: true },
      },
      {
        path: 'import',
        name: 'admin-import',
        component: () => import('../views/admin/AdminImport.vue'),
        meta: { requiresAdmin: true },
      },
    ],
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('../views/NotFoundView.vue'),
  },
]

export const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  },
})

router.beforeEach((to) => {
  const auth = useAuthStore()

  if (to.meta.guestOnly && auth.isAuthenticated) {
    return { name: 'home' }
  }
  if (to.matched.some((r) => r.meta.requiresEditor) && !auth.isEditor) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }
  if (to.matched.some((r) => r.meta.requiresAdmin) && !auth.isAdmin) {
    return { name: 'admin-articles' }
  }
  return true
})
