# RPG-Zero 🗡️🛡️

Un RPG rétro au tour par tour sur navigateur, inspiré des jeux web des années 2000 (*Mountyhall*, *Ogame*, *Shakes & Fidget*).

## 🚀 Stack Technique

* **PHP 8.3** (Architecture MVC légère, typage strict, PDO)
* **MariaDB 11** (Modèle de données relationnel)
* **HTMX 2** (Interactions asynchrones et combats sans rechargement de page)
* **CSS Dark Fantasy / Rétro** (Interface inspirée des jeux des années 2000)
* **Docker & Docker Compose** (Environnement de dev immédiat)

## 📦 Démarrage Rapide

1. Lancer les conteneurs :
   ```bash
   docker compose up -d --build
   ```
2. Accéder au jeu :
   * **Jeu :** [http://localhost:8000](http://localhost:8000)
   * **Administration BDD (Adminer) :** [http://localhost:8080](http://localhost:8080)
     * *Système :* MySQL / MariaDB
     * *Serveur :* `db`
     * *Utilisateur :* `rpg_user`
     * *Mot de passe :* `rpg_pass`
     * *Base de données :* `rpg_zero`

## 🎮 Fonctionnalités du Starter

* **Authentification & Personnages :** Inscription, connexion, choix de classe (Guerrier, Voleur, Mage).
* **Fiche de Héros :** Statistiques (PV, PA, Force, Agilité, Intelligence, Or, XP, Niveau).
* **Régénération Passive :** Calcul dynamique du temps écoulé pour recharger PV et PA.
* **La Ville & Taverne :** Repos pour récupérer ses points de vie.
* **Exploration & Combat :** Rencontre de monstres, calcul de dégâts/esquives/critiques, journal de combat tour par tour avec HTMX.
