<script setup lang="ts">
import NavBar from './components/NavBar.vue'
</script>

<template>
  <NavBar />
  <RouterView />
  <footer class="navbar" style="border-top: 1px solid var(--border); border-bottom: none">
    <div class="container" style="height: auto; padding: 18px 20px; display: block">
      <span class="muted">goodPractice — Symfony 7 · API Platform · Vue 3</span>
    </div>
  </footer>
</template>
