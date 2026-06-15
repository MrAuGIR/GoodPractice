<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'
import { importBonnesPratiques, type ImportResult } from '../../api/import'

const fileName = ref('')
const jsonContent = ref('')
const dryRun = ref(true)
const loading = ref(false)
const result = ref<ImportResult | null>(null)
const errors = ref<string[]>([])

function onFile(e: Event): void {
  const file = (e.target as HTMLInputElement).files?.[0]
  result.value = null
  errors.value = []
  if (!file) {
    fileName.value = ''
    jsonContent.value = ''
    return
  }
  fileName.value = file.name
  const reader = new FileReader()
  reader.onload = () => {
    jsonContent.value = String(reader.result ?? '')
  }
  reader.readAsText(file)
}

async function submit(): Promise<void> {
  if (!jsonContent.value) return
  loading.value = true
  result.value = null
  errors.value = []
  try {
    result.value = await importBonnesPratiques(jsonContent.value, dryRun.value)
  } catch (e) {
    if (axios.isAxiosError(e) && e.response?.data?.errors) {
      errors.value = e.response.data.errors as string[]
    } else {
      errors.value = ["L'import a échoué (fichier ou droits invalides)."]
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div>
    <h2>Importer des bonnes pratiques</h2>
    <p class="muted">
      Sélectionnez un fichier JSON conforme au format d'import (catégories + articles).
      Cochez « simulation » pour vérifier sans rien écrire en base.
    </p>

    <div class="form-card" style="margin: 0; max-width: 640px">
      <div class="field">
        <label>Fichier JSON</label>
        <input type="file" accept="application/json,.json" class="input" @change="onFile" />
        <span v-if="fileName" class="muted" style="font-size: 0.85rem">{{ fileName }}</span>
      </div>

      <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px">
        <input v-model="dryRun" type="checkbox" />
        <span>Simulation (dry-run) — ne rien écrire</span>
      </label>

      <button class="btn" :disabled="loading || !jsonContent" @click="submit">
        {{ loading ? 'Import…' : dryRun ? 'Simuler' : 'Importer' }}
      </button>
    </div>

    <div v-if="errors.length" class="alert alert-error" style="margin-top: 20px">
      <strong>Import refusé :</strong>
      <ul style="margin: 8px 0 0; padding-left: 20px">
        <li v-for="(err, i) in errors" :key="i">{{ err }}</li>
      </ul>
    </div>

    <div v-if="result" class="alert alert-info" style="margin-top: 20px">
      <strong>{{ result.dryRun ? 'Simulation' : 'Import' }} terminé{{ result.dryRun ? 'e' : '' }}.</strong>
      <table class="table" style="margin-top: 12px; background: transparent">
        <tbody>
          <tr><td>Catégories créées</td><td>{{ result.categoriesCreated }}</td></tr>
          <tr><td>Articles créés</td><td>{{ result.articlesCreated }}</td></tr>
          <tr><td>Articles mis à jour</td><td>{{ result.articlesUpdated }}</td></tr>
          <tr><td>Articles ignorés (doublons)</td><td>{{ result.articlesSkipped }}</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
