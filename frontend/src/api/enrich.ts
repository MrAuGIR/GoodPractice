import { http } from './http'

export interface OpenGraphData {
  title: string | null
  description: string | null
  image: string | null
  siteName: string | null
  url: string | null
}

/** Récupère les métadonnées OpenGraph d'une URL (réservé éditeur+). */
export async function fetchOpenGraph(url: string): Promise<OpenGraphData> {
  const { data } = await http.post<OpenGraphData>('/enrich/opengraph', { url })
  return data
}
