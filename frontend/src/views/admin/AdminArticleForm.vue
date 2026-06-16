<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import {
  createArticle,
  fetchArticle,
  updateArticle,
  type ArticleInput,
} from '../../api/articles'
import { fetchCategories } from '../../api/categories'
import { fetchTags } from '../../api/tags'
import { fetchOpenGraph } from '../../api/enrich'
import type { Category, Tag } from '../../types'

const props = defineProps<{ id?: string }>()
const router = useRouter()
const isEdit = !!props.id

const form = ref<ArticleInput>({
  title: '',
  description: '',
  url: '',
  urlImg: '',
  category: 0,
  featured: false,
  tags: [],
})
const categories = ref<Category[]>([])
const tags = ref<Tag[]>([])
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const enriching = ref(false)
const enrichMsg = ref('')

onMounted(async () => {
  try {
    categories.value = (await fetchCategories()).member
    tags.value = (await fetchTags()).member
    if (isEdit) {
      const a = await fetchArticle(Number(props.id))
      form.value = {
        title: a.title,
        description: a.description,
        url: a.url ?? '',
        urlImg: a.urlImg ?? '',
        category: a.category?.id ?? 0,
        featured: a.featured,
        tags: a.tags?.map((t) => t.id) ?? [],
      }
    } else if (categories.value.length) {
      form.value.category = categories.value[0].id
    }
  } catch {
    error.value = 'Chargement impossible.'
  } finally {
    loading.value = false
  }
})

async function enrich(): Promise<void> {
  if (!form.value.url) return
  enriching.value = true
  enrichMsg.value = ''
  try {
    const og = await fetchOpenGraph(form.value.url)
    const filled: string[] = []
    if (og.image && !form.value.urlImg) {
      form.value.urlImg = og.image
      filled.push('image')
    }
    if (og.title && !form.value.title) {
      form.value.title = og.title
      filled.push('titre')
    }
    if (og.description && !form.value.description) {
      form.value.description = og.description
      filled.push('description')
    }
    enrichMsg.value = filled.length
      ? `Champs pré-remplis : ${filled.join(', ')}.`
      : 'Métadonnées trouvées, mais aucun champ vide à compléter.'
  } catch (e) {
    enrichMsg.value =
      axios.isAxiosError(e) && e.response?.data?.error
        ? (e.response.data.error as string)
        : 'Enrichissement impossible.'
  } finally {
    enriching.value = false
  }
}

function toggleTag(id: number): void {
  const list = form.value.tags ?? (form.value.tags = [])
  const i = list.indexOf(id)
  if (i === -1) list.push(id)
  else list.splice(i, 1)
}

async function submit(): Promise<void> {
  saving.value = true
  error.value = ''
  try {
    if (isEdit) {
      await updateArticle(Number(props.id), form.value)
    } else {
      await createArticle(form.value)
    }
    router.push({ name: 'admin-articles' })
  } catch {
    error.value = "Enregistrement refusé (droits ou champs invalides)."
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <div>
    <div class="toolbar">
      <h2 class="grow" style="margin: 0">{{ isEdit ? 'Éditer' : 'Nouvel' }} article</h2>
      <RouterLink :to="{ name: 'admin-articles' }" class="btn btn-ghost btn-sm">Annuler</RouterLink>
    </div>

    <p v-if="error" class="alert alert-error">{{ error }}</p>
    <div v-if="loading" class="state">Chargement…</div>

    <form v-else @submit.prevent="submit" class="form-card" style="margin: 0; max-width: 640px">
      <div class="field">
        <label>Titre</label>
        <input v-model="form.title" class="input" required />
      </div>
      <div class="field">
        <label>Catégorie</label>
        <select v-model.number="form.category" class="select" required>
          <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
      </div>
      <div class="field">
        <label>Description</label>
        <textarea v-model="form.description" class="textarea" required />
      </div>
      <div class="field">
        <label>Lien externe (optionnel)</label>
        <div class="toolbar" style="gap: 8px">
          <input v-model="form.url" class="input grow" type="url" placeholder="https://…" />
          <button
            type="button"
            class="btn btn-ghost btn-sm"
            :disabled="!form.url || enriching"
            @click="enrich"
          >
            {{ enriching ? 'Récupération…' : 'Récupérer les métadonnées' }}
          </button>
        </div>
        <span v-if="enrichMsg" class="muted" style="font-size: 0.85rem">{{ enrichMsg }}</span>
      </div>
      <div class="field">
        <label>Image (URL, optionnel)</label>
        <input v-model="form.urlImg" class="input" type="url" placeholder="https://…" />
      </div>
      <div v-if="tags.length" class="field">
        <label>Tags</label>
        <div class="theme-pills">
          <button
            v-for="t in tags"
            :key="t.id"
            type="button"
            class="theme-pill"
            :class="{ 'is-active': form.tags?.includes(t.id) }"
            @click="toggleTag(t.id)"
          >
            #{{ t.slug }}
          </button>
        </div>
      </div>
      <div class="field">
        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer">
          <input v-model="form.featured" type="checkbox" />
          Mettre en avant (★ Essentiel)
        </label>
      </div>
      <button class="btn" :disabled="saving">
        {{ saving ? 'Enregistrement…' : 'Enregistrer' }}
      </button>
    </form>
  </div>
</template>
