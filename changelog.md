Changelog
=========

2.15.0 (2020-03-04)
-------------------

- Add support for Laravel 7


2.14.0 (2020-01-23)
-------------------

- Move generated "Listing" classes into "Http\Listings" directory


2.13.1 (2020-01-21)
-------------------

- Use Illuminate\Support\Str class instead of deleted helpers


2.13.0 (2019-12-31)
-------------------

- Add support for Laravel 6


2.12.1 (2019-11-04)
-------------------

- Remove ending dot from tooltips translations


2.12.0 (2019-11-04)
-------------------

- Add some new translations


2.11.0 (2019-07-12)
-------------------

- Added "force" option
- Added generation of the "options" trait


2.10.0 (2019-03-07)
-------------------

- Added Laravel 5.8.* support


2.9.0 (2018-10-10)
------------------

- Added generation of the "Listing" class


2.8.0 (2018-09-07)
------------------

- Added Laravel 5.7.* support


2.7.0 (2018-07-04)
------------------

- Added Laravel 5.6.* support


2.6.4 (2018-04-13)
------------------

- Fixes on "adminlte-params-table" stubs set


2.6.3 (2018-04-12)
------------------

- Fixed typo


2.6.2 (2018-04-12)
------------------

- Added {{countZero}} {{countOne}} {{countMany}} lang replacement


2.6.1 (2018-04-12)
------------------

- Enhanced "adminlte-params-table" stubs set
- Added {{listSummarySimple}} lang replacement


2.6.0 (2018-04-11)
------------------

- Added new stubs set "adminlte-params-table"
- Added searchable abilities
- Added {{listSummary}} lang replacements


2.5.0 (2018-04-09)
------------------

- Added new stubs set "adminlte-params-list"
- Added {{createFormTitle}} and {{editFormTitle}} lang replacements


2.4.3 (2018-04-09)
------------------

- Added {{studlySectionBaseName}} common replacement


2.4.2 (2018-04-09)
------------------

- add {{sectionBaseName}} common replacement


2.4.1 (2018-04-07)
------------------

- Fixed handle routes path for Laravel >= 5.3


2.4.0 (2017-10-02)
------------------

- Added Laravel 5.5.* support


2.3.1 (2017-02-01)
------------------

- Fixed singleton method call


2.3.0 (2017-01-31)
------------------

- Added Laravel 5.4.* support


2.2.1 (2016-11-02)
------------------

- Move to Github


2.2.0 (2016-03-22)
------------------

- Source code released with the MIT license
- Added license file
- Use Route facade instead of route helper functions in stubs


2.1.0 (2016-01-21)
------------------

- Ajout des templates pour la présentation des listes en tableaux.
- Ajout de la clé de remplacement {{createButton}} dans le template de langue.
- Correction de la recherche du chemin d'un template ou d'un répertoire de templates.


2.0.3 (2016-01-12)
------------------

- Revert injection modèle dans requests (problématique).


2.0.2 (2016-01-12)
------------------

- Injection du modèle dans les requests.
- Ajout de la règle de validation d'unicité du libellé dans les requests.


2.0.1 (2016-01-07)
------------------

- Complétion du fichier composer.json


2.0.0 (2016-01-05)
------------------

- Questions posées uniquement si besoin.
- Vérification de la validité du modèle avant de poser les questions.
- Templates des vues en .stub
- Génération de chaque vue de manière unitaire au lieu d'une copie de tout le répertoire.
- Génération des requêtes en fonction des templates présents (nom de la classe = nom du template).
- Clé de remplacement {{requestNamespace}} dans le contrôleur au lieu des noms complets de chaque requête.
- Les clés de remplacement {{routeBaseAlias}}, {{langBaseKey}}, {{viewBaseName}} et {{modelClass}}
  peuvent être utilisées dans tous les templates (vues comprises).
- Auto-détection de la présence du champ "ordre" à la place de l'option "sortable".


1.0.0 (2015-12-23)
------------------

- First release.

