import { ref } from 'vue'

/**
 * Bascule de thème clair / sombre.
 *
 * - Choix explicite de l'utilisateur → `data-theme` posé sur <html> + mémorisé.
 * - Aucun choix → on laisse la préférence système gouverner (via le
 *   `@media (prefers-color-scheme)` de tokens.css) et on suit ses changements.
 */
type Theme = 'light' | 'dark'

const STORAGE_KEY = 'gp-theme'

function readStored(): Theme | null {
  const v = localStorage.getItem(STORAGE_KEY)
  return v === 'light' || v === 'dark' ? v : null
}

function systemTheme(): Theme {
  return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
}

const stored = readStored()
// État partagé (singleton module) : NavBar et init lisent la même source.
const theme = ref<Theme>(stored ?? systemTheme())
const isExplicit = ref(stored !== null)

function apply(): void {
  if (isExplicit.value) {
    document.documentElement.dataset.theme = theme.value
  } else {
    delete document.documentElement.dataset.theme
  }
}

/** À appeler une fois au démarrage (avant le mount). */
export function initTheme(): void {
  apply()
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    if (!isExplicit.value) theme.value = e.matches ? 'dark' : 'light'
  })
}

export function useTheme() {
  function toggle(): void {
    theme.value = theme.value === 'dark' ? 'light' : 'dark'
    isExplicit.value = true
    localStorage.setItem(STORAGE_KEY, theme.value)
    apply()
  }
  return { theme, toggle }
}
