# SupScan

## Description

**SupScan** est une application de bureau développée dans le cadre d'un stage d'observation au sein de **Suprajit Morocco**, destinée au département **Finance**.

Son objectif est de faciliter le traitement des factures fournisseurs grâce à l'intelligence artificielle. L'application extrait automatiquement les informations nécessaires à la saisie dans **SAP**, puis permet leur exportation au format **Excel (CSV)** afin de réduire les tâches manuelles, les erreurs de saisie et le temps de traitement.

---

# Fonctionnalités

- Importation d'une ou plusieurs factures (PDF ou image).
- Prévisualisation des documents.
- Extraction automatique des données grâce à **Google Gemini API**.
- Modification des informations extraites avant validation.
- Enregistrement des factures.
- Historique des sessions.
- Exportation des données vers Excel (CSV).
- Tableau de bord des statistiques.
- Application de bureau sous Windows.

---

# Architecture du projet

```text
SupScan/
│
├── ai/
│   ├── ai_extractor.py
│   ├── api.py
│   ├── pdf_processor.py
│   ├── validator.py
│   ├── SupScanAPI.spec
│   ├── .env
│   └── dist/
│
├── assets/
│   ├── SupScan.ico
│   ├── SupScan.png
│   ├── about.html
│   └── loading.html
│
├── electron/
│   ├── dist/
│   ├── node_modules/
│   ├── config.json
│   ├── database.js
│   ├── main.js
│   ├── preload.js
│   ├── package.json
│   └── package-lock.json
│
├── frontend/
│   ├── error_handling/
│   │   ├── error.php
│   │   ├── file_too_large.php
│   │   ├── network_error.php
│   │   ├── quota_limit.php
│   │   └── server_error.php
│   │
│   ├── imgs/
│   │   └── suprajitLogo.png
│   │
│   ├── js/
│   ├── main.js
│   ├── page1.php
│   ├── process_animation.php
│   ├── page3.php
│   ├── page4.php
│   ├── page5.php
│   ├── session.php
│   ├── statis.php
│   └── Sidebar.php
│
└── README.md
```

---

# Technologies utilisées

## Frontend

- HTML5
- CSS3
- JavaScript
- PHP
- Font Awesome
- Chart.js

## Intelligence artificielle

- Python
- Google Gemini API

## Application Desktop

- Electron
- SQLite3

## Bibliothèques Python

- Flask
- Flask-CORS
- Google Generative AI
- python-dotenv
- Pillow
- pdf2image
- python-dateutil
- PyPDF2
- PyInstaller

## Outils de développement

- Visual Studio Code
- Git
- GitHub
- Electron Builder

---

# Packages utilisés

## Electron

```bash
electron
electron-builder
sqlite3
get-port
```

## Python

```bash
Flask
Flask-CORS
google-generativeai
python-dotenv
Pillow
pdf2image
python-dateutil
PyPDF2
PyInstaller
poppler
```

---

# Fonctionnement

1. L'utilisateur importe une ou plusieurs factures.
2. Les documents sont envoyés au moteur d'intelligence artificielle.
3. Google Gemini analyse les factures.
4. Les informations importantes sont extraites automatiquement.
5. Les données sont affichées dans l'application.
6. L'utilisateur peut les modifier si nécessaire.
7. Les informations sont enregistrées localement.
8. Les données peuvent être exportées au format Excel (CSV) afin de faciliter leur intégration dans SAP.

---

# Informations extraites

SupScan extrait automatiquement :

- Fournisseur
- Référence de facture
- Date de facture
- Code TVA
- Montant
- Devise
- Description

---

# Installation

## Prérequis

- PHP
- Python 3.x
- Une clé Google Gemini API

---

## Installation des dépendances Electron

```bash
cd electron
npm install
```

---

## Installation des dépendances Python

```bash
pip install flask flask-cors python-dotenv pillow pdf2image python-dateutil PyPDF2 google-generativeai pyinstaller
```

---

## Configuration

Créer un fichier **.env** à la racine du dossier **ai/** :

```env
GEMINI_API_KEY=Votre_Clé_API
```

---

## Lancer l'API Python

```bash
python api.py
```

---

## Lancer l'application Desktop

```bash
cd electron
npm start
```

---

## Générer l'exécutable

```bash
npm run build
```

---

# Objectifs

- Automatiser le traitement des factures fournisseurs.
- Réduire les erreurs de saisie.
- Diminuer le temps de traitement.
- Faciliter le travail du département Finance.
- Générer rapidement un fichier Excel destiné au traitement dans SAP.

---

# Auteurs

Projet réalisé par :

- **Aya Hachimi**
- **Hafsa Loukili Mourino**

Projet développé dans le cadre d'un **stage d'observation** au sein de **Suprajit Morocco**.

---

# Remerciements

Nous remercions chaleureusement **Suprajit Morocco**, le département **Finance**, notre ingénieure IT **Mme Houda**, notre manager IT **M. Younossi**, ainsi que toutes les personnes qui nous ont accompagnées tout au long de ce stage d'observation et de la réalisation du projet **SupScan**.