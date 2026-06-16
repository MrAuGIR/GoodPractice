# Import de bonnes pratiques (JSON)

Ce projet se remplit avec un corpus de **bonnes pratiques de développement** généré
**hors-ligne par un LLM** (on lui donne le prompt ci-dessous, il renvoie du JSON), puis
importé via une commande Symfony ou l'upload admin.

- **Commande** : `php bin/console app:import:bonnes-pratiques <fichier.json> [--dry-run]`
- **Admin** : page `/admin/import` (upload du fichier, case « simulation »), endpoint
  `POST /api/import/bonnes-pratiques` (`ROLE_ADMIN`, `?dry-run=1`).
- **Seed de référence** : [`backend/data/bonnes-pratiques.json`](../backend/data/bonnes-pratiques.json).

L'import est **idempotent** : upsert des catégories par `name`, des tags par `name`, des
articles par `title`. Rejouer le même fichier ne crée pas de doublon (les articles existants
sont mis à jour, tags et `featured` resynchronisés).

## Format JSON

```json
{
  "categories": [
    { "name": "Green IT", "defaultImage": null }
  ],
  "articles": [
    {
      "title": "Optimiser le poids des images",
      "category": "Green IT",
      "description": "Texte explicatif (plusieurs paragraphes) de la bonne pratique…",
      "url": "https://source-officielle.example/doc",
      "urlImg": null,
      "tags": ["Images", "Performance", "Green IT"],
      "featured": true
    }
  ]
}
```

### Champs

| Clé | Type | Notes |
|-----|------|-------|
| `categories[].name` | `string` | Obligatoire, ≤ 100 caractères. Upsert par `name`. |
| `categories[].defaultImage` | `string \| null` | URL d'illustration par défaut. |
| `articles[].title` | `string` | Obligatoire, ≤ 255. Clé d'upsert (réimport sans doublon). |
| `articles[].category` | `string` | Obligatoire. Doit correspondre à un `name` de `categories` (créée à la volée sinon). |
| `articles[].description` | `string` | Obligatoire. 2 à 4 paragraphes, sans markdown. |
| `articles[].url` | `string \| null` | Source de référence (URL valide) ou `null`. |
| `articles[].urlImg` | `string \| null` | Image (URL valide) ou `null`. |
| `articles[].tags` | `string[]` | **Tags transverses** par nom (≤ 60 car. chacun), créés à la volée. Optionnel (défaut `[]`). |
| `articles[].featured` | `boolean` | Met l'article « À la une » (★ Essentiel). Optionnel (défaut `false`). |

> Les **catégories** rangent (1 catégorie par article) ; les **tags** relient de façon
> transverse (plusieurs tags, recoupant plusieurs catégories). Un tag est référencé par son
> nom : même nom = même tag, peu importe l'article. Le `slug` est généré automatiquement.

## Prompt à donner au LLM

> Tu es un expert en développement logiciel. Génère un corpus de **bonnes pratiques de
> développement web** au format **JSON strict** (aucun texte hors du JSON), conforme à ce
> schéma :
>
> ```
> { "categories": [{ "name": string, "defaultImage": string|null }],
>   "articles":   [{ "title": string, "category": string, "description": string,
>                    "url": string|null, "urlImg": string|null,
>                    "tags": string[], "featured": boolean }] }
> ```
>
> Contraintes :
> - 5 à 6 catégories (ex. *Green IT*, *Performance*, *Sécurité*, *Accessibilité*,
>   *Qualité de code*, *DevOps*).
> - 4 à 6 articles par catégorie, soit ~30 articles au total.
> - `title` : court et actionnable (impératif). `category` : doit correspondre exactement
>   à un `name` de `categories`.
> - `description` : 2 à 4 paragraphes en français, concrets (le *pourquoi* + le *comment*),
>   sans markdown.
> - `url` : lien vers une source de référence fiable quand c'est pertinent, sinon `null`.
> - `urlImg` : `null` (les images seront ajoutées plus tard).
> - `tags` : 1 à 3 **tags transverses** par article, choisis dans un vocabulaire restreint et
>   réutilisé d'un article à l'autre (ex. *Frontend*, *Backend*, *Base de données*, *Cache*,
>   *Tests*, *Images*, *Sécurité*, *Performance*, *Accessibilité*, *Débutant*, *Infrastructure*,
>   *CI/CD*). Réemploie le **même libellé exact** quand deux articles partagent un thème —
>   c'est ce qui les relie. Réutilise un nom de catégorie comme tag uniquement si c'est pertinent.
> - `featured` : `true` pour 3 à 5 articles « essentiels » au total (transverses), `false` sinon.
> - JSON **valide et minifiable**, encodage UTF-8, pas de virgule finale, pas de commentaire.

## Vérification

```bash
# Simulation (rien écrit en base) — affiche catégories/tags/articles créés
php bin/console app:import:bonnes-pratiques data/bonnes-pratiques.json --dry-run

# Import réel
php bin/console app:import:bonnes-pratiques data/bonnes-pratiques.json
```
