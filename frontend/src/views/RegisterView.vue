<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()

const email = ref('')
const name = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

async function submit(): Promise<void> {
  loading.value = true
  error.value = ''
  try {
    await auth.register(email.value, name.value, password.value)
    await auth.login(email.value, password.value)
    router.push('/')
  } catch {
    error.value = "L'inscription a échoué (email déjà utilisé ?)."
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <main class="page container">
    <div class="form-card">
      <h1>Inscription</h1>
      <p v-if="error" class="alert alert-error">{{ error }}</p>
      <form @submit.prevent="submit">
        <div class="field">
          <label>Nom</label>
          <input v-model="name" class="input" required />
        </div>
        <div class="field">
          <label>Email</label>
          <input v-model="email" type="email" class="input" required autocomplete="email" />
        </div>
        <div class="field">
          <label>Mot de passe</label>
          <input
            v-model="password"
            type="password"
            class="input"
            required
            minlength="6"
            autocomplete="new-password"
          />
        </div>
        <button class="btn" style="width: 100%" :disabled="loading">
          {{ loading ? 'Création…' : 'Créer mon compte' }}
        </button>
      </form>
      <p class="muted center" style="margin: 16px 0 0">
        Déjà inscrit ? <RouterLink to="/login">Connexion</RouterLink>
      </p>
    </div>
  </main>
</template>
