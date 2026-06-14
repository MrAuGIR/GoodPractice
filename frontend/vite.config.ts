import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  server: {
    host: '0.0.0.0',
    port: 5173,
    // HMR derrière Docker
    watch: {
      usePolling: true,
    },
    // Proxy des appels API vers le backend Symfony (réseau Docker)
    proxy: {
      '/api': {
        target: 'http://backend:80',
        changeOrigin: true,
      },
    },
  },
})
