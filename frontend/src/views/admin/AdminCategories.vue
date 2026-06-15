<script setup lang="ts">
import { onMounted, ref } from 'vue'
import {
  createCategory,
  deleteCategory,
  fetchCategories,
  updateCategory,
  type CategoryInput,
} from '../../api/categories'
import type { Category } from '../../types'

const categories = ref<Category[]>([])
const loading = ref(true)
const error = ref('')

const editingId = ref<number | null>(null)
const form = ref<CategoryInput>({ name: '', defaultImage: '' })
const saving = ref(false)

async function load(): Promise<void> {
  loading.value = true
  try {
    categories.value = (await fetchCategories()).member
  } catch {
    error.value = 'Chargement impossible.'
  } finally {
    loading.value = false
  }
}

function startCreate(): void {
  editingId.value = null
  form.value = { name: '', defaultImage: '' }
}

function startEdit(c: Category): void {
  editingId.value = c.id
  form.value = { name: c.name, defaultImage: c.defaultImage ?? '' }
}

async function submit(): Promise<void> {
  saving.value = true
  error.value = ''
  try {
    if (editingId.value) {
      await updateCategory(editingId.value, form.value)
    } else {
      await createCategory(form.value)
    }
    startCreate()
    await load()
  } catch {
    error.value = 'Enregistrement refusé.'
  } finally {
    saving.value = false
  }
}

async function remove(c: Category): Promise<void> {
  if (!confirm(`Supprimer « ${c.name} » ?`)) return
  try {
    await deleteCategory(c.id)
    await load()
  } catch {
    error.value = 'Suppression refusée (catégorie utilisée ?).'
  }
}

onMounted(load)
</script>

<template>
  <div>
    <h2>Catégories</h2>
    <p v-if="error" class="alert alert-error">{{ error }}</p>

    <form @submit.prevent="submit" class="toolbar">
      <input v-model="form.name" class="input grow" placeholder="Nom de la catégorie" required />
      <input v-model="form.defaultImage" class="input grow" placeholder="Image par défaut (URL)" />
      <button class="btn btn-sm" :disabled="saving">
        {{ editingId ? 'Modifier' : 'Ajouter' }}
      </button>
      <button
        v-if="editingId"
        type="button"
        class="btn btn-ghost btn-sm"
        @click="startCreate"
      >
        Annuler
      </button>
    </form>

    <div v-if="loading" class="state">Chargement…</div>
    <table v-else class="table">
      <thead>
        <tr>
          <th>Nom</th>
          <th>Image</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="c in categories" :key="c.id">
          <td>{{ c.name }}</td>
          <td class="muted">{{ c.defaultImage || '—' }}</td>
          <td class="actions">
            <button class="btn btn-ghost btn-sm" @click="startEdit(c)">Éditer</button>
            <button class="btn btn-danger btn-sm" @click="remove(c)">Suppr.</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
