# Changelog for Laravel Crud Generator

## 2.1.0 (2016-01-21)

- Ajout des templates pour la présentation des listes en tableaux.
- Ajout de la clé de remplacement {{createButton}} dans le template de langue.
- Correction de la recherche du chemin d'un template ou d'un répertoire de templates.

## 2.0.3 (2016-01-12)

- Revert injection modèle dans requests (problématique).

## 2.0.2 (2016-01-12)

- Injection du modèle dans les requests.
- Ajout de la règle de validation d'unicité du libellé dans les requests.

## 2.0.1 (2016-01-07)

- Complétion du fichier composer.json

## 2.0.0 (2016-01-05)

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
