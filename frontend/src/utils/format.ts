export function formatDate(iso: string): string {
  const d = new Date(iso)
  if (Number.isNaN(d.getTime())) return ''
  return d.toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' })
}

/** Image d'article avec repli sur une image neutre. */
export function articleImage(url: string | null | undefined): string {
  return url || 'https://placehold.co/640x360?text=goodPractice'
}

/** Temps de lecture estimé (≈ 200 mots/min), borné à 1 min minimum. */
export function readingTime(text: string | null | undefined): number {
  const words = (text ?? '').trim().split(/\s+/).filter(Boolean).length
  return Math.max(1, Math.round(words / 200))
}
