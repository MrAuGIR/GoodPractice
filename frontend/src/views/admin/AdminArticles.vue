<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { deleteArticle, fetchArticles } from '../../api/articles'
import type { Article } from '../../types'
import { formatDate } from '../../utils/format'
import StateEmpty from '../../components/StateEmpty.vue'
import StateError from '../../components/StateError.vue'

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

    <div v-if="loading" aria-busy="true" style="display: flex; flex-direction: column; gap: 12px">
      <span class="sr-only">Chargement des articles…</span>
      <span v-for="n in 6" :key="n" class="skeleton" style="width: 100%; height: 44px" />
    </div>

    <StateError
      v-else-if="error"
      title="Chargement impossible"
      :message="error"
      tech="GET /api/articles"
      @retry="load"
    />

    <StateEmpty
      v-else-if="articles.length === 0"
      icon="+"
      title="Aucun article pour l'instant"
      message="Plantez la première pratique, ou importez-la depuis une URL — les métadonnées (titre, description, image) seront récupérées automatiquement."
    >
      <template #actions>
        <RouterLink :to="{ name: 'admin-article-new' }" class="btn">+ Nouvel article</RouterLink>
        <RouterLink :to="{ name: 'admin-import' }" class="btn btn-ghost">Importer depuis une URL</RouterLink>
      </template>
    </StateEmpty>

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
