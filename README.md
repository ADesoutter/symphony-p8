# Projet student

Cette appli permet de gérer une liste d'apprenants, leurs promos ainsi que leurs projets.

## Install

    git clone https://github.com/jibundeyare/src-symfony-p8
    cd src-symfony-p8
    composer install

Après install du projet, créez le fichier `.env.local` et ajoutez-y les variables `APP_ENV` et `DATABASE_URL`.

Créez la BDD avec PhpMyAdmin.

Ensuite créez le schéma de la BDD et injectez les données de test avec la commande :

    bin/dofilo.sh

## Utilisation

    symfony serve

Ensuite visitez la page [http://localhost:8000](http://localhost:8000).

## Cahier des charges

### Student

Cette classe représente un apprenant.

- id : primary key
- firstname : varchar 190
- lastname : varchar 190
- email : varchar 190, unique
- phone : varchar 190, nullable, unique
- creationDate : timestamp, default now
- modificationDate : timestamp, nullable, default now
- deletionDate : timestamp, nullable, default now

- projects : many to many
- schoolYear : many to one
- tags : many to many
- user : one to one

### SchoolYear

Cette classe représente une promo d'apprenants.

- id : primary key
- name : varchar 190
- description : text
- startDate : timestamp
- endDate : timestamp
- creationDate : timestamp, default now
- modificationDate : timestamp, nullable, default now
- deletionDate : timestamp, nullable, default now

- students : one to many
- teachers : many to many

### Project

Cette classe représente un projet réalisé par des apprenants.

- id : primary key
- name : varchar 190
- description : text, nullable
- deadline : timestamp, nullable
- budget : int, nullable
- creationDate : timestamp, default now
- modificationDate : timestamp, nullable, default now
- deletionDate : timestamp, nullable, default now

- clients : many to many
- students : many to many
- teacher : many to one, nullable
- tags : many to many

### Client

Cette classe représente un commanditaire d'un projet.

- id : primary key
- firstname : varchar 190
- lastname : varchar 190
- phone : varchar 190, nullable, unique
- creationDate : timestamp, default now
- modificationDate : timestamp, nullable, default now
- deletionDate : timestamp, nullable, default now

- projects : many to many
- user : one to one, uni-directional

### Tag

Cette classe représente une étiquette que l'on pourra associer à un apprenant, un formateur ou un projet.

- id : primary key
- name : varchar 1ECF - Part 1 - Projet bibliothèque - BDD
Daishi Kaszer
•
1 juin
100 points
# ECF - Part 1 - Projet bibliothèque - BDD

Le but de cet exercice est de maîtriser la création d'une base de données (BDD) qui sera utilisée dans une application web dynamique.

## Cahier des charges

Vous devez créer une BDD qui implémente la structure et les données indiquées plus bas.

Attention : l'accès à la BDD doit être limité à un unique utilisateur ayant le minimum possible de privilèges.

Pour créer la BDD, vous avez le choix des armes : SQL vanila, PHPMyAdmin, Doctrine (Symfony), Eloquent (Laravel), etc.
Mais vous êtes vivement encouragé à utiliser Symfony.

## Livrables

La BDD et les données doivent être livrées sous la forme d'un repository git en ligne sur un site comme github, gitlab ou autre.

Vous avez deux options : soit vous créez la BDD en utilisant un framework PHP soit vous la créez sans utiliser de framework PHP.

En fonction de votre choix, le repository doit contenir les fichiers suivants :

1. avec framework PHP
  - un fichier `README.md` (voir ci-dessous)
  - un ou des fichiers PHP contenant le code de création de la BDD
  - un ou des fichiers PHP contenant le code de création des données indispensables
  - un ou des fichiers PHP contenant le code de création des données de test
2. sans framework PHP
  - un fichier `README.md` (voir ci-dessous)
  - un fichier SQL contenant le code de création de l'utilisateur de la BDD
  - un fichier SQL contenant le code de création de la BDD
  - un fichier SQL contenant les données indispensables
  - un fichier SQL contenant les données de test

Dans tous les cas, le fichier `README.md` doit indiquer la procédure à suivre pour :

- si nécessaire, installer les dépendances (avec composer par exemple)
- créer l'utilisateur de la BDD
- créer la BDD
- créer la structure de la BDD
- injecter les données indispensables
- injecter les données de test

Optionnellement, vous pouvez aussi fournir un script Bash qui réalise chacune de ces opérations.

## Prérequis

- MariaDB
- PHPMyAdmin

Si vous utilisez Symfony :

- PHP 7.x ou 8.x
- composer90, unique
- description : text
- creationDate : timestamp, default now
- modificationDate : timestamp, nullable, default now
- deletionDate : timestamp, nullable, default now

- students : many to many
- projects : many to many
- teachers : many to many

### Teacher

Cette classe représente un formateur.

- id : primary key
- firstname : varchar 190
- lastname : varchar 190
- phone : varchar 190, nullable, unique
- creationDate : timestamp, de:fault now
- modificationDate : timestamp, nullable, default now
- deletionDate : timestamp, nullable, default now

- projects : one to many
- schoolYears : many to many
- tags : many to many
- user : one to one

### User

Cette classe représente un compte utilisateur qui peut se connecter à l'application.

Structure par défaut proposée par Symfony

- id : primary key
- email : varchar 190, unique
- password : varchar 190
- roles : text- creationDate : timestamp, default now
- modificationDate : timestamp, nullable, default now
- deletionDate : timestamp, nullable, default now

### Dépendances

- Client
   - User
- Project
- SchoolYear
- Student
  - SchoolYear
  - User
- Tag
- Teacher
  - User
- User
