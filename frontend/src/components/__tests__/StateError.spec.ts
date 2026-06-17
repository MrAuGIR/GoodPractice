import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import StateError from '../StateError.vue'

describe('StateError', () => {
  it('affiche le titre, le message et le code technique', () => {
    const wrapper = mount(StateError, {
      props: { title: 'Oups', message: 'Quelque chose a cassé', tech: 'GET /api/x' },
    })

    expect(wrapper.text()).toContain('Oups')
    expect(wrapper.text()).toContain('Quelque chose a cassé')
    expect(wrapper.text()).toContain('GET /api/x')
    expect(wrapper.attributes('role')).toBe('alert')
  })

  it('émet « retry » au clic sur le bouton', async () => {
    const wrapper = mount(StateError)

    await wrapper.get('button').trigger('click')

    expect(wrapper.emitted('retry')).toHaveLength(1)
  })
})
