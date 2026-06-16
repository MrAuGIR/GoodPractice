<script setup lang="ts">
// État d'erreur : message humain, couleur d'alerte dédiée (jamais l'accent),
// code technique discret, et toujours un moyen de réessayer.
withDefaults(
  defineProps<{ title?: string; message?: string; tech?: string; retryLabel?: string }>(),
  {
    title: 'Une erreur est survenue',
    retryLabel: 'Réessayer',
  },
)

const emit = defineEmits<{ retry: [] }>()
</script>

<template>
  <div class="state-block is-error" role="alert">
    <div class="icon" aria-hidden="true">!</div>
    <h3>{{ title }}</h3>
    <p v-if="message">{{ message }}</p>
    <div class="actions">
      <button type="button" class="btn" @click="emit('retry')">↺ {{ retryLabel }}</button>
      <slot name="actions" />
    </div>
    <div v-if="tech" class="tech">{{ tech }}</div>
  </div>
</template>
