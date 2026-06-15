<script setup lang="ts">
import { useAuthStore } from '../../stores/auth'

const auth = useAuthStore()
</script>

<template>
  <main class="page container">
    <div class="page-header"><h1>Administration</h1></div>
    <div class="admin-shell">
      <aside class="admin-nav">
        <RouterLink :to="{ name: 'admin-articles' }">Articles</RouterLink>
        <RouterLink v-if="auth.isAdmin" :to="{ name: 'admin-categories' }">Catégories</RouterLink>
        <RouterLink v-if="auth.isAdmin" :to="{ name: 'admin-users' }">Utilisateurs</RouterLink>
        <RouterLink v-if="auth.isAdmin" :to="{ name: 'admin-import' }">Import JSON</RouterLink>
      </aside>
      <section>
        <RouterView />
      </section>
    </div>
  </main>
</template>
