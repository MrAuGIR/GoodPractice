<script setup lang="ts">
// Graphe de connaissances décoratif (jardin relié) — illustre le concept
// « savoir relié ». Couleurs pilotées par les tokens de thème.
</script>

<template>
  <svg viewBox="0 0 300 250" role="img" aria-label="Réseau de bonnes pratiques reliées entre elles">
    <g class="edges">
      <line x1="150" y1="120" x2="70" y2="55" />
      <line x1="150" y1="120" x2="248" y2="70" />
      <line x1="150" y1="120" x2="60" y2="180" />
      <line x1="150" y1="120" x2="235" y2="185" />
      <line x1="150" y1="120" x2="160" y2="225" />
      <line x1="70" y1="55" x2="248" y2="70" class="dashed" />
      <line x1="60" y1="180" x2="160" y2="225" class="dashed" />
    </g>
    <circle cx="150" cy="120" r="26" class="node-accent" />
    <text x="150" y="124" class="label-on-accent">perf</text>
    <circle cx="70" cy="55" r="15" class="node" />
    <text x="70" y="59" class="label">a11y</text>
    <circle cx="248" cy="70" r="17" class="node" />
    <text x="248" y="74" class="label">front</text>
    <circle cx="60" cy="180" r="13" class="node" />
    <text x="60" y="184" class="label sm">cache</text>
    <circle cx="235" cy="185" r="16" class="node" />
    <text x="235" y="189" class="label">sécu</text>
    <circle cx="160" cy="225" r="12" class="node" />
    <text x="160" y="229" class="label sm">img</text>
  </svg>
</template>

<style scoped>
.edges line {
  stroke: var(--border);
  stroke-width: 1.5;
}
.edges line.dashed {
  stroke-width: 1;
  stroke-dasharray: 3 4;
}
.node-accent {
  fill: var(--accent);
}
.node {
  fill: var(--surface);
  stroke: var(--border);
  stroke-width: 1.5;
}
text {
  font-family: var(--mono);
  font-size: 9px;
  fill: var(--text-muted);
  text-anchor: middle;
}
text.sm {
  font-size: 8px;
}
.label-on-accent {
  font-size: 11px;
  font-weight: 600;
  fill: var(--on-accent);
  text-anchor: middle;
}
</style>
