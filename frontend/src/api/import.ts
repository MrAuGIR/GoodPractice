import { http } from './http'

export interface ImportResult {
  dryRun: boolean
  categoriesCreated: number
  articlesCreated: number
  articlesUpdated: number
  articlesSkipped: number
}

/** Envoie le contenu JSON brut au backend (réservé admin). */
export async function importBonnesPratiques(json: string, dryRun: boolean): Promise<ImportResult> {
  const { data } = await http.post<ImportResult>('/import/bonnes-pratiques', json, {
    params: dryRun ? { 'dry-run': 1 } : {},
    headers: { 'Content-Type': 'application/json' },
  })
  return data
}
