import { describe, it, expect } from 'vitest'
import { formatDate, articleImage, readingTime } from '../format'

describe('readingTime', () => {
  it('renvoie au moins 1 minute pour un texte vide', () => {
    expect(readingTime('')).toBe(1)
    expect(readingTime(null)).toBe(1)
    expect(readingTime(undefined)).toBe(1)
  })

  it('estime ~200 mots par minute', () => {
    expect(readingTime('mot '.repeat(200))).toBe(1)
    expect(readingTime('mot '.repeat(400))).toBe(2)
    expect(readingTime('mot '.repeat(500))).toBe(3) // 2.5 → arrondi à 3
  })
})

describe('formatDate', () => {
  it('formate une date ISO en français', () => {
    expect(formatDate('2026-01-15T12:00:00')).toBe('15 janvier 2026')
  })

  it('renvoie une chaîne vide pour une date invalide', () => {
    expect(formatDate('pas-une-date')).toBe('')
  })
})

describe('articleImage', () => {
  it('renvoie l\'URL fournie', () => {
    expect(articleImage('https://exemple.test/i.png')).toBe('https://exemple.test/i.png')
  })

  it('retombe sur un placeholder si absente', () => {
    expect(articleImage(null)).toContain('placehold.co')
    expect(articleImage(undefined)).toContain('placehold.co')
  })
})
