<template>
  <main class="page container">
    <div class="state-block">
      <div class="not-found-code">404</div>
      <p>Cette page s'est perdue dans le jardin. Le lien est peut-être périmé.</p>
      <div class="actions">
        <RouterLink to="/" class="btn">Retour à l'exploration</RouterLink>
        <RouterLink to="/categories" class="btn btn-ghost">Parcourir les catégories</RouterLink>
      </div>
    </div>
  </main>
</template>

<style scoped>
.not-found-code {
  font-family: var(--display);
  font-weight: 700;
  font-size: clamp(56px, 12vw, 88px);
  line-height: 1;
  letter-spacing: -0.03em;
  color: var(--text-h);
  margin-bottom: 8px;
}
</style>
