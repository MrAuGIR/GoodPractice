import { describe, it, expect } from 'vitest'
import { iri } from '../http'

describe('iri', () => {
  it('construit une référence IRI API Platform', () => {
    expect(iri('categories', 3)).toBe('/api/categories/3')
    expect(iri('tags', 12)).toBe('/api/tags/12')
  })
})
