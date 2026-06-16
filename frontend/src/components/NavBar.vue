<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useTheme } from '../composables/useTheme'

const auth = useAuthStore()
const router = useRouter()
const { theme, toggle } = useTheme()

function logout(): void {
  auth.logout()
  router.push({ name: 'home' })
}
</script>

<template>
  <header class="navbar">
    <div class="container">
      <RouterLink to="/" class="brand">
        <span class="brand-mark" aria-hidden="true"><span class="brand-diamond" /></span>
        goodPractice
      </RouterLink>
      <nav>
        <RouterLink to="/">Explorer</RouterLink>
        <RouterLink to="/categories">Catégories</RouterLink>
        <RouterLink v-if="auth.isEditor" to="/admin">Admin</RouterLink>
      </nav>
      <span class="spacer" />
      <button
        type="button"
        class="theme-toggle"
        :aria-label="theme === 'dark' ? 'Passer en thème clair' : 'Passer en thème sombre'"
        @click="toggle"
      >
        <span class="dot" />
        {{ theme === 'dark' ? 'Sombre' : 'Clair' }}
      </button>
      <template v-if="auth.isAuthenticated">
        <span class="user">{{ auth.username }}</span>
        <button class="btn btn-ghost btn-sm" @click="logout">Déconnexion</button>
      </template>
      <template v-else>
        <RouterLink to="/login" class="btn btn-ghost btn-sm">Connexion</RouterLink>
        <RouterLink to="/register" class="btn btn-sm">Inscription</RouterLink>
      </template>
    </div>
  </header>
</template>
