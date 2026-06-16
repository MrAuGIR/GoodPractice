<script setup lang="ts">
// Squelette de la page de lecture d'un article (calqué sur la vraie colonne).
</script>

<template>
  <div aria-busy="true" aria-live="polite">
    <span class="sr-only">Chargement de l'article…</span>
    <span class="skeleton" style="width: 130px; height: 12px" />
    <span class="skeleton" style="display: block; margin-top: 20px; width: 90px; height: 24px; border-radius: 999px" />
    <span class="skeleton" style="display: block; margin-top: 16px; width: 100%; height: 34px" />
    <span class="skeleton" style="display: block; margin-top: 10px; width: 60%; height: 34px" />
    <span class="skeleton" style="display: block; margin-top: 18px; width: 45%; height: 12px" />
    <span class="skeleton" style="display: block; margin-top: 22px; width: 100%; height: 240px; border-radius: var(--radius)" />
    <span class="skeleton" style="display: block; margin-top: 22px; width: 100%; height: 14px" />
    <span class="skeleton" style="display: block; margin-top: 11px; width: 97%; height: 14px" />
    <span class="skeleton" style="display: block; margin-top: 11px; width: 88%; height: 14px" />
  </div>
</template>
