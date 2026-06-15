<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { fetchArticle } from '../api/articles'
import { createComment, deleteComment } from '../api/comments'
import type { Article } from '../types'
import { useAuthStore } from '../stores/auth'
import { articleImage, formatDate } from '../utils/format'

const props = defineProps<{ id: string }>()
const auth = useAuthStore()

const article = ref<Article | null>(null)
const loading = ref(true)
const error = ref('')

const commentTitle = ref('')
const commentBody = ref('')
const submitting = ref(false)
const commentError = ref('')

async function load(): Promise<void> {
  loading.value = true
  error.value = ''
  try {
    article.value = await fetchArticle(Number(props.id))
  } catch {
    error.value = "Article introuvable."
  } finally {
    loading.value = false
  }
}

async function submitComment(): Promise<void> {
  if (!article.value) return
  submitting.value = true
  commentError.value = ''
  try {
    await createComment({
      title: commentTitle.value,
      description: commentBody.value,
      article: article.value.id,
    })
    commentTitle.value = ''
    commentBody.value = ''
    await load()
  } catch {
    commentError.value = "Impossible d'envoyer le commentaire."
  } finally {
    submitting.value = false
  }
}

async function removeComment(commentId: number): Promise<void> {
  if (!confirm('Supprimer ce commentaire ?')) return
  await deleteComment(commentId)
  await load()
}

onMounted(load)
</script>

<template>
  <main class="page container">
    <div v-if="loading" class="state">Chargement…</div>
    <p v-else-if="error" class="alert alert-error">{{ error }}</p>

    <template v-else-if="article">
      <RouterLink to="/" class="muted">← Retour aux articles</RouterLink>
      <div class="page-header" style="margin-top: 16px">
        <h1>{{ article.title }}</h1>
      </div>
      <div class="meta muted" style="display: flex; gap: 12px; margin-bottom: 20px">
        <span class="badge">{{ article.category?.name }}</span>
        <span>{{ article.author?.name }}</span>
        <span>{{ formatDate(article.dateCreate) }}</span>
      </div>

      <img
        :src="articleImage(article.urlImg)"
        :alt="article.title"
        style="width: 100%; max-height: 380px; object-fit: cover; border-radius: var(--radius)"
      />

      <p style="margin-top: 24px; white-space: pre-line">{{ article.description }}</p>
      <p v-if="article.url">
        <a :href="article.url" target="_blank" rel="noopener">Source / lien externe ↗</a>
      </p>

      <hr style="border: none; border-top: 1px solid var(--border); margin: 32px 0" />

      <h2>Commentaires ({{ article.comments?.length ?? 0 }})</h2>

      <div v-if="!article.comments?.length" class="muted">Aucun commentaire pour l'instant.</div>
      <div v-else style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px">
        <div
          v-for="c in article.comments"
          :key="c.id"
          class="card"
          style="padding: 16px"
        >
          <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 12px">
            <strong>{{ c.title }}</strong>
            <button
              v-if="auth.isAdmin"
              class="btn btn-danger btn-sm"
              @click="removeComment(c.id)"
            >
              Supprimer
            </button>
          </div>
          <p style="margin: 8px 0 6px">{{ c.description }}</p>
          <span class="muted" style="font-size: 0.82rem">
            {{ c.author?.name }} · {{ formatDate(c.datePosted) }}
          </span>
        </div>
      </div>

      <div v-if="auth.isAuthenticated" class="form-card" style="margin: 0">
        <h3>Ajouter un commentaire</h3>
        <p v-if="commentError" class="alert alert-error">{{ commentError }}</p>
        <form @submit.prevent="submitComment">
          <div class="field">
            <label>Titre</label>
            <input v-model="commentTitle" class="input" required />
          </div>
          <div class="field">
            <label>Commentaire</label>
            <textarea v-model="commentBody" class="textarea" required />
          </div>
          <button class="btn" :disabled="submitting">
            {{ submitting ? 'Envoi…' : 'Publier' }}
          </button>
        </form>
      </div>
      <p v-else class="alert alert-info">
        <RouterLink to="/login">Connectez-vous</RouterLink> pour laisser un commentaire.
      </p>
    </template>
  </main>
</template>
