<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { deleteArticle, fetchArticles } from '../../api/articles'
import type { Article } from '../../types'
import { formatDate } from '../../utils/format'

const articles = ref<Article[]>([])
const loading = ref(true)
const error = ref('')

async function load(): Promise<void> {
  loading.value = true
  error.value = ''
  try {
    articles.value = (await fetchArticles({ itemsPerPage: 50, order: { dateCreate: 'desc' } })).member
  } catch {
    error.value = 'Chargement impossible.'
  } finally {
    loading.value = false
  }
}

async function remove(a: Article): Promise<void> {
  if (!confirm(`Supprimer « ${a.title} » ?`)) return
  try {
    await deleteArticle(a.id)
    await load()
  } catch {
    error.value = 'Suppression refusée.'
  }
}

onMounted(load)
</script>

<template>
  <div>
    <div class="toolbar">
      <h2 class="grow" style="margin: 0">Articles</h2>
      <RouterLink :to="{ name: 'admin-article-new' }" class="btn btn-sm">+ Nouvel article</RouterLink>
    </div>

    <p v-if="error" class="alert alert-error">{{ error }}</p>
    <div v-if="loading" class="state">Chargement…</div>

    <table v-else class="table">
      <thead>
        <tr>
          <th>Titre</th>
          <th>Catégorie</th>
          <th>Auteur</th>
          <th>Date</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="a in articles" :key="a.id">
          <td>{{ a.title }}</td>
          <td><span class="badge badge-muted">{{ a.category?.name }}</span></td>
          <td>{{ a.author?.name }}</td>
          <td class="muted">{{ formatDate(a.dateCreate) }}</td>
          <td class="actions">
            <RouterLink
              :to="{ name: 'admin-article-edit', params: { id: a.id } }"
              class="btn btn-ghost btn-sm"
            >
              Éditer
            </RouterLink>
            <button class="btn btn-danger btn-sm" @click="remove(a)">Suppr.</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
