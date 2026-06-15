<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { fetchArticles } from '../api/articles'
import { fetchCategories } from '../api/categories'
import type { Article, Category } from '../types'
import { articleImage, formatDate } from '../utils/format'

const route = useRoute()
const articles = ref<Article[]>([])
const categories = ref<Category[]>([])
const total = ref(0)
const page = ref(1)
const itemsPerPage = 9
const search = ref('')
const initialCategory = Number(route.query.category)
const categoryId = ref<number | null>(initialCategory || null)
const loading = ref(false)
const error = ref('')

let searchTimer: ReturnType<typeof setTimeout> | undefined

async function load(): Promise<void> {
  loading.value = true
  error.value = ''
  try {
    const data = await fetchArticles({
      page: page.value,
      itemsPerPage,
      title: search.value || undefined,
      category: categoryId.value ?? undefined,
      order: { dateCreate: 'desc' },
    })
    articles.value = data.member
    total.value = data.totalItems
  } catch {
    error.value = 'Impossible de charger les articles.'
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await load()
  try {
    categories.value = (await fetchCategories()).member
  } catch {
    /* non bloquant */
  }
})

watch([categoryId, page], load)
watch(search, () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    page.value = 1
    load()
  }, 350)
})

const totalPages = () => Math.max(1, Math.ceil(total.value / itemsPerPage))
</script>

<template>
  <main class="page container">
    <div class="page-header">
      <h1>Articles</h1>
    </div>

    <div class="toolbar">
      <input v-model="search" class="input grow" placeholder="Rechercher un article…" />
      <select v-model="categoryId" class="select" style="width: auto">
        <option :value="null">Toutes les catégories</option>
        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
    </div>

    <p v-if="error" class="alert alert-error">{{ error }}</p>

    <div v-if="loading" class="state">Chargement…</div>
    <div v-else-if="articles.length === 0" class="state">Aucun article trouvé.</div>

    <div v-else class="grid">
      <RouterLink
        v-for="a in articles"
        :key="a.id"
        :to="{ name: 'article', params: { id: a.id } }"
        class="card"
        style="color: inherit"
      >
        <img class="thumb" :src="articleImage(a.urlImg)" :alt="a.title" />
        <div class="body">
          <span class="badge">{{ a.category?.name }}</span>
          <h3>{{ a.title }}</h3>
          <div class="meta">
            <span>{{ a.author?.name }}</span>
            <span>{{ formatDate(a.dateCreate) }}</span>
            <span>{{ a.nbComments }} commentaire(s)</span>
          </div>
        </div>
      </RouterLink>
    </div>

    <div v-if="totalPages() > 1" class="pagination">
      <button class="btn btn-ghost btn-sm" :disabled="page <= 1" @click="page--">Précédent</button>
      <span class="muted">Page {{ page }} / {{ totalPages() }}</span>
      <button class="btn btn-ghost btn-sm" :disabled="page >= totalPages()" @click="page++">
        Suivant
      </button>
    </div>
  </main>
</template>
