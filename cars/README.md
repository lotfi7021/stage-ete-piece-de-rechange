<h1 align="center">🚗 SelectCars</h1>

<p align="center">
  <strong>Plateforme e-commerce B2B/B2C de pièces automobiles et de machines</strong><br>
  Projet de Fin d'Études (PFE) — Laravel 10
</p>

<p align="center">
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-10.x-FF2D20?logo=laravel&logoColor=white">
  <img alt="PHP" src="https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php&logoColor=white">
  <img alt="MySQL" src="https://img.shields.io/badge/MySQL-base%20de%20donn%C3%A9es-4479A1?logo=mysql&logoColor=white">
  <img alt="Licence" src="https://img.shields.io/badge/licence-acad%C3%A9mique-lightgrey">
</p>

<p align="center">
  <a href="#a-propos">À propos</a> •
  <a href="#fonctionnalites">Fonctionnalités</a> •
  <a href="#roles-et-permissions">Rôles</a> •
  <a href="#stack-technique">Stack technique</a> •
  <a href="#installation">Installation</a> •
  <a href="#comptes-de-demonstration">Comptes</a> •
  <a href="#structure-du-projet">Structure</a> •
  <a href="#base-de-donnees">Base de données</a> •
  <a href="#routes-principales">Routes</a> •
  <a href="#securite">Sécurité</a> •
  <a href="#tests-et-qualite">Tests</a>
</p>

---

<a id="a-propos"></a>

## À propos

**SelectCars** est une application web de gestion et de vente de pièces automobiles et de machines, destinée à couvrir l'ensemble du cycle commercial :

- **Catalogue** de produits organisé par marque, modèle et catégorie.
- **Recherche** par marque, modèle et fourchette de prix, avec chargement AJAX des modèles.
- **Commandes** avec lignes de détail, affectation d'un livreur et suivi de livraison.
- **Facturation** avec génération et aperçu de facture.
- **Stock** par produit.
- **SAV** via un système de tickets de support.
- **Back-office** complet : dashboard, calendrier, gestion des comptes et contenus éditoriaux.

Ce dépôt correspond à un **Projet de Fin d'Études (PFE)**. La documentation de durcissement est disponible dans [SECURITE.md](SECURITE.md) et le récapitulatif des optimisations dans [AMELIORATIONS.md](AMELIORATIONS.md).

---

<a id="fonctionnalites"></a>

## Fonctionnalités

### Front Office (public)

| Page | URL | Description |
|------|-----|-------------|
| Accueil | `/` | Slider de bannières, formulaire de recherche, produits mis en avant |
| Catalogue | `/machine` | Liste publique des machines et pièces |
| Recherche | `/search` | Filtrage par marque, modèle et fourchette de prix (modèles chargés en AJAX) |
| FAQ | `/FAQ` | Questions/réponses publiques |
| Contact | `/contact-US` | Formulaire de contact avec enregistrement en base et notification par e-mail |

### Back Office (authentifié)

| Module | Description |
|--------|-------------|
| Dashboard | Statistiques ventes, produits et tickets SAV (`/admin/dashboard`) |
| Calendrier | Visualisation des commandes planifiées (livreur) |
| Pièces / machines | CRUD complet, upload d'images, vérification AJAX de l'unicité de la référence |
| Marques / Modèles / Catégories | CRUD complet |
| Stock | Consultation et mise à jour des quantités par produit |
| Clients | Gestion des comptes clients (`admins` avec rôle `client`) |
| Livreurs | Gestion des comptes livreurs (`admins` avec rôle `livreur`) |
| Commandes | Création, affectation d'un livreur, marquage « livré », suppression, facture et aperçu |
| SAV | Consultation et clôture des tickets créés par les clients |
| Bannières | Gestion du slider de la page d'accueil |
| FAQ | Gestion du contenu de la FAQ |
| Contacts | Consultation et suppression des messages reçus |
| Comptes | Création, édition et désactivation des comptes administrateurs |

---

<a id="roles-et-permissions"></a>

## Rôles et permissions

L'authentification repose sur le **guard de session `admin`** (table `admins`). Le contrôle d'accès combine les middlewares `admin` et `role` (`EnsureRole`), le middleware `revalidate` (anti-retour-arrière) et la Gate `role`.

| Rôle | Accès |
|------|-------|
| **admin** | Accès total au back-office et à la gestion des contenus |
| **client** | Catalogue client, création de commandes, consultation des commandes et factures, création de tickets SAV |
| **livreur** | Dashboard livreur, calendrier, consultation des commandes, marquage « livré » |

| Groupe de routes | Middleware | Contenu |
|------------------|------------|---------|
| Gestion complète | `role:admin` | Dashboard, CRUD produits/marques/modèles/catégories, stock, clients, livreurs, comptes, FAQ, bannières, contacts, SAV (gestion) |
| Cache | `role:admin,livreur` | `POST /clear-cache` |
| Consultation des commandes | `role:admin,client,livreur` | Liste des commandes, facture, aperçu de facture |
| Création de ticket SAV | `role:admin,client` | `/admin/support/add` |
| Création de commande | `role:admin,client` | `/admin/list_commandeM/add` |
| Catalogue client | `role:client` | `/client/list_machine` et détail produit |
| Livraison | `role:livreur` | Dashboard livreur, calendrier, marquage « livré » |

> Toutes les routes protégées sont regroupées sous `['admin', 'revalidate']` dans `routes/web.php`.

---

<a id="stack-technique"></a>

## Stack technique

### Backend

| Composant | Version (contrainte) |
|-----------|----------------------|
| PHP | `^8.1` |
| Laravel | `^10.10` |
| Laravel UI (authentification) | `^4.4` |
| Yajra Laravel DataTables | `^10.11` |
| Laravel Tinker | `^2.8` |
| Guzzle | `^7.2` |
| Base de données | MySQL (`mysql`) |

### Frontend

| Composant | Version (contrainte) |
|-----------|----------------------|
| Blade (templates) | Laravel 10 |
| Bootstrap | `^5.2.3` |
| AdminLTE (thème back-office) | Assets embarqués dans `public/assets` |
| Vite | `^5.0` |
| SASS | `^1.56` |
| laravel-vite-plugin | `^1.0` |
| Axios | `^1.6.4` |
| Popper | `^2.11.6` |
| jQuery / AJAX, Swiper, AOS, noUiSlider, Nice Select | Bibliothèques front embarquées |

### Qualité / Développement

| Outil | Version (contrainte) |
|-------|----------------------|
| PHPUnit | `^10.1` |
| Laravel Pint | `^1.0` |
| FakerPHP | `^1.9.1` |
| Mockery | `^1.4.4` |
| Collision | `^7.0` |
| Ignition | `^2.0` |

> L'application est exclusivement web : l'authentification utilise la session, aucune API à jeton n'est exposée.

---

<a id="installation"></a>

## Installation

### Prérequis

- **PHP 8.1+** avec les extensions `mbstring`, `openssl`, `pdo_mysql`, `fileinfo`, `tokenizer`, `xml`, `ctype`, `json`
- **Composer 2**
- **Node.js 18+** et **npm**
- **MySQL 5.7+** ou **MariaDB**

### Étapes

Toutes les commandes s'exécutent depuis le dossier applicatif **`cars/`**.

```bash
# 1. Cloner le dépôt
git clone https://github.com/lotfi7021/stage-ete-3eme-anne.git
cd stage-ete-3eme-anne/cars

# 2. Installer les dépendances PHP
composer install

# 3. Installer les dépendances JS
npm install

# 4. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 5. Créer la base MySQL (nom par défaut : selectcars) puis renseigner DB_* dans .env

# 6. Migrations et données de démonstration
php artisan migrate --seed

# 7. Compiler les assets
npm run build

# 8. Lancer le serveur de développement
php artisan serve
```

L'application est alors accessible sur `http://127.0.0.1:8000` et le back-office sur `/admin`.

### Adaptation pour le développement local

Le fichier `.env.example` est **pré-configuré pour la production**. En local, modifiez au minimum :

| Variable | Valeur locale |
|----------|---------------|
| `APP_ENV` | `local` |
| `APP_DEBUG` | `true` |
| `APP_URL` | `http://127.0.0.1:8000` |
| `SESSION_SECURE_COOKIE` | `false` (pas de HTTPS en local) |

`SESSION_ENCRYPT=true` peut être conservé ; il nécessite simplement un `APP_KEY` généré.

### Messagerie en développement

Le projet est configuré pour [Mailpit](https://github.com/axllent/mailpit) (`MAIL_MAILER=smtp`, `MAIL_HOST=mailpit`, `MAIL_PORT=1025`) afin de tester les notifications du formulaire de contact sans envoyer de vrais e-mails.

---

<a id="comptes-de-demonstration"></a>

## Comptes de démonstration

Les comptes sont créés par `database/seeders/adminSeeder.php`, exécuté via `php artisan migrate --seed` ou :

```bash
php artisan db:seed --class=adminSeeder
```

| Rôle | Login | E-mail |
|------|-------|--------|
| Admin | `admin` | admin@cars.com |
| Client | `user` | user@cars.com |
| Livreur | `livreur` | livreur@cars.com |

**Mot de passe** : défini par `SEED_ADMIN_PASSWORD`, `SEED_CLIENT_PASSWORD` ou `SEED_LIVREUR_PASSWORD` dans `.env`. Si la variable est vide, un mot de passe aléatoire de 16 caractères est généré et **affiché une seule fois dans la console** pendant le seed. Les mots de passe sont stockés hachés (bcrypt) ; le seeder utilise `updateOrInsert` et ne duplique pas les comptes existants.

---

<a id="structure-du-projet"></a>

## Structure du projet

```
cars/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # AdminController (login, dashboard, comptes, livreurs)
│   │   │   ├── Front/          # Home, FAQ, Contact, Catégorie, Banner, Dash
│   │   │   ├── Auth/           # Authentification (Laravel UI)
│   │   │   └── …               # GestionMachine, MarqueMachine, ModelMachine, Stock,
│   │   │                       # Client, CommandeMachine, Support, Cache
│   │   ├── Middleware/         # Admin, EnsureRole, RevalidateBackHistory, SecurityHeaders…
│   │   └── Requests/           # StoreAdminRequest, StoreClientRequest, StoreLivreurRequest
│   ├── Mail/                   # ContactMail
│   ├── Models/                 # Admin, CommandeM, DetailCommandeM, GestionMachine,
│   │                           # MarqueMachine, ModelMachine, StockMachine, Categorie,
│   │                           # Support, Banner, Faq, Contact
│   ├── Policies/               # CommandePolicy
│   ├── Providers/              # RouteServiceProvider (rate limiter « login »), Auth…
│   └── Rules/                  # ReCaptcha
├── config/                     # Configuration Laravel (auth, session, database…)
├── database/
│   ├── migrations/             # Schéma complet (métier + sessions + index)
│   ├── factories/              # UserFactory
│   └── seeders/                # adminSeeder, CategoriesSeeder, MarquesSeeder, données fictives
├── public/                     # Point d'entrée + assets compilés + images uploadées
├── resources/
│   ├── views/
│   │   ├── back/               # Back-office (dashboard, CRUD, commandes, support…)
│   │   ├── front/              # Vues publiques (home, search, faq, contact)
│   │   ├── layouts/            # Layouts partagés front/back
│   │   ├── auth/               # Vues d'authentification
│   │   └── vendor/             # Vues publiées (pagination Bootstrap)
│   └── …
├── routes/
│   ├── web.php                 # Routes publiques et protégées par rôle
│   ├── api.php                 # Aucune route API exposée
│   ├── channels.php
│   └── console.php
├── tests/
│   ├── Feature/                # AuthSecurityTest, RoleGateTest, ExampleTest
│   └── Unit/                   # CommandePolicyTest, ExampleTest
├── SECURITE.md                 # Audit et plan de sécurisation
├── AMELIORATIONS.md            # Récapitulatif des optimisations
├── composer.json
└── package.json
```

---

<a id="base-de-donnees"></a>

## Base de données

### Tables

| Table | Rôle |
|-------|------|
| `admins` | Comptes utilisateurs du guard `admin` (rôles `admin`, `client`, `livreur`) |
| `gestion_machines` | Catalogue produits/pièces (référence, titre, description, image, prix, quantité, marque, modèle) |
| `marque_machines` | Marques de produits |
| `model_machines` | Modèles de produits (liés à une marque) |
| `categories` | Catégories de produits |
| `stock_machines` | Suivi de stock par produit |
| `commande__m_s` | Commandes (référence, client, livreur, prix, TVA, dates, état, paiement) |
| `detail_commande_m_s` | Lignes de commande (référence, produit, prix, quantité) |
| `supports` | Tickets SAV |
| `contacts` | Messages du formulaire de contact |
| `banners` | Bannières du slider d'accueil |
| `faqs` | Questions/réponses de la FAQ |
| `settings` | Paramètres de l'application |
| `sessions` | Sessions Laravel (driver `database`) |
| `clients` | Table héritée — la gestion actuelle des clients passe par `admins` + rôle |
| `livreurs` | Table héritée — la gestion actuelle des livreurs passe par `admins` + rôle |
| `users`, `password_reset_tokens`, `password_resets`, `failed_jobs`, `personal_access_tokens` | Tables Laravel standard |

### Relations Eloquent principales

| Modèle | Relations |
|--------|-----------|
| `GestionMachine` | `belongsTo` `MarqueMachine` (`marque_id`), `belongsTo` `ModelMachine` (`model_id`), `hasOne` `StockMachine` (`id_produit`), `hasMany` `DetailCommandeM` (`id_produit`) |
| `CommandeM` | `belongsTo` `Admin` — client (`id_client`), `belongsTo` `Admin` — livreur (`id_livreur`), `hasMany` `DetailCommandeM` (`ref_commande`) |
| `DetailCommandeM` | `belongsTo` `CommandeM` (`ref_commande`), `belongsTo` `GestionMachine` (`id_produit`) |
| `MarqueMachine` | `hasMany` `GestionMachine` |
| `Support` | `belongsTo` `Admin` (`id_client`) |
| `Admin` | `hasMany` `CommandeM` |

---

<a id="routes-principales"></a>

## Routes principales

### Routes publiques

| Méthode | URL | Nom | Description |
|---------|-----|-----|-------------|
| GET | `/` | `home` | Accueil |
| GET | `/machine` | `machine` | Catalogue public |
| GET | `/search` | `search` | Recherche (marque, modèle, prix — AJAX) |
| GET | `/FAQ` | `faq` | FAQ publique |
| GET | `/contact-US` | `contact.us.add` | Formulaire de contact |
| POST | `/contact-US/add` | `contact.us.store` | Envoi du formulaire (e-mail + base) |
| GET | `/admin` | `login.form` | Page de connexion du back-office |
| POST | `/admin/login-functionality` | `login.functionality` | Connexion — throttle 5 tentatives/minute |

### Back-office (extraits)

| Méthode | URL | Description |
|---------|-----|-------------|
| GET | `/admin/dashboard` | Dashboard administrateur |
| GET | `/admin/dashboard/vente/machine` | Statistiques machines |
| GET | `/admin/dashboard/produit` | Statistiques produits |
| GET | `/admin/dashboard/support` | Statistiques SAV |
| GET | `/admin/list_machine` | Liste des produits |
| GET/POST | `/admin/list_machine/add` | Ajout d'un produit |
| GET/POST | `/admin/list_machine/{id}/edit` · `/update` | Édition d'un produit |
| POST | `/admin/checkreference` | Vérification AJAX de l'unicité de la référence |
| GET | `/admin/list_marque` · `/admin/list_model` · `/admin/list_categories` | CRUD marques, modèles, catégories |
| GET | `/admin/list_stock/machine` | Consultation du stock |
| GET | `/admin/list_client` · `/admin/list_compte` | Gestion des clients et des comptes |
| GET | `/admin/list_livreurs` | Gestion des livreurs |
| GET | `/admin/list_commandeM` | Liste des commandes (DataTables) |
| GET/POST | `/admin/list_commandeM/add` | Création d'une commande |
| GET | `/admin/list_commandeM/{id}/facture` · `/facture/apercu` | Facture et aperçu |
| GET | `/admin/support` | Gestion des tickets SAV |
| GET/POST | `/admin/support/add` | Création d'un ticket SAV |
| GET | `/admin/list_faq` · `/admin/list_banners` · `/admin/list_contact` | Contenus éditoriaux |
| GET | `/client/list_machine` | Catalogue côté client |
| GET | `/admin/dashboardL` | Dashboard livreur |
| GET | `/admin/calendar` | Calendrier des commandes (livreur) |
| GET | `/admin/list_commandeM/{id}/mark-as-delivered` | Marquage « livré » (livreur) |
| POST | `/clear-cache` | Vidage du cache (admin + livreur) |
| GET | `/logoutSession` | Déconnexion |

---

<a id="securite"></a>

## Sécurité

Les mesures de durcissement sont détaillées dans **[SECURITE.md](SECURITE.md)** :

- **CSRF** : middleware `VerifyCsrfToken` actif dans le groupe `web`, sans exception.
- **Anti-brute-force** : rate limiter `login` (5 tentatives/minute par couple login + IP).
- **Mots de passe** : hachage bcrypt, politique de mot de passe forte, messages d'erreur génériques (anti-énumération), champ `password` masqué sur le modèle `Admin`.
- **Sessions** : driver `database`, contenu chiffré (`SESSION_ENCRYPT`), cookie `http_only`, `same_site=lax`, `secure` en HTTPS.
- **En-têtes HTTP** (`SecurityHeaders`) : CSP, `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, HSTS en HTTPS.
- **Contrôle d'accès** : middlewares `admin` et `role` (`EnsureRole`), Gate `role`, policy `CommandePolicy`.
- **Anti-retour-arrière** : middleware `RevalidateBackHistory` après déconnexion.
- **Anti-IDOR** : vérifications d'appartenance dans les policies et contrôleurs.

---

<a id="tests-et-qualite"></a>

## Tests et qualité

Le projet compte **19 tests** non destructifs (aucune écriture en base réelle) :

| Fichier | Tests | Couverture |
|---------|-------|------------|
| `tests/Feature/AuthSecurityTest.php` | 6 | Connexion, accueil, redirection des invités, CSRF, messages génériques, throttle |
| `tests/Feature/RoleGateTest.php` | 4 | Gate `role` (rôle attendu, liste, refus, invité) |
| `tests/Unit/CommandePolicyTest.php` | 7 | `view`, `markDelivered`, `delete`, `assignLivreur` par rôle |
| `tests/Feature/ExampleTest.php` · `tests/Unit/ExampleTest.php` | 2 | Tests de base |

```bash
# Lancer la suite de tests
php artisan test

# Vérifier le style de code
./vendor/bin/pint
```

---

<a id="commandes-utiles"></a>

## Commandes utiles

```bash
# Serveur de développement
php artisan serve

# Assets (développement avec HMR / production)
npm run dev
npm run build

# Migrations et seeders
php artisan migrate
php artisan migrate --seed
php artisan migrate:fresh --seed              # Réinitialise la base + seed
php artisan db:seed --class=adminSeeder       # (Re)crée les comptes de démonstration
php artisan db:seed --class=CategoriesSeeder
php artisan db:seed --class=MarquesSeeder
php artisan db:seed --class=ProduitFakeSeeder
php artisan db:seed --class=CommandeFakeSeeder
php artisan db:seed --class=ClientCompteFakeSeeder
php artisan db:seed --class=ContactMessageFakeSeeder
php artisan db:seed --class=SavFakeSeeder

# Tests et qualité
php artisan test
./vendor/bin/pint

# Cache
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
```

---

<a id="variables-environnement"></a>

## Variables d'environnement spécifiques

| Variable | Description |
|----------|-------------|
| `DB_CONNECTION` · `DB_HOST` · `DB_PORT` · `DB_DATABASE` · `DB_USERNAME` · `DB_PASSWORD` | Connexion MySQL (base par défaut : `selectcars`) |
| `SESSION_DRIVER=database` | Sessions stockées dans la table `sessions` |
| `SESSION_LIFETIME=60` | Durée de vie de la session (minutes) |
| `SESSION_ENCRYPT=true` | Chiffrement du contenu de session (nécessite `APP_KEY`) |
| `SESSION_SECURE_COOKIE=true` | Cookie `Secure` — à passer à `false` hors HTTPS |
| `SEED_ADMIN_PASSWORD` · `SEED_CLIENT_PASSWORD` · `SEED_LIVREUR_PASSWORD` | Mots de passe des comptes seedés (sinon aléatoire affiché en console) |
| `MAIL_MAILER` · `MAIL_HOST` · `MAIL_PORT` | SMTP (Mailpit en développement) |
| `APP_ENV` · `APP_DEBUG` | Environnement et affichage des erreurs |

> `http_only` et `same_site=lax` sont fixés dans `config/session.php`.

---

<a id="ameliorations"></a>

## Pistes d'amélioration

- **Double authentification (2FA)** pour les comptes administrateurs.
- **Sauvegardes chiffrées** et procédure de restauration documentée.
- **Environnement de production distinct** avec `APP_KEY` dédié et vérification TLS/HSTS réelle.
- **Observabilité** : journalisation centralisée et suivi des erreurs en production.

Le détail des optimisations déjà réalisées (sécurité, performance, qualité) est disponible dans [AMELIORATIONS.md](AMELIORATIONS.md).

---

## Licence

Projet de Fin d'Études (PFE) — usage académique.
