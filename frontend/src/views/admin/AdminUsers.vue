<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { deleteUser, fetchUsers, updateUserRoles } from '../../api/users'
import type { User } from '../../types'

const users = ref<User[]>([])
const loading = ref(true)
const error = ref('')
const savingId = ref<number | null>(null)

const ROLE_OPTIONS = [
  { label: 'Utilisateur', value: 'user' },
  { label: 'Éditeur', value: 'editor' },
  { label: 'Administrateur', value: 'admin' },
] as const

function levelOf(roles: string[]): string {
  if (roles.includes('ROLE_ADMIN')) return 'admin'
  if (roles.includes('ROLE_EDITOR')) return 'editor'
  return 'user'
}

function rolesFor(level: string): string[] {
  if (level === 'admin') return ['ROLE_ADMIN']
  if (level === 'editor') return ['ROLE_EDITOR']
  return []
}

async function load(): Promise<void> {
  loading.value = true
  try {
    users.value = (await fetchUsers()).member
  } catch {
    error.value = 'Chargement impossible.'
  } finally {
    loading.value = false
  }
}

async function changeRole(u: User, level: string): Promise<void> {
  savingId.value = u.id
  error.value = ''
  try {
    const updated = await updateUserRoles(u.id, rolesFor(level))
    u.roles = updated.roles
  } catch {
    error.value = 'Mise à jour du rôle refusée.'
    await load()
  } finally {
    savingId.value = null
  }
}

async function remove(u: User): Promise<void> {
  if (!confirm(`Supprimer « ${u.name} » ?`)) return
  try {
    await deleteUser(u.id)
    await load()
  } catch {
    error.value = 'Suppression refusée.'
  }
}

onMounted(load)
</script>

<template>
  <div>
    <h2>Utilisateurs</h2>
    <p v-if="error" class="alert alert-error">{{ error }}</p>
    <div v-if="loading" class="state">Chargement…</div>

    <table v-else class="table">
      <thead>
        <tr>
          <th>Nom</th>
          <th>Email</th>
          <th>Rôle</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="u in users" :key="u.id">
          <td>{{ u.name }}</td>
          <td class="muted">{{ u.email }}</td>
          <td>
            <select
              class="select"
              style="width: auto"
              :value="levelOf(u.roles)"
              :disabled="savingId === u.id"
              @change="changeRole(u, ($event.target as HTMLSelectElement).value)"
            >
              <option v-for="o in ROLE_OPTIONS" :key="o.value" :value="o.value">
                {{ o.label }}
              </option>
            </select>
          </td>
          <td class="actions">
            <button class="btn btn-danger btn-sm" @click="remove(u)">Suppr.</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
