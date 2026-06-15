<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()

function logout(): void {
  auth.logout()
  router.push({ name: 'home' })
}
</script>

<template>
  <header class="navbar">
    <div class="container">
      <RouterLink to="/" class="brand">goodPractice</RouterLink>
      <nav>
        <RouterLink to="/">Articles</RouterLink>
        <RouterLink to="/categories">Catégories</RouterLink>
        <RouterLink v-if="auth.isEditor" to="/admin">Admin</RouterLink>
      </nav>
      <span class="spacer" />
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
