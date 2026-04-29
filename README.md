# 🚀 KH LIVRAISON V2 - PROFESSIONNEL

Site web moderne de service de livraison avec gestion complète des livraisons.

## ✨ NOUVEAUTÉS V2

### Design Professionnel
- Interface moderne et épurée
- Animations fluides
- Responsive design optimisé
- Typographie distinctive (Outfit + DM Sans)

### Fonctionnalités
- ✅ Page d'authentification dédiée (connexion/inscription)
- ✅ Dashboard de gestion des livraisons (CRUD complet)
- ✅ Page de contact simplifiée
- ✅ Système de statuts des livraisons
- ✅ Filtres et recherche
- ✅ Statistiques en temps réel

## 📁 STRUCTURE

```
kh_livraison_v2/
├── index.html              Page d'accueil moderne
├── auth.html               Connexion/Inscription
├── livraisons.html         Gestion des livraisons
├── contact.html            Formulaire de contact
├── css/style.css           Design moderne
├── js/
│   ├── main.js            Script principal
│   ├── auth.js            Authentification
│   ├── livraisons.js      Gestion CRUD
│   └── contact.js         Formulaire contact
├── php/
│   ├── config.php         Configuration BDD
│   ├── login.php          Connexion
│   ├── register.php       Inscription
│   ├── get_livraisons.php Récupérer livraisons
│   ├── add_livraison.php  Ajouter livraison
│   ├── update_livraison.php Modifier livraison
│   ├── delete_livraison.php Supprimer livraison
│   └── contact.php        Formulaire contact
├── sql/kh_livraison.sql   Base de données
└── images/                Vos images
```

## 🚀 INSTALLATION

### 1. Prérequis
- XAMPP (Apache + MySQL)
- Navigateur moderne

### 2. Installation (3 étapes)
```bash
# 1. Copier dans htdocs
C:\xampp\htdocs\kh_livraison_v2\

# 2. Démarrer XAMPP
Apache ✅ MySQL ✅

# 3. Importer la base
http://localhost/phpmyadmin
→ Importer : sql/kh_livraison.sql
```

### 3. Tester
```
http://localhost/kh_livraison_v2/index.html
```

## 📊 GESTION DES LIVRAISONS

### Créer une livraison
1. Se connecter via `auth.html`
2. Aller sur `livraisons.html`
3. Cliquer sur "Nouvelle livraison"
4. Remplir le formulaire
5. Enregistrer

### Modifier/Supprimer
- Cliquer sur les icônes d'action dans le tableau
- Confirmer l'action

### Filtrer
- Par statut (en attente, en livraison, etc.)
- Par service (repas, colis, pro)
- Par recherche (adresse, ville, code)

## 🎨 PERSONNALISATION

### Couleurs (dans style.css)
```css
:root {
    --primary: #FF3B30;      /* Rouge principal */
    --secondary: #1A1A1A;    /* Noir */
    --accent: #FFD60A;       /* Jaune */
}
```

### Images requises
Ajoutez dans `/images/` :
- logo (2).png
- image-accueil.png
- photo-about.jpg
- service-1.jpg
- service-2.jpg
- service-3.png
- Designer.png

## 🔒 SÉCURITÉ

- Mots de passe hashés (Bcrypt)
- Requêtes préparées (PDO)
- Protection CSRF (headers)
- Validation serveur
- Sessions sécurisées

## 📞 COMPTE TEST

```
Email: jean.dupont@test.fr
Mot de passe: test123
```

## 🎯 PAGES

- **/** - Accueil avec services
- **/auth.html** - Connexion/Inscription
- **/livraisons.html** - Dashboard livraisons
- **/contact.html** - Formulaire contact

## 💡 CONSEILS

- Toujours tester en `http://localhost/...`
- Vérifier que Apache et MySQL sont démarrés
- Consulter la console (F12) en cas d'erreur
- Vérifier les permissions des dossiers

## ✅ CHECKLIST

- [ ] XAMPP installé et démarré
- [ ] Base de données importée
- [ ] Images ajoutées
- [ ] Site accessible
- [ ] Connexion fonctionnelle
- [ ] CRUD livraisons opérationnel

---

**Version 2.0 - Mars 2025**
**KH Livraison - Service professionnel**
