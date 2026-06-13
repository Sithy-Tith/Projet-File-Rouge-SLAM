# SuperPlumber

Application web de gestion d'interventions plomberie développée avec Symfony

---

## Prérequis

- PHP 8.2+
- Composer
- MySQL / MariaDB

---

## Installation

### 1. Cloner le projet

```bash
git clone https://github.com/Sithy-Tith/Projet-File-Rouge-SLAM.git
cd Projet-File-Rouge-SLAM/SuperPlumber
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Configurer l'environnement

Copiez le fichier `.env` et configurez la connexion à la base de données :

```bash
cp .env .env.local
```

Modifiez `.env.local` :

```env
DATABASE_URL="mysql://utilisateur:motdepasse@127.0.0.1:3306/superplumber_db?serverVersion=8.0"
```

### 4. Créer la base de données

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5. Charger les données de test

```bash
php bin/console doctrine:fixtures:load
```

### 6. Lancer le serveur

```bash
symfony serve
```

L'application est accessible sur `http://localhost:8000`

---

## Comptes de test

| Rôle | Email | Mot de passe |
|---|---|---|
| Administrateur | admin@test.com | admin123 |
| Plombier | plombier@test.com | plombier123 |
| Client | client@test.com | client123 |

---

## Stack technique

- **Backend** : Symfony 6.4 (PHP 8.2)
- **Base de données** : MariaDB / MySQL avec Doctrine ORM
- **Frontend** : Twig, Bootstrap 5, FullCalendar 5
- **Sécurité** : Symfony Security, tokens CSRF
- **Gestion des dépendances** : Composer

---

## Structure du projet

```
SuperPlumber/
├── src/
│   ├── Controller/     # Controllers Symfony
│   ├── Entity/         # Entités Doctrine
│   ├── Form/           # Formulaires Symfony
│   ├── Repository/     # Requêtes personnalisées
│   ├── Enum/           # Enums PHP (Status, Type, Position)
│   └── Security/       # Authentification et autorisations
├── templates/          # Templates Twig
├── migrations/         # Migrations Doctrine
├── config/             # Configuration Symfony
└── public/             # Point d'entrée de l'application
```

---

## Fonctionnalités principales

- Authentification multi-rôles (Admin, Plombier, Client)
- Gestion des interventions avec statuts colorés
- Attribution des interventions aux plombiers selon leurs disponibilités
- Planning interactif avec FullCalendar (drag & drop)
- Gestion de l'inventaire des pièces avec alertes de stock
- Switch user pour l'administrateur
- Dashboard personnalisé par rôle

---

## Problèmes connus

Si la commande `doctrine:migrations:migrate` échoue, lancez :

```bash
php bin/console doctrine:migrations:sync-metadata-storage
php bin/console doctrine:migrations:migrate
```

Si les tables n'existent pas :

```bash
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

## Équipe

| Nom | Rôle | GitHub |
|---|---|---|
| Aryen | Développeur | [@Sithy-Tith](https://github.com/Sithy-Tith) |
| Ilan | Développeur | [@ilan-github](https://github.com/ilanven) |
