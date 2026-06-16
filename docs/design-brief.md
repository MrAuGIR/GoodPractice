# Brief design — Refonte UI/UX de *goodPractice*

> Document à destination du·de la designer. Objectif : refonte graphique et UX/UI
> complète d'un site de **bonnes pratiques de développement web**, qui sert aussi de
> **vitrine technique** dans le cadre d'une recherche d'emploi.

---

## 1. Contexte

*goodPractice* est un site éditorial qui publie des **articles de bonnes pratiques de
développement web**, rangés par catégories (Green IT, Performance, Sécurité, Accessibilité,
Qualité de code, DevOps). Il a été entièrement remodernisé techniquement (API REST +
application monopage). Il manque aujourd'hui une **identité visuelle forte** : le design
actuel est générique (palette violette « template SaaS », listes en cartes basiques,
rangement uniquement par catégorie).

Particularité importante : **le site est lui-même une démonstration**. Un site qui parle de
qualité, de performance, d'accessibilité et de sobriété numérique doit *incarner* ces
qualités. C'est le cœur du positionnement.

## 2. Objectifs

1. Donner au site une **identité visuelle moderne, mémorable et différenciante**.
2. **Incarner les bonnes pratiques** : sobriété (éco-conception), performance, accessibilité,
   structure de contenu irréprochable — visibles et assumées.
3. Repenser l'**architecture de l'information** : trouver une présentation originale et un
   meilleur rangement des articles (au-delà des catégories).
4. Servir de **preuve de savoir-faire** crédible auprès de recruteurs et leads techniques.

## 3. Cibles

- **Primaire — recruteurs / leads techniques / pairs développeurs** : doivent être impressionnés
  en quelques secondes (crédibilité, soin du détail, maîtrise UX/perf/a11y).
- **Secondaire — lecteurs développeurs** : viennent lire/parcourir les bonnes pratiques ;
  doivent trouver, situer et explorer le contenu facilement.

## 4. Message clé & positionnement

> « Je ne me contente pas de connaître les bonnes pratiques — je les applique, et ce site en
> est la preuve vivante. »

Positionnement : un **jardin de connaissances vivant** (*digital garden*) sur les bonnes
pratiques, **qui se prouve par l'exemple**.

## 5. Ton & personnalité

**Audacieux et créatif**, avec un vrai parti pris visuel — mais maîtrisé, jamais gratuit.
Le caractère doit venir du **design** (typographie expressive, compositions affirmées,
micro-interactions, une couleur d'accent signature, du détail) et **non** d'assets lourds.

> Contrainte créative centrale : **être marquant tout en restant sobre et performant.**
> L'audace doit cohabiter avec un budget perf/éco strict (voir §10). C'est précisément ce
> contraste « bold mais léger » qui rend le site crédible et original.

## 6. Direction créative — concept

Deux idées validées, à fusionner en une seule direction :

### a) La preuve par l'exemple
Le site affiche et assume sa propre qualité. Pistes à explorer (le designer choisit/propose) :
- Indicateurs de qualité **visibles** : score de performance (Lighthouse), poids de la page,
  niveau d'accessibilité, sobriété (« page éco-conçue »). À intégrer avec goût, comme un
  élément de design (badge, footer « impact », signature de bas de page), pas un gadget criard.
- Une **esthétique de la sobriété** : peu d'images lourdes, illustrations vectorielles légères,
  typographie comme matériau principal, espace, rythme.

### b) Base de connaissance / digital garden
Présenter les articles comme un **savoir vivant et relié**, pas une simple liste chronologique :
- Articles connectés par **tags transverses** (voir §7).
- Une vue d'ensemble originale (index riche, nuage/carte de thèmes, exploration par tags)
  qui donne envie de naviguer de proche en proche.
- Mise en valeur des **essentiels** : hiérarchie claire entre contenus phares et le reste.

**Références / inspirations à étudier** (à adapter, pas à copier) :
- *Digital gardens* : Maggie Appleton, Andy Matuschak (notes reliées, exploration non linéaire).
- *Éco-conception audacieuse* : Branch Magazine (ClimateAction.tech), Organic Basics
  « Low Impact Website », Wholegrain Digital — la preuve qu'on peut être bold ET ultra-léger.
- *Dark mode & polish dev* : Josh Comeau (personnalité + a11y + dark mode exemplaires),
  Linear, Vercel (rigueur, contraste, typographie).
- *Éditorial tech* : Smashing Magazine (hiérarchie de contenu, mise en avant).

## 7. Architecture de l'information & rangement des articles

Le rangement actuel (catégories seules) est trop pauvre. À concevoir :

- **Tags transverses** *(prioritaire)* : étiquettes qui traversent les catégories
  (ex. `frontend`, `backend`, `perf`, `sécurité`, `débutant`, `outillage`…). Filtrables et
  combinables. C'est le principal levier d'exploration « digital garden ».
  > Note technique : les tags sont une **nouvelle notion** (les catégories existent déjà) ;
  > prévoir l'UI de filtre par tags (chips), une page/État « articles d'un tag », et la
  > présence de tags sur la carte article et la page détail.
- **Mise en avant / essentiels** *(prioritaire)* : hiérarchiser le contenu. Prévoir
  un traitement visuel pour : article(s) « à la une », badge **« Essentiel »**, temps de
  lecture, éventuellement un ordre de recommandation. La home ne doit pas traiter tous les
  articles à plat.
- *(Secondaire, à proposer si pertinent)* : niveau de difficulté (débutant → expert),
  parcours curatés (« 5 pratiques pour démarrer »). Bonus, non obligatoire.

Les **catégories** restent un axe de navigation, mais ne sont plus le seul.

## 8. Couleurs

- **Thèmes clair ET sombre**, le **dark mode étant une vraie fonctionnalité** soignée
  (pas une inversion automatique) : contrastes, profondeur, ombres/élévations repensées pour
  chaque thème. Basculement explicite, mémorisé.
- **Abandonner le violet `#7c3aed` actuel.** Définir une **nouvelle couleur d'accent
  signature**, affirmée et mémorable, déclinée pour les deux thèmes.
- Recommandation : palette construite en **tokens sémantiques** (surface, surface-élevée,
  texte, texte-atténué, accent, accent-contrasté, bordure, succès/alerte) plutôt que des
  couleurs « en dur ». Une seule couleur d'accent forte plutôt qu'un arc-en-ciel.
- Contrastes conformes **WCAG AA au minimum** (viser AAA sur le texte courant) — c'est
  cohérent avec le message du site.

## 9. Typographie, composants & écrans

### Typographie
- La typo est le **matériau principal** (esthétique sobre). Proposer une **échelle typographique**
  claire et expressive. Privilégier une (ou deux) **police(s) variable(s) sous-ensemblées** ou
  des polices système pour rester léger. Soin particulier sur la lecture longue (articles).

### Système de design à livrer
Tokens (couleurs light+dark, typo, espacements, rayons, élévations, durées d'animation) +
composants :
- Barre de navigation + bascule de thème
- **Carte article** (avec tags, temps de lecture, badge essentiel)
- **Chip / tag** filtrable, **badge** (Essentiel / catégorie)
- Bloc « à la une » / hero éditorial
- En-tête d'article (titre, méta, tags, éventuels indicateurs qualité)
- Barre de filtres (catégorie + tags + recherche)
- Vue d'exploration « base de connaissance » (index / carte de thèmes)
- Tableau & formulaires (espace admin)
- Pagination, fil d'ariane
- **États** : vide, chargement (squelettes), erreur, focus clavier visible

### Écrans clés à maquetter (desktop + mobile)
1. **Accueil** : proposition de valeur, essentiels/à la une, entrée vers l'exploration.
2. **Exploration / index** : la présentation originale (tags, carte/nuage de thèmes, filtres).
3. **Catégorie / tag** : liste filtrée.
4. **Détail d'un article** : lecture confortable, tags, articles reliés, (commentaires).
5. **Recherche & résultats.**
6. **Espace admin** (léger) : liste + formulaire d'article — cohérent avec le design system.

## 10. Contraintes techniques (à respecter dans le design)

- **Application monopage Vue 3 + TypeScript**, **CSS maison** (variables CSS / design tokens,
  pas de gros framework UI). Le design doit être **implémentable en CSS custom** : penser
  composants, tokens, états.
- **Mobile-first / responsive** obligatoire.
- **Dark mode** géré via variables CSS (thème commutable).
- **Accessibilité** : WCAG **AA** minimum (navigation clavier, focus visible, contrastes,
  `prefers-reduced-motion` respecté pour les animations).
- **Budget performance / éco-conception** (cœur du concept) : viser une page légère
  — privilégier SVG/illustrations vectorielles, images optimisées et lazy-loadées, polices
  sous-ensemblées, animations CSS sobres. Éviter les vidéos/assets lourds, les libs
  d'animation massives. *Cible indicative : pages essentielles < ~500 Ko, score Lighthouse
  perf/a11y/bonnes pratiques ≥ 95.* (À confirmer ensemble.)
- Contenu **en français**.

## 11. Ce qu'on garde / ce qu'on change

- **On garde** : la structure de contenu (articles, catégories, auteur, commentaires),
  l'idée d'un espace admin, le caractère éditorial.
- **On change** : la palette (fini le violet générique), la présentation/rangement des
  articles (tags + essentiels + exploration), l'identité globale, le système de composants,
  l'ajout d'un dark mode soigné et d'indicateurs de qualité assumés.

## 12. Livrables attendus

1. **Moodboard / direction artistique** — idéalement **2 pistes** à arbitrer.
2. **Design tokens** : couleurs (clair + sombre), typographie, espacements, rayons,
   élévations, motion.
3. **Maquettes** des écrans clés (§9), **desktop + mobile**, dans les **deux thèmes** pour
   au moins l'accueil et le détail d'article.
4. **Bibliothèque de composants / mini design system** (Figma avec variables + auto-layout
   de préférence, pour faciliter l'intégration en CSS).
5. **Spécifications** : comportements responsives, états, dark mode, règles d'accessibilité,
   guidelines de micro-interactions.

## 13. Critères de succès

- Identité **différenciante** et mémorable, qui démarque d'un site « template ».
- **Crédibilité immédiate** auprès d'un recruteur / lead technique.
- **Accessible (AA+) et performant/éco** — le design rend cela visible et tient le budget.
- **Cohérent et industrialisable** : un système de tokens/composants réellement intégrable
  en CSS maison sur une SPA Vue.
- Une **architecture de l'information** qui donne envie d'explorer (tags, essentiels).

## 14. Questions ouvertes / à caler avec le designer

- Veux-tu un **logo / wordmark** retravaillé, ou conserver le nom tel quel typographié ?
- Niveau d'**illustration** souhaité (système d'icônes/illustrations propre vs. minimalisme typo) ?
- Budgets perf/éco chiffrés à figer (poids de page, scores cibles).
- Périmètre exact de l'admin à maquetter (minimal vs. complet).

---

# Annexe A — Détail des écrans clés

> Wireframes **basse fidélité** (structure et intention, pas la direction artistique).
> Ils décrivent *quoi* afficher et *comment ça se comporte* ; le « comment c'est beau »
> reste la liberté du designer. Les schémas ASCII sont indicatifs.

## 0. Éléments globaux (présents partout)

**En-tête (sticky, fin) :**

```
┌───────────────────────────────────────────────────────────────────┐
│  ◆ goodPractice        Explorer   Catégories   Essentiels    [🔍]  ☾ │
└───────────────────────────────────────────────────────────────────┘
```
- Wordmark à gauche (lien accueil).
- Navigation : *Explorer* (la vue digital garden), *Catégories*, *Essentiels*.
- Icône **recherche** (ouvre un champ / palette de recherche, pas une page lourde).
- **Bascule de thème** clair/sombre (☀/☾), état mémorisé.
- Si connecté éditeur/admin : entrée discrète *Admin* + avatar/menu.
- **Mobile** : nav repliée en menu (burger) ; recherche + thème restent accessibles.

**Pied de page — « signature qui prouve » :** c'est ici qu'on assume le concept.

```
┌───────────────────────────────────────────────────────────────────┐
│  goodPractice — bonnes pratiques de dev, appliquées.                │
│                                                                     │
│  Perf 98 ·  A11y 100 ·  Éco A ·  Page 142 Ko        ☾ Thème   ↑ Haut │
│  Liens · Mentions · Construit avec Symfony + Vue, éco-conçu.        │
└───────────────────────────────────────────────────────────────────┘
```
- Bandeau d'**indicateurs de qualité** (perf / accessibilité / éco / poids de page) traité
  comme un élément de design discret mais fier. (Valeurs réelles si possible, sinon statiques.)

**États transverses à concevoir** : chargement (squelettes, pas de spinner plein écran),
vide, erreur, focus clavier visible, `prefers-reduced-motion`.

---

## 1. Accueil

**But** : poser la proposition de valeur en 3 secondes, mettre en avant les essentiels,
donner envie d'explorer. Surtout **ne pas** afficher tous les articles à plat.

```
┌───────────────────────────────────────────────────────────────────┐
│  [ HERO ]                                                           │
│   Les bonnes pratiques du web,                                      │
│   appliquées — ce site en est la preuve.            ◆ motif/typo    │
│   [ Explorer les pratiques ]   [ Voir les essentiels ]              │
│                                                                     │
│  ── À LA UNE ──────────────────────────────────────────────────    │
│  ┌───────────────────────────────┐  ┌──────────────┐ ┌──────────┐  │
│  │  ★ ESSENTIEL                   │  │ Article      │ │ Article  │  │
│  │  Titre de l'article phare      │  │ #tags  6 min │ │ #tags    │  │
│  │  Accroche sur 2 lignes…        │  └──────────────┘ └──────────┘  │
│  │  #perf #frontend · 6 min       │                                 │
│  └───────────────────────────────┘                                 │
│                                                                     │
│  ── EXPLORER PAR THÈME ───────────────────────────────────────     │
│  ( Green IT ) ( Performance ) ( Sécurité ) ( A11y ) ( DevOps )…     │
│  #frontend #backend #tests #débutant #outillage …  (nuage de tags) │
│                                                                     │
│  ── DERNIERS AJOUTS ──────────────────────────────────────────     │
│  • Titre …………………………………… #tag  4 min                                 │
│  • Titre …………………………………… #tag  7 min                                 │
└───────────────────────────────────────────────────────────────────┘
```
- **Hero** typographique fort (l'audace passe par la typo, pas une image lourde).
- **À la une / Essentiels** : 1 article vedette + 2-3 secondaires (hiérarchie visible).
- **Explorer par thème** : entrée double — catégories ET nuage de tags → amorce le digital garden.
- **Derniers ajouts** : liste sobre.
- **Mobile** : hero plein écran, à la une en pile verticale (vedette puis cartes), thèmes en
  rangée scrollable.
- **Expression du concept** : hero = message ; nuage de tags = base de connaissance ;
  indicateurs en footer = preuve.

---

## 2. Exploration / Index (la vue « digital garden ») — écran signature

**But** : présentation **originale** du savoir. C'est l'écran le plus différenciant.
Naviguer par tags/thèmes de proche en proche plutôt qu'une liste paginée.

```
┌───────────────────────────────────────────────────────────────────┐
│  Explorer                                   [ Liste ] [ Carte ]  ⟳  │
│  Filtres : ( Catégorie ▾ )  #frontend ✕  #perf ✕   [+ ajouter tag]  │
│  37 pratiques · triées par : Pertinence ▾                          │
├───────────────────────────────────────────────────────────────────┤
│                                                                     │
│   Vue LISTE (par défaut)            │   Vue CARTE (toggle)          │
│   ┌─────────────┐ ┌─────────────┐   │        (perf)                 │
│   │ ★ Titre      │ │ Titre        │   │      •───•   •──• (tests)     │
│   │ #perf #front │ │ #front       │   │   (front)\  /                 │
│   │ 6 min        │ │ 4 min        │   │        •──•──• (a11y)          │
│   └─────────────┘ └─────────────┘   │      (sécu)   \                │
│   ┌─────────────┐ ┌─────────────┐   │               •  (green it)    │
│   │ Titre        │ │ Titre        │   │  → clic sur un nœud = filtre   │
│   └─────────────┘ └─────────────┘   │     / ouvre l'article          │
└───────────────────────────────────────────────────────────────────┘
```
- **Barre de filtres** combinables : catégorie + plusieurs **tags** (chips retirables) + tri
  (pertinence / récent / temps de lecture). État reflété dans l'URL (partageable).
- **Deux vues commutables** :
  - *Liste* (grille de cartes filtrées) — robuste, défaut.
  - *Carte / nuage de thèmes* — vue originale : tags/articles reliés ; cliquer un thème filtre,
    cliquer un article l'ouvre. (Si trop coûteux en perf : version « nuage de tags » statique.)
- **Mobile** : filtres dans un panneau dépliable (bottom-sheet) ; vue liste par défaut, carte
  simplifiée ou masquée.
- **États** : aucun résultat (« Aucune pratique pour ces filtres » + reset), chargement squelette.
- **Expression du concept** : exploration non linéaire = cœur « digital garden ».

---

## 3. Catégorie / Tag (liste filtrée)

**But** : page d'atterrissage d'une catégorie ou d'un tag (SEO + navigation directe).

```
┌───────────────────────────────────────────────────────────────────┐
│  Catégorie : Performance            (12 pratiques)                  │
│  Courte description de l'axe / du tag.                              │
│  Tags liés : #frontend #images #cache #réseau                      │
│  Tri : Pertinence ▾                                                 │
├───────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐                   │
│  │ ★ Titre      │ │ Titre        │ │ Titre        │                   │
│  │ #tags · 6min │ │ #tags · 4min │ │ #tags · 9min │                   │
│  └─────────────┘ └─────────────┘ └─────────────┘                   │
│                       … pagination ou « charger plus » …            │
└───────────────────────────────────────────────────────────────────┘
```
- En-tête contextuel (titre, description, **tags liés** cliquables → tissage entre thèmes).
- Même carte article que partout (cohérence du design system).
- Pagination **ou** « charger plus » (au choix designer, mais sobre).
- **Mobile** : cartes en pile, en-tête compact.

---

## 4. Détail d'un article — confort de lecture

**But** : lecture longue agréable + tissage vers le reste du jardin.

```
┌───────────────────────────────────────────────────────────────────┐
│  ‹ Performance / Explorer                                          │
│                                                                     │
│  ★ ESSENTIEL                                                        │
│  Titre de la bonne pratique (grand, expressif)                     │
│  Par Auteur · 14 juin 2026 · 6 min · #perf #frontend #images       │
│  ─────────────────────────────────────────────────────────────    │
│                                                                     │
│   Corps de l'article, colonne de lecture étroite (~65 car.),       │
│   typographie soignée, paragraphes aérés, citations, code.         │
│                                                                     │
│   [ ↗ Source de référence ]                                        │
│                                                                     │
│  ── À EXPLORER ENSUITE ─────────────────────────────────────       │
│  ┌────────────┐ ┌────────────┐ ┌────────────┐  (mêmes tags)        │
│  │ Titre lié   │ │ Titre lié   │ │ Titre lié   │                     │
│  └────────────┘ └────────────┘ └────────────┘                     │
│                                                                     │
│  ── COMMENTAIRES ──────────────────────────────────────────        │
│  [ champ commentaire si connecté ]                                  │
│  • Auteur — « … »                                                   │
└───────────────────────────────────────────────────────────────────┘
```
- **En-tête** : badge essentiel (si applicable), titre, méta (auteur, date, temps de lecture),
  **tags cliquables**.
- **Colonne de lecture étroite** centrée (lisibilité), typo de lecture optimisée pour les 2 thèmes.
- Lien vers la **source de référence** (champ `url`) traité comme une action claire.
- **Articles reliés** (« à explorer ensuite », basés sur tags/catégorie) = tissage du jardin.
- **Commentaires** : formulaire si authentifié, liste ; suppression pour admin.
- **Mobile** : tout en une colonne ; méta compacte ; reliés en pile.
- *(Option, dans l'esprit « preuve »)* : mini sommaire / barre de progression de lecture, sobre.

---

## 5. Recherche & résultats

**But** : trouver vite. Privilégier une **recherche légère** (champ/palette) plutôt qu'une page lourde.

```
┌───────────────────────────────────────────────────────────────────┐
│  🔍  perf images_____________________________________   (Échap)    │
│  ─────────────────────────────────────────────────────────────    │
│  PRATIQUES                                                          │
│  • Titre …………………………… #perf #images                                 │
│  • Titre …………………………… #perf                                          │
│  TAGS        #performance  #images  #lazy-loading                   │
│  CATÉGORIES  Performance                                            │
└───────────────────────────────────────────────────────────────────┘
```
- Ouverte depuis l'icône recherche (overlay / palette type ⌘K) — clin d'œil « dev » discret.
- Résultats **groupés** : pratiques, tags, catégories. Navigation clavier (↑↓ + Entrée).
- Recherche au fil de la frappe (débouncée).
- État vide (« Tapez pour chercher… »), aucun résultat.
- **Mobile** : overlay plein écran.

---

## 6. Espace admin (léger, cohérent avec le design system)

**But** : gérer les articles sans casser l'identité. Visuel sobre, mêmes tokens/composants.

**Liste des articles :**
```
┌───────────────────────────────────────────────────────────────────┐
│  Admin › Articles                         [ + Nouvel article ]      │
│  [ recherche ]                Filtre catégorie ▾                    │
├───────────────────────────────────────────────────────────────────┤
│  Titre              Catégorie     Tags        ★   Date     Actions  │
│  Titre …………          Performance   #perf       ★   14/06    ✎  🗑     │
│  Titre …………          Sécurité      #auth           12/06    ✎  🗑     │
└───────────────────────────────────────────────────────────────────┘
```

**Formulaire article (création / édition) :**
```
┌───────────────────────────────────────────────────────────────────┐
│  Nouvel article                                   [ Annuler ]       │
│  Titre        [______________________________]                     │
│  Catégorie    [ Performance ▾ ]                                     │
│  Tags         [ #perf ✕ ] [ #images ✕ ] [+ ajouter]                │
│  ☆ Marquer comme essentiel  [ ]                                     │
│  Lien source  [____________________]  [ Récupérer les métadonnées ] │
│  Image (URL)  [____________________]                               │
│  Description  [ éditeur de texte multi-lignes …………………………… ]          │
│                                          [ Enregistrer ]            │
└───────────────────────────────────────────────────────────────────┘
```
- Reprend les nouveaux concepts : **tags**, **marquer essentiel**, bouton **« Récupérer les
  métadonnées »** (enrichissement OpenGraph déjà en place).
- Mêmes champs (formulaire, table, boutons) que le design system public.
- États : validation/erreurs de champ, sauvegarde en cours, succès.
- **Mobile** : table → cartes empilées ; formulaire en une colonne.

---

## Récapitulatif des écrans à livrer (desktop + mobile)

| # | Écran                  | Priorité | Thèmes à fournir        |
|---|------------------------|----------|-------------------------|
| 1 | Accueil                | Haute    | Clair **et** sombre     |
| 2 | Exploration / index    | Haute    | Clair **et** sombre     |
| 4 | Détail article         | Haute    | Clair **et** sombre     |
| 3 | Catégorie / Tag        | Moyenne  | 1 thème (l'autre dérivé) |
| 5 | Recherche (overlay)    | Moyenne  | 1 thème                 |
| 6 | Admin (liste + form)   | Basse    | 1 thème                 |
