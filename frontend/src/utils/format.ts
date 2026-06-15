export function formatDate(iso: string): string {
  const d = new Date(iso)
  if (Number.isNaN(d.getTime())) return ''
  return d.toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' })
}

/** Image d'article avec repli sur une image neutre. */
export function articleImage(url: string | null | undefined): string {
  return url || 'https://placehold.co/640x360?text=goodPractice'
}
