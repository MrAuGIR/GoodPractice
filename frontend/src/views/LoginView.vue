<script setup lang="ts">
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

async function submit(): Promise<void> {
  loading.value = true
  error.value = ''
  try {
    await auth.login(email.value, password.value)
    const redirect = (route.query.redirect as string) || '/'
    router.push(redirect)
  } catch {
    error.value = 'Identifiants invalides.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <main class="page container">
    <div class="form-card">
      <h1>Connexion</h1>
      <p v-if="error" class="alert alert-error">{{ error }}</p>
      <form @submit.prevent="submit">
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
            autocomplete="current-password"
          />
        </div>
        <button class="btn" style="width: 100%" :disabled="loading">
          {{ loading ? 'Connexion…' : 'Se connecter' }}
        </button>
      </form>
      <p class="muted center" style="margin: 16px 0 0">
        Pas de compte ? <RouterLink to="/register">Inscription</RouterLink>
      </p>
    </div>
  </main>
</template>
