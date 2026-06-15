<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import {
  createArticle,
  fetchArticle,
  updateArticle,
  type ArticleInput,
} from '../../api/articles'
import { fetchCategories } from '../../api/categories'
import type { Category } from '../../types'

const props = defineProps<{ id?: string }>()
const router = useRouter()
const isEdit = !!props.id

const form = ref<ArticleInput>({
  title: '',
  description: '',
  url: '',
  urlImg: '',
  category: 0,
})
const categories = ref<Category[]>([])
const loading = ref(true)
const saving = ref(false)
const error = ref('')

onMounted(async () => {
  try {
    categories.value = (await fetchCategories()).member
    if (isEdit) {
      const a = await fetchArticle(Number(props.id))
      form.value = {
        title: a.title,
        description: a.description,
        url: a.url ?? '',
        urlImg: a.urlImg ?? '',
        category: a.category?.id ?? 0,
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
        <input v-model="form.url" class="input" type="url" placeholder="https://…" />
      </div>
      <div class="field">
        <label>Image (URL, optionnel)</label>
        <input v-model="form.urlImg" class="input" type="url" placeholder="https://…" />
      </div>
      <button class="btn" :disabled="saving">
        {{ saving ? 'Enregistrement…' : 'Enregistrer' }}
      </button>
    </form>
  </div>
</template>
