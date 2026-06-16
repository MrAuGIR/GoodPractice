<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { fetchCategories } from '../api/categories'
import type { Category } from '../types'
import ArticleGridSkeleton from '../components/ArticleGridSkeleton.vue'
import StateEmpty from '../components/StateEmpty.vue'
import StateError from '../components/StateError.vue'

const categories = ref<Category[]>([])
const loading = ref(true)
const error = ref('')

async function load(): Promise<void> {
  loading.value = true
  error.value = ''
  try {
    categories.value = (await fetchCategories()).member
  } catch {
    error.value = "Le serveur n'a pas répondu. Réessayez dans un instant."
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

<template>
  <main class="page container">
    <div class="page-header"><h1>Catégories</h1></div>

    <ArticleGridSkeleton v-if="loading" :count="3" />

    <StateError
      v-else-if="error"
      title="Impossible de charger les catégories"
      :message="error"
      tech="GET /api/categories"
      @retry="load"
    />

    <StateEmpty
      v-else-if="categories.length === 0"
      title="Aucune catégorie pour l'instant"
      message="Les catégories apparaîtront ici dès qu'un article en utilisera une."
    />

    <div v-else class="grid">
      <RouterLink
        v-for="c in categories"
        :key="c.id"
        :to="{ name: 'home', query: { category: c.id } }"
        class="card"
        style="color: inherit"
      >
        <img
          v-if="c.defaultImage"
          class="thumb"
          :src="c.defaultImage"
          :alt="c.name"
        />
        <div class="body">
          <h3>{{ c.name }}</h3>
        </div>
      </RouterLink>
    </div>
  </main>
</template>
