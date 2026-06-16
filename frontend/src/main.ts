import { createApp } from 'vue'
import { createPinia } from 'pinia'
import './style.css'
import App from './App.vue'
import { router } from './router'
import { setOnAuthFailure } from './api/http'
import { useAuthStore } from './stores/auth'
import { initTheme } from './composables/useTheme'

// Applique le thème (clair/sombre) avant le mount pour éviter tout flash.
initTheme()

const app = createApp(App)
app.use(createPinia())
app.use(router)

// Session expirée : on nettoie le store et on renvoie vers le login
setOnAuthFailure(() => {
  const auth = useAuthStore()
  auth.logout()
  router.push({ name: 'login' })
})

app.mount('#app')
