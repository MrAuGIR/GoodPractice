/// <reference types="vite/client" />

interface ImportMetaEnv {
  /** Base des appels API. Défaut `/api` ; `/index.php/api` en prod sans rewrite. */
  readonly VITE_API_BASE?: string
}

interface ImportMeta {
  readonly env: ImportMetaEnv
}
