# Projet student

Cette appli permet de gérer une liste d'apprenants, leurs promos ainsi que leurs projets.

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
- name : varchar 190, unique
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
- creationDate : timestamp, default now
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
