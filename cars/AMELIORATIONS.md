# Améliorations du projet — SelectCars

> Récapitulatif de toutes les optimisations, corrections et mesures de sécurité
> réalisées sur le projet Laravel 10 « Cars » (SelectCars).
> Méthode de travail : par **lots** indépendants, sans régression ni affaiblissement de la sécurité.

---

## Vue d'ensemble

| Axe | Contenu |
|---|---|
| **Sécurité** | Anti-brute-force, sessions en base chiffrées, en-têtes HTTP, Gates/Policies, validation, anti-IDOR |
| **Performance** | Index MySQL, eager loading (anti N+1), cache, allègement des assets (~86 Mo) |
| **Qualité de code** | Refactorisation contrôleurs/vues, suppression de code mort, modèles renommés, validation centralisée |
| **Fiabilité** | 8 bugs corrigés (B1→B8) + 2 bugs de session récents, tests automatisés (19 tests) |
| **Documentation** | README détaillé, SECURITE.md, ce fichier |

---

## 1. Lots d'optimisation

### Lot A — Correction des bugs bloquants (B1 → B8)

| # | Bug corrigé | Impact |
|---|---|---|
| B1 | Édition de catégorie : modèles inexistants (`Gestion_pri`, `Produit`) | Erreur 500 supprimée |
| B2 | Aperçu de facture : modèle `Produit` inexistant | Erreur 500 supprimée |
| B3 | `ClientController::destroy` manquant | Suppression de client opérationnelle |
| B4 | Liens DataTables cassés + sémantique de suppression client | Navigation corrigée |
| B5 | Liens des produits côté front (cible) | Liens fonctionnels |
| B6 | Filtre de recherche par modèle (`getModels`) | Filtre réparé |
| B7 | Affectation du livreur | Livreur désormais requis |
| B8 | Null-check du stock | Plus de crash si stock absent |

### Lot B — Index MySQL manquants

Nouvelle migration `add_performance_indexes` (aucune migration existante modifiée) :

| Table | Index ajoutés |
|---|---|
| `admins` | `UNIQUE(login)`, `INDEX(role, status)` |
| `commande__m_s` | `INDEX(id_client)`, `INDEX(id_livreur)`, `INDEX(ref_commande)`, `INDEX(etat)` |
| `detail_commande_m_s` | `INDEX(ref_commande)`, `INDEX(id_produit)` |
| `stock_machines` | `UNIQUE(id_produit)` |
| `supports` | `INDEX(id_client)` |

→ Jointures, filtres et connexion accélérés.

### Lot C — Eager loading (anti N+1)

- `GestionMachine::with(['marque', 'model', 'stock'])` dans les listes admin/client.
- `CommandeM::with(['client', 'livreur'])` dans les trois vues de commandes.
- `MarqueMachine::withCount('gestionMachines as nb')` pour le tableau de bord.
- Vues converties aux relations (`$data->marque?->titre`, `$commande->client?->nom`) au lieu de requêtes par ligne.
- Résultat mesuré : **4 requêtes au lieu de 34** pour 11 machines + relations (tinker).

### Lot D — Logique métier dans les contrôleurs

- Toute la logique du tableau de bord déplacée des vues vers `DashController` (`support()`, `machine()`, `produit()`).
- `$admin = Auth::guard('admin')->user()` injecté proprement dans les vues back.
- **Bug corrigé** : page `Stock/edit` en erreur 500 (`$Stock` → `$stock`).
- **Bug corrigé** : `GestionMachineController::show()` plantait sur id inexistant → renvoie désormais **404** (au lieu de 500).
- Relation `MarqueMachine::gestionMachines()` ajoutée.

### Lot E — Cache avec invalidation sécurisée

| Clé de cache | Durée | Invalidé lors de |
|---|---|---|
| `front.marques` | 600 s | store/update/destroy des marques |
| `front.models` | 600 s | store/update/destroy des modèles |
| `front.banners` | 600 s | store/update/destroy des bannières |
| `front.faq.list` | 600 s | store/update/destroy de la FAQ |
| `back.tickets.count` | 300 s | store/update/destroy des tickets SAV |

- **Sécurité** : la route `/clear-cache` est passée de GET à **POST protégée par CSRF** (le vidage de cache ne peut plus être déclenché par un simple lien).
- `CacheController` réécrit (`optimize:clear` + `Cache::flush()`).

### Lot F — SEO, marque et tests non destructifs

- Titres SEO harmonisés : `Accueil - SelectCars`, `FAQ - SelectCars`, `Machines - SelectCars`, `Connexion - SelectCars`.
- Marque « SelectCars » uniformisée (aperçu de facture, footer).
- Code mort supprimé : `Front/LoginController` orphelin (aucune route, aucune vue).
- Création de `AuthSecurityTest` : 6 tests non destructifs (pas de RefreshDatabase, la vraie base n'est pas touchée).

### Lot G — Autorisation par Gates/Policies

- **Gate `role`** : `Gate::allows('role', 'admin')` ou `['admin', 'livreur']` (variadique).
- **`CommandePolicy`** : `view`, `markDelivered`, `delete`, `assignLivreur` avec règles par rôle (admin, client propriétaire, livreur assigné).
- Contrôleurs protégés : `maj()`, `updateLivreur()` (assignLivreur), `destroy()` (delete, dans une transaction).
- Vues converties : `@can('role', …)` / `@elsecan` dans `header`, `sidebar`, `commandeM/list`, `commandeM/factureM`.
- Blocs `@php $admin` morts supprimés dans list et factureM.
- Nouveaux tests : `CommandePolicyTest` (7 tests unitaires purs) + `RoleGateTest` (4 tests).

### Lot H — Allègement des assets (~86 Mo économisés)

- **Images** : `background.jpg` compressée (860 → 297 Ko), `footer-bg-3.jpg` (258 → 51 Ko), 10 images mortes supprimées (~6,9 Mo) après vérification de 0 référence (code, vues, CSS, JS, base).
- **AdminLTE** : `assets/back` réduit de 89 Mo → 10 Mo (plugins inutilisés : toastr, sweetalert2, select2, summernote, moment, codemirror, fullcalendar, chart.js, uplot, dropzone, jquery-ui, flag-icon-css… ; doublons non-minifiés ; CSS alt ; pages de démo).
- **Composer** : retrait de `laravel/helpers` et `laravel/sail` (inutilisés).
- **NPM** : suppression de `webpack.mix.js` et de `laravel-mix`.
- Vérifié : compilation des vues OK, 114 routes, tous les assets référencés répondent en 200.

---

## 2. Sécurité (plan SECURITE.md, phases réalisées)

### Phase 1 — Autorisation centralisée & anti-IDOR ✅

- Policies et Gate `role` (voir Lot G) — plus aucun `role == '...'` utilisé comme contrôle de sécurité.
- Vérification de propriété : un client ne voit que ses commandes, un livreur uniquement les siennes.
- Actions non autorisées → 403.

### Phase 2 — Anti-brute-force & configuration production ✅

- **Throttle** sur la route de login : 5 tentatives/minute.
- `RateLimiter` nommé `login` : verrouillage par couple `login` + IP, message dédié « Trop de tentatives de connexion. Veuillez réessayer dans une minute. »
- `APP_DEBUG=false`, `APP_ENV=production` documentés dans `.env.example`.
- Identifiants par défaut remplacés par des mots de passe forts (seeder + comptes en base).
- Message d'erreur générique « Login ou mot de passe incorrect. » (anti-énumération).
- Comptes désactivés (`status`) bloqués à la connexion.

### Phase 3 — Sessions, validation, nettoyage ✅

- **Sessions en base** : `SESSION_DRIVER=database` + migration `sessions` ; purge automatique par la loterie Laravel.
- Durée de session réduite : 120 → **60 min**.
- Validation `max:255` sur `login` (auth + création de comptes).
- Code mort supprimé (`AdminController::__construct`).
- HTTPS forcé en production (`AppServiceProvider::URL::forceScheme`).
- **Journalisation des échecs de connexion** : `Log::warning('Échec de connexion', [login, ip, user_agent])` — le mot de passe n'est **jamais** journalisé.

### Phase 4 — Durcissement avancé ✅ (partiel)

- `SESSION_ENCRYPT=true` : contenu de session chiffré en base (`config/session.php` corrigé pour lire l'env).
- Middleware **`SecurityHeaders`** ajouté au groupe `web` : `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, **CSP**, **HSTS** (HTTPS).
- Retrait de `laravel/sanctum` (inutilisé) : composer, `config/sanctum.php`, trait `HasApiTokens`, `routes/api.php` nettoyés.

### Mesures conservées (jamais affaiblies)

CSRF sans exception, hachage bcrypt, politique de mot de passe forte, régénération d'ID de session au login, cookie `http_only` + `same_site=lax`, anti-cache retour arrière (`RevalidateBackHistory`), middleware `admin` et `role` (`EnsureRole`).

### Restant à faire (déploiement / hors code)

- `.env` de production distinct + `APP_KEY` régénéré (invaliderait sessions et données chiffrées).
- Vérification TLS/HSTS sur environnement HTTPS réel.
- 2FA pour les comptes admin (choix d'approche à valider).
- Sauvegardes chiffrées + procédure de restauration.

---

## 3. Qualité de code

- **Modèles renommés** (convention camelCase) : `Commande_M` → `CommandeM`, `Detail_commandeM` → `DetailCommandeM`, `Gestion_machine` → `GestionMachine`, `Model_machine` → `ModelMachine`, `stock_machine` → `StockMachine` ; anciens `Client`, `Settings`, `User` supprimés (auth unifiée sur `Admin`).
- **Validation centralisée** dans des Form Requests : `StoreAdminRequest`, `StoreClientRequest`, `StoreLivreurRequest`.
- **Suppression des anciens contrôleurs Auth Laravel** inutilisés (Login, Register, ResetPassword, etc.).
- **Seeders de données factices** : clients, commandes, produits, messages de contact, tickets SAV.
- Migration `drop_unused_tables` : suppression des tables héritées inutilisées.

---

## 4. Tests automatisés — 19 tests, 100 % verts

| Fichier | Tests | Couverture |
|---|---|---|
| `tests/Feature/AuthSecurityTest.php` | 6 | Page de connexion, accueil, redirection invité, middleware CSRF, message d'erreur générique (mot de passe non divulgué), throttle après 5 échecs |
| `tests/Feature/RoleGateTest.php` | 4 | Gate `role` : rôle attendu, liste de rôles, rôle différent, utilisateur non authentifié |
| `tests/Unit/CommandePolicyTest.php` | 7 | `view` / `markDelivered` / `delete` / `assignLivreur` par rôle |
| `tests/Feature/ExampleTest.php` | 2 | Tests de base |

→ **Non destructifs** : aucune base de données réelle n'est modifiée par les tests.

---

## 5. Nettoyage du dépôt

- **2 560 fichiers obsolètes supprimés** : plugins AdminLTE inutilisés, anciens modèles/contrôleurs, images uploadées orphelines.
- `.gitignore` racine : exclusion des données locales des outils CLI.
- Commits finaux (locaux puis poussés sur GitHub) :

| Commit | Contenu |
|---|---|
| `a2f9e33` | Nettoyage : fichiers obsolètes, `.gitignore` |
| `4359941` | Optimisation et durcissement : lots F/C/D/E/G, sécurité, base, tests |
| `d556cb2` | Vues, assets et documentation (README, SECURITE.md) |

---

## 6. Documentation produite

| Fichier | Contenu |
|---|---|
| `README.md` | Documentation détaillée du projet (installation, architecture, rôles) |
| `SECURITE.md` | Plan de sécurisation : audit, phases, check-list, annexes |
| `AMELIORATIONS.md` | Ce fichier |
