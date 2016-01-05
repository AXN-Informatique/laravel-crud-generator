# Changelog for Laravel Crud Generator

## 2.0.0-dev

- Questions posées uniquement si besoin.
- Vérification de la validité du modèle avant de poser les questions.
- Templates des vues en .stub
- Génération de chaque vue de manière unitaire au lieu d'une copie de tout le répertoire.
- Génération des requêtes en fonction des templates présents (nom de la classe = nom du template).
- Clé de remplacement {{requestNamespace}} dans le contrôleur au lieu des noms complets de chaque requête.
- Les clés de remplacement {{routeBaseAlias}}, {{langBaseKey}}, {{viewBaseName}} et {{modelClass}}
  peuvent être utilisées dans tous les templates (vues comprises).
- Auto-détection de la présence du champ "ordre" à la place de l'option "sortable".

## 1.0.0 (2015-12-23)

- First release.
