<script setup lang="ts">
// État vide : dit pourquoi c'est vide, puis offre l'action suivante.
// Jamais une zone morte — passez les actions via le slot #actions.
withDefaults(defineProps<{ title: string; message?: string; icon?: string }>(), {
  icon: '◇',
})
</script>

<template>
  <div class="state-block">
    <div class="icon" aria-hidden="true">{{ icon }}</div>
    <h3>{{ title }}</h3>
    <p v-if="message">{{ message }}</p>
    <div v-if="$slots.actions" class="actions">
      <slot name="actions" />
    </div>
  </div>
</template>
