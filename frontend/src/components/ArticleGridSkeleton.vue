<script setup lang="ts">
// Squelette de la grille d'articles : une copie fantôme de la vraie mise en
// page (pas de spinner centré). La transition vers le contenu est un fondu.
withDefaults(defineProps<{ count?: number }>(), { count: 6 })
</script>

<template>
  <div class="grid" aria-busy="true" aria-live="polite">
    <span class="sr-only">Chargement des articles…</span>
    <div v-for="n in count" :key="n" class="skeleton-card">
      <span class="skeleton thumb" />
      <div class="body">
        <span class="skeleton" style="width: 80px; height: 20px; border-radius: 999px" />
        <span class="skeleton" style="width: 100%; height: 16px" />
        <span class="skeleton" style="width: 65%; height: 16px" />
        <span class="skeleton" style="width: 50%; height: 11px; margin-top: 6px" />
      </div>
    </div>
  </div>
</template>
