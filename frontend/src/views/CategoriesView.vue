<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { fetchCategories } from '../api/categories'
import type { Category } from '../types'

const categories = ref<Category[]>([])
const loading = ref(true)
const error = ref('')

onMounted(async () => {
  try {
    categories.value = (await fetchCategories()).member
  } catch {
    error.value = 'Impossible de charger les catégories.'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <main class="page container">
    <div class="page-header"><h1>Catégories</h1></div>

    <p v-if="error" class="alert alert-error">{{ error }}</p>
    <div v-if="loading" class="state">Chargement…</div>
    <div v-else-if="categories.length === 0" class="state">Aucune catégorie.</div>

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
