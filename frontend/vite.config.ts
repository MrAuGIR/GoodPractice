/// <reference types="vitest/config" />
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  test: {
    environment: 'jsdom',
    globals: true,
    setupFiles: ['src/__tests__/setup.ts'],
    coverage: {
      provider: 'v8',
      reporter: ['text', 'html', 'lcov', 'json-summary'],
      include: ['src/**/*.{ts,vue}'],
      exclude: ['src/main.ts', 'src/env.d.ts', 'src/types/**', '**/__tests__/**'],
      thresholds: { lines: 70, functions: 70, branches: 60, statements: 70 },
    },
  },
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
