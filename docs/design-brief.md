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
