import { beforeEach, describe, expect, it, vi } from 'vitest'

// État singleton au niveau module : on recharge le module à chaque test.
async function load() {
  vi.resetModules()
  return import('../useTheme')
}

function mockSystemDark(dark: boolean) {
  const listeners: Array<(e: { matches: boolean }) => void> = []
  vi.spyOn(window, 'matchMedia').mockImplementation(
    (query: string) =>
      ({
        matches: dark,
        media: query,
        addEventListener: (_: string, cb: (e: { matches: boolean }) => void) => listeners.push(cb),
        removeEventListener: vi.fn(),
      }) as unknown as MediaQueryList,
  )
  return listeners
}

describe('useTheme', () => {
  beforeEach(() => {
    delete document.documentElement.dataset.theme
  })

  it('suit la préférence système sans choix explicite', async () => {
    const listeners = mockSystemDark(true)
    const { initTheme, useTheme } = await load()
    const { theme } = useTheme()

    initTheme()

    expect(theme.value).toBe('dark')
    expect(document.documentElement.dataset.theme).toBeUndefined()

    listeners.forEach((cb) => cb({ matches: false }))
    expect(theme.value).toBe('light')
  })

  it('toggle mémorise un choix explicite et pose data-theme', async () => {
    const listeners = mockSystemDark(false)
    const { initTheme, useTheme } = await load()
    initTheme()
    const { theme, toggle } = useTheme()

    toggle()

    expect(theme.value).toBe('dark')
    expect(localStorage.getItem('gp-theme')).toBe('dark')
    expect(document.documentElement.dataset.theme).toBe('dark')

    // le système ne reprend plus la main
    listeners.forEach((cb) => cb({ matches: false }))
    expect(theme.value).toBe('dark')

    toggle()
    expect(theme.value).toBe('light')
  })

  it('restaure le thème stocké', async () => {
    mockSystemDark(false)
    localStorage.setItem('gp-theme', 'dark')
    const { initTheme, useTheme } = await load()

    initTheme()

    expect(useTheme().theme.value).toBe('dark')
    expect(document.documentElement.dataset.theme).toBe('dark')
  })
})
