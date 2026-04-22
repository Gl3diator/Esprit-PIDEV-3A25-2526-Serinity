# CSS Harmonisé — Serenity Sleep
## Guide d'intégration complet (A à Z)

---

## 📁 Fichiers créés

| Fichier | Rôle | Templates concernés |
|---|---|---|
| `tokens.css` | Variables globales (palette, typo, spacing) | Importé par tous |
| `admin.css` | Dashboard Admin Sleep + tous modules | `sleep/admin/base.html.twig` |
| `front.css` | Front Office Sleep, Exercice, Mood | `exercice/front/base_front.html.twig`, `mood/front/base_front.html.twig` |
| `home.css` | Landing page + navbar globale | `home/index.html.twig` |
| `auth.css` | Login, Register, Profil, User management | `access/access_control/layout.html.twig` |
| `serinity-custom.css` | Overrides finaux + compat Bootstrap/Argon | Chargé EN DERNIER partout |

---

## 🔗 Intégration dans chaque template BASE (sans toucher au Twig)

### 1. `sleep/admin/base.html.twig`
Remplacer la ligne :
```html
<link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
```
Par :
```html
<link rel="stylesheet" href="{{ asset('assets/css/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/serinity-custom.css') }}">
```

### 2. `exercice/admin/base_admin.html.twig` (dans `{% block stylesheets %}`)
Ajouter AVANT les autres liens argon :
```html
<link rel="stylesheet" href="{{ asset('assets/css/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
```
Ajouter EN DERNIER dans `{% block stylesheets %}` :
```html
<link rel="stylesheet" href="{{ asset('assets/css/serinity-custom.css') }}">
```

### 3. `mood/admin/base_admin.html.twig` (dans `{% block stylesheets %}`)
Ajouter EN DERNIER :
```html
<link rel="stylesheet" href="{{ asset('assets/css/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/serinity-custom.css') }}">
```

### 4. `exercice/front/base_front.html.twig`
Ajouter dans `{% block stylesheets %}` :
```html
<link rel="stylesheet" href="{{ asset('assets/css/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/front.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/serinity-custom.css') }}">
```

### 5. `mood/front/base_front.html.twig`
Même chose :
```html
<link rel="stylesheet" href="{{ asset('assets/css/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/front.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/serinity-custom.css') }}">
```

### 6. `base.html.twig` (template racine)
Dans `{% block stylesheets %}`, ajouter :
```html
<link rel="stylesheet" href="{{ asset('assets/css/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">
```
Et EN DERNIER :
```html
<link rel="stylesheet" href="{{ asset('assets/css/serinity-custom.css') }}">
```

### 7. `access/access_control/layout.html.twig`
Ajouter dans `<head>` :
```html
<link rel="stylesheet" href="{{ asset('assets/css/tokens.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/serinity-custom.css') }}">
```
> **Note** : `auth.css` remplace `assets/styles/app.css`. Garder app.css mais le charger AVANT auth.css.

---

## 🎨 Palette de couleurs unifiée

| Rôle | Couleur | Hex |
|---|---|---|
| **Teal (primaire)** | Accent principal | `#0ca5a9` |
| **Teal dark** | Hover / focus | `#0b7d80` |
| **Navy** | Fond admin | `#080e14` |
| **Gold** | Étoiles / accent | `#f4c642` |
| **Lavender** | Module Humeur | `#9b8fef` |
| **Emerald** | Module Exercice | `#10b981` |
| **Rose** | Consultation | `#f472b6` |
| **Orange** | Forum | `#fb923c` |
| **Danger** | Erreurs | `#ef4444` |
| **Success** | Succès | `#10b981` |

---

## 📐 Classes utilitaires clés

### Boutons
```html
<!-- Front office -->
<a class="sn-btn sn-btn-primary">Principal</a>
<a class="sn-btn sn-btn-outline">Secondaire</a>
<a class="sn-btn sn-btn-emerald">Exercice</a>

<!-- Admin -->
<button class="adm-btn adm-btn-primary">Admin</button>
<button class="adm-btn adm-btn-outline">Outline</button>
<button class="adm-btn adm-btn-danger">Supprimer</button>

<!-- Auth/dashboard -->
<button class="ac-btn ac-btn-primary">Connexion</button>
<button class="ac-ghost-btn">Annuler</button>
```

### Badges
```html
<!-- Admin -->
<span class="adm-badge adm-badge-teal">Actif</span>
<span class="adm-badge adm-badge-success">Bon</span>
<span class="adm-badge adm-badge-danger">Erreur</span>

<!-- Sommeil -->
<span class="sl-badge sl-badge-excellent">Excellente</span>
<span class="sl-badge sl-badge-mauvais">Mauvaise</span>

<!-- Mood -->
<span class="sn-mood-badge sn-mood-badge-positive">Positif</span>
```

### Cartes
```html
<!-- Admin -->
<div class="adm-card">
  <div class="adm-card-header">...</div>
  <div class="adm-card-body">...</div>
</div>

<!-- Auth/dashboard -->
<section class="ac-card">...</section>

<!-- Front -->
<div class="sn-hub-card">...</div>
```

### Tables
```html
<!-- Admin -->
<div class="adm-table-wrap">
  <table class="adm-table">...</table>
</div>

<!-- Sommeil front -->
<div class="sl-table-wrap">
  <table class="sl-table">...</table>
</div>

<!-- Mood front -->
<div class="sn-mood-table-wrap">
  <table class="sn-mood-table">...</table>
</div>
```

---

## 🔤 Typographie

- **Corps** : Inter (Google Fonts — chargé automatiquement)
- **Titres** : Playfair Display (Google Fonts — chargé automatiquement)

---

## 🌙 Thème Admin (dark) vs Front (light)

- **Admin** : variables `--adm-*` + fond `#080e14`
- **Front/Auth** : variables `--bg`, `--surface`, `--text` (clair)
- **Pas de `data-theme` nécessaire** : le dark est appliqué uniquement sur les layouts admin

---

## ⚡ Ordre de chargement recommandé

```
1. tokens.css       ← variables globales
2. [admin/front/home/auth].css  ← selon le template
3. serinity-custom.css  ← overrides finaux EN DERNIER
```

---

## ✅ Checklist vérification

- [ ] `tokens.css` chargé dans tous les bases templates
- [ ] `admin.css` dans `sleep/admin/base.html.twig`
- [ ] `admin.css` + `serinity-custom.css` dans `exercice/admin/base_admin.html.twig`
- [ ] `front.css` dans `exercice/front/base_front.html.twig`
- [ ] `front.css` dans `mood/front/base_front.html.twig`
- [ ] `home.css` dans `base.html.twig`
- [ ] `auth.css` dans `access/access_control/layout.html.twig`
- [ ] `serinity-custom.css` en dernier partout
