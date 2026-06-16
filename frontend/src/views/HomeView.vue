<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { fetchArticles } from '../api/articles'
import { fetchCategories } from '../api/categories'
import type { Article, Category } from '../types'
import { formatDate, readingTime } from '../utils/format'
import HeroGraph from '../components/HeroGraph.vue'
import ArticleGridSkeleton from '../components/ArticleGridSkeleton.vue'
import StateEmpty from '../components/StateEmpty.vue'
import StateError from '../components/StateError.vue'

const route = useRoute()

// --- Données de la page d'accueil (indépendantes des filtres) ---
const latest = ref<Article[]>([])
const featuredList = ref<Article[]>([])
// À la une : les articles « essentiels » ; repli sur les plus récents si aucun.
const highlights = computed(() =>
  featuredList.value.length ? featuredList.value : latest.value,
)
const featured = computed(() => highlights.value[0] ?? null)
const secondary = computed(() => highlights.value.slice(1, 3))
const feed = computed(() => latest.value.slice(0, 4))

// --- Grille « toutes les pratiques » (recherche + filtre + pagination) ---
const articles = ref<Article[]>([])
const categories = ref<Category[]>([])
const total = ref(0)
const page = ref(1)
const itemsPerPage = 9
const search = ref('')
const initialCategory = Number(route.query.category)
const categoryId = ref<number | null>(initialCategory || null)
const loading = ref(false)
const error = ref('')
const errorTech = ref('')

const hasFilters = computed(() => !!search.value || categoryId.value !== null)

let searchTimer: ReturnType<typeof setTimeout> | undefined

async function load(): Promise<void> {
  loading.value = true
  error.value = ''
  errorTech.value = ''
  try {
    const data = await fetchArticles({
      page: page.value,
      itemsPerPage,
      title: search.value || undefined,
      category: categoryId.value ?? undefined,
      order: { dateCreate: 'desc' },
    })
    articles.value = data.member
    total.value = data.totalItems
  } catch {
    error.value = "Le serveur n'a pas répondu. Vérifiez votre connexion, puis réessayez."
    errorTech.value = 'GET /api/articles'
  } finally {
    loading.value = false
  }
}

function resetFilters(): void {
  search.value = ''
  categoryId.value = null
  page.value = 1
}

function selectTheme(id: number): void {
  categoryId.value = categoryId.value === id ? null : id
  page.value = 1
  document.getElementById('toutes')?.scrollIntoView({ behavior: 'smooth' })
}

onMounted(async () => {
  await load()
  try {
    categories.value = (await fetchCategories()).member
    latest.value = (
      await fetchArticles({ itemsPerPage: 6, order: { dateCreate: 'desc' } })
    ).member
    featuredList.value = (
      await fetchArticles({ featured: true, itemsPerPage: 3, order: { dateCreate: 'desc' } })
    ).member
  } catch {
    /* sections d'accueil non bloquantes */
  }
})

watch([categoryId, page], load)
watch(search, () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    page.value = 1
    load()
  }, 350)
})

const totalPages = () => Math.max(1, Math.ceil(total.value / itemsPerPage))
</script>

<template>
  <main class="page container">
    <!-- ===================== HERO ===================== -->
    <section class="hero">
      <div>
        <span class="eyebrow">Jardin de bonnes pratiques</span>
        <h1>Les bonnes pratiques du web, appliquées — ce site en est la preuve.</h1>
        <p>
          Performance, sécurité, accessibilité, éco-conception. Un savoir relié, vérifiable,
          et qui se prouve par l'exemple.
        </p>
        <div class="cta">
          <a href="#toutes" class="btn">Explorer les pratiques</a>
          <RouterLink to="/categories" class="btn btn-ghost">Voir les catégories</RouterLink>
        </div>
      </div>
      <div class="hero__graph"><HeroGraph /></div>
    </section>

    <!-- ===================== À LA UNE ===================== -->
    <section v-if="featured" style="margin-top: 40px">
      <div class="section-label">À la une</div>
      <div class="featured">
        <RouterLink
          :to="{ name: 'article', params: { id: featured.id } }"
          class="practice-card"
        >
          <div class="practice-card__meta">
            <span v-if="featured.featured" class="tag-essential">★ Essentiel</span>
            <span class="practice-card__cat"><span class="diamond" />{{ featured.category?.name }}</span>
            <span style="margin-left: auto">{{ readingTime(featured.description) }} min</span>
          </div>
          <h3>{{ featured.title }}</h3>
          <p>{{ featured.description }}</p>
          <div class="practice-card__foot">
            <span v-for="t in featured.tags?.slice(0, 3)" :key="t.id" class="chip">#{{ t.slug }}</span>
            <span style="margin-left: auto">{{ featured.author?.name }}</span>
          </div>
        </RouterLink>

        <div class="featured__side">
          <RouterLink
            v-for="a in secondary"
            :key="a.id"
            :to="{ name: 'article', params: { id: a.id } }"
            class="practice-card"
          >
            <div class="practice-card__meta">
              <span v-if="a.featured" class="tag-essential">★ Essentiel</span>
              <span class="practice-card__cat"><span class="diamond" />{{ a.category?.name }}</span>
              <span style="margin-left: auto">{{ readingTime(a.description) }} min</span>
            </div>
            <h4>{{ a.title }}</h4>
          </RouterLink>
        </div>
      </div>
    </section>

    <!-- ===================== EXPLORER PAR THÈME ===================== -->
    <section v-if="categories.length" style="margin-top: 40px">
      <div class="section-label">Explorer par thème</div>
      <div class="theme-pills">
        <button
          v-for="c in categories"
          :key="c.id"
          type="button"
          class="theme-pill"
          :class="{ 'is-active': categoryId === c.id }"
          @click="selectTheme(c.id)"
        >
          {{ c.name }}
        </button>
      </div>
    </section>

    <!-- ===================== DERNIERS AJOUTS ===================== -->
    <section v-if="feed.length" style="margin-top: 40px">
      <div class="section-label">Derniers ajouts</div>
      <div>
        <RouterLink
          v-for="a in feed"
          :key="a.id"
          :to="{ name: 'article', params: { id: a.id } }"
          class="feed-row"
        >
          <span class="feed-row__title">{{ a.title }}</span>
          <span class="feed-row__meta">{{ a.category?.name }}</span>
          <span class="feed-row__meta" style="width: 48px; text-align: right">
            {{ readingTime(a.description) }} min
          </span>
        </RouterLink>
      </div>
    </section>

    <!-- ===================== TOUTES LES PRATIQUES ===================== -->
    <section id="toutes" style="margin-top: 48px; scroll-margin-top: 76px">
      <div class="section-label">Toutes les pratiques</div>

      <div class="toolbar">
        <input v-model="search" class="input grow" placeholder="Rechercher une pratique…" />
        <select v-model="categoryId" class="select" style="width: auto">
          <option :value="null">Toutes les catégories</option>
          <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
      </div>

      <ArticleGridSkeleton v-if="loading" :count="6" />

      <StateError
        v-else-if="error"
        title="Impossible de charger les pratiques"
        :message="error"
        :tech="errorTech"
        @retry="load"
      />

      <StateEmpty
        v-else-if="articles.length === 0 && hasFilters"
        title="Aucune pratique ne correspond à ces filtres"
        message="Élargissez la recherche en retirant un filtre, ou repartez de la liste complète."
      >
        <template #actions>
          <button type="button" class="btn" @click="resetFilters">Réinitialiser les filtres</button>
        </template>
      </StateEmpty>

      <StateEmpty
        v-else-if="articles.length === 0"
        title="Aucune pratique pour l'instant"
        message="Le jardin attend sa première bonne pratique."
      />

      <div v-else class="grid">
        <RouterLink
          v-for="a in articles"
          :key="a.id"
          :to="{ name: 'article', params: { id: a.id } }"
          class="card"
          style="color: inherit"
        >
          <div class="body">
            <div class="practice-card__meta">
              <span v-if="a.featured" class="tag-essential">★ Essentiel</span>
              <span class="practice-card__cat"><span class="diamond" />{{ a.category?.name }}</span>
              <span style="margin-left: auto">{{ readingTime(a.description) }} min</span>
            </div>
            <h3 style="font-size: 1.15rem; margin: 0">{{ a.title }}</h3>
            <div class="practice-card__foot" style="margin-top: 4px">
              <span v-for="t in a.tags?.slice(0, 3)" :key="t.id" class="chip">#{{ t.slug }}</span>
            </div>
            <div class="meta">
              <span>{{ a.author?.name }}</span>
              <span>{{ formatDate(a.dateCreate) }}</span>
              <span>{{ a.nbComments }} commentaire(s)</span>
            </div>
          </div>
        </RouterLink>
      </div>

      <div v-if="totalPages() > 1" class="pagination">
        <button class="btn btn-ghost btn-sm" :disabled="page <= 1" @click="page--">Précédent</button>
        <span class="muted">Page {{ page }} / {{ totalPages() }}</span>
        <button class="btn btn-ghost btn-sm" :disabled="page >= totalPages()" @click="page++">
          Suivant
        </button>
      </div>
    </section>
  </main>
</template>
