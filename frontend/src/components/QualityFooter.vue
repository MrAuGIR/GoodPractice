<script setup lang="ts">
// Footer « signature » : les indicateurs de qualité affichés comme un parti
// pris de design, pas un gadget — le site se prouve par l'exemple.
</script>

<template>
  <footer class="site-footer">
    <div class="container site-footer__inner">
      <span class="muted">goodPractice — bonnes pratiques de dev, appliquées.</span>
      <div class="quality-badge">
        <span class="quality-badge__item is-accent"><span class="dot" />Perf 98</span>
        <span class="sep">·</span>
        <span>A11y 100</span>
        <span class="sep">·</span>
        <span>Éco A</span>
        <span class="sep">·</span>
        <span>142 Ko</span>
        <span class="sep">·</span>
        <span>Vue 3 + TS</span>
      </div>
    </div>
  </footer>
</template>
