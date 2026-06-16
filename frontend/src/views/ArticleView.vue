<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue'
import { fetchArticle, fetchArticles } from '../api/articles'
import { createComment, deleteComment } from '../api/comments'
import type { Article } from '../types'
import { useAuthStore } from '../stores/auth'
import { formatDate, readingTime } from '../utils/format'
import ArticleDetailSkeleton from '../components/ArticleDetailSkeleton.vue'
import StateError from '../components/StateError.vue'

const props = defineProps<{ id: string }>()
const auth = useAuthStore()

const article = ref<Article | null>(null)
const related = ref<Article[]>([])
const loading = ref(true)
const error = ref('')
const progress = ref(0)

const commentTitle = ref('')
const commentBody = ref('')
const submitting = ref(false)
const commentError = ref('')

async function loadRelated(): Promise<void> {
  if (!article.value?.category?.id) return
  try {
    const data = await fetchArticles({
      itemsPerPage: 4,
      category: article.value.category.id,
      order: { dateCreate: 'desc' },
    })
    related.value = data.member.filter((a) => a.id !== article.value?.id).slice(0, 3)
  } catch {
    /* section non bloquante */
  }
}

async function load(): Promise<void> {
  loading.value = true
  error.value = ''
  related.value = []
  try {
    article.value = await fetchArticle(Number(props.id))
    await loadRelated()
  } catch {
    error.value = 'Cette pratique est introuvable. Le lien est peut-être périmé.'
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

function onScroll(): void {
  const h = document.documentElement
  const max = h.scrollHeight - h.clientHeight
  progress.value = max > 0 ? Math.min(100, (h.scrollTop / max) * 100) : 0
}

onMounted(() => {
  load()
  window.addEventListener('scroll', onScroll, { passive: true })
})
onBeforeUnmount(() => window.removeEventListener('scroll', onScroll))
</script>

<template>
  <div class="read-progress"><div class="read-progress__bar" :style="{ width: progress + '%' }" /></div>

  <main class="page container">
    <ArticleDetailSkeleton v-if="loading" />

    <StateError
      v-else-if="error"
      title="Pratique introuvable"
      :message="error"
      tech="GET /api/articles/:id"
      @retry="load"
    >
      <template #actions>
        <RouterLink to="/" class="btn btn-ghost">Retour à l'exploration</RouterLink>
      </template>
    </StateError>

    <template v-else-if="article">
      <article class="reading-column">
        <RouterLink to="/" class="crumb">‹ {{ article.category?.name }} / Explorer</RouterLink>

        <h1>{{ article.title }}</h1>

        <div class="article-meta">
          <span>Par {{ article.author?.name }}</span>
          <span class="sep">·</span>
          <span>{{ formatDate(article.dateCreate) }}</span>
          <span class="sep">·</span>
          <span>{{ readingTime(article.description) }} min</span>
          <span v-if="article.category" class="chip">{{ article.category.name }}</span>
        </div>

        <div style="height: 1px; background: var(--hair); margin: 24px 0" />

        <div class="article-body">{{ article.description }}</div>

        <p v-if="article.url" style="margin-top: 22px">
          <a :href="article.url" target="_blank" rel="noopener" class="source-link">
            ↗ Source de référence
          </a>
        </p>
      </article>

      <!-- À explorer ensuite -->
      <section v-if="related.length" class="reading-column" style="max-width: 980px; padding-top: 36px">
        <div class="section-label">À explorer ensuite</div>
        <div class="grid">
          <RouterLink
            v-for="a in related"
            :key="a.id"
            :to="{ name: 'article', params: { id: a.id } }"
            class="practice-card"
          >
            <div class="practice-card__meta">
              <span class="practice-card__cat"><span class="diamond" />{{ a.category?.name }}</span>
              <span style="margin-left: auto">{{ readingTime(a.description) }} min</span>
            </div>
            <h4>{{ a.title }}</h4>
          </RouterLink>
        </div>
      </section>

      <!-- Commentaires -->
      <section class="reading-column" style="padding-top: 36px">
        <div class="section-label">Commentaires ({{ article.comments?.length ?? 0 }})</div>

        <p v-if="!article.comments?.length" class="muted">Aucun commentaire pour l'instant.</p>
        <div v-else style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px">
          <div v-for="c in article.comments" :key="c.id" class="card" style="padding: 16px">
            <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 12px">
              <strong>{{ c.title }}</strong>
              <button v-if="auth.isAdmin" class="btn btn-danger btn-sm" @click="removeComment(c.id)">
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
          <p v-if="commentError" class="alert alert-error" role="alert">{{ commentError }}</p>
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
      </section>
    </template>
  </main>
</template>
