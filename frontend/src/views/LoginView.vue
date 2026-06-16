<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

// Erreurs de validation par champ (affichées après une tentative ou au blur).
const fieldErrors = reactive<{ email: string; password: string }>({ email: '', password: '' })

function validate(): boolean {
  fieldErrors.email = !email.value.trim()
    ? 'Adresse e-mail requise.'
    : !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email.value)
      ? 'Adresse e-mail incomplète.'
      : ''
  fieldErrors.password = !password.value
    ? 'Mot de passe requis.'
    : password.value.length < 8
      ? 'Au moins 8 caractères.'
      : ''
  return !fieldErrors.email && !fieldErrors.password
}

async function submit(): Promise<void> {
  error.value = ''
  if (!validate()) return
  loading.value = true
  try {
    await auth.login(email.value, password.value)
    const redirect = (route.query.redirect as string) || '/'
    router.push(redirect)
  } catch {
    error.value = 'Identifiants incorrects. Vérifiez votre e-mail et votre mot de passe.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <main class="page container">
    <div class="form-card">
      <h1>Se connecter</h1>

      <p v-if="error" class="alert alert-error" role="alert">{{ error }}</p>

      <form novalidate @submit.prevent="submit">
        <div class="field">
          <label for="login-email">Adresse e-mail</label>
          <input
            id="login-email"
            v-model="email"
            type="email"
            class="input"
            :class="{ 'is-invalid': fieldErrors.email }"
            :aria-invalid="!!fieldErrors.email"
            aria-describedby="login-email-err"
            autocomplete="email"
            @blur="validate"
          />
          <span v-if="fieldErrors.email" id="login-email-err" class="field-error">
            <span aria-hidden="true">!</span> {{ fieldErrors.email }}
          </span>
        </div>

        <div class="field">
          <label for="login-password">Mot de passe</label>
          <input
            id="login-password"
            v-model="password"
            type="password"
            class="input"
            :class="{ 'is-invalid': fieldErrors.password }"
            :aria-invalid="!!fieldErrors.password"
            aria-describedby="login-password-err"
            autocomplete="current-password"
            @blur="validate"
          />
          <span v-if="fieldErrors.password" id="login-password-err" class="field-error">
            <span aria-hidden="true">!</span> {{ fieldErrors.password }}
          </span>
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
