# Laravel Crud Generator

Ce package permet de générer les fichiers d'une section CRUD avec le framework Laravel 5.

## Installation

Inclure le package avec Composer :

```
composer require axn/laravel-crud-generator
```

Ajouter le service provider au tableau des providers dans `config/app.php` :

```
'Axn\CrudGenerator\ServiceProvider',
```

Publier si besoin les templates (stubs) du package via la commande :

```
// stubs
php artisan vendor:publish --tag=crud-generator.stubs
```

Les templates sont publiés dans `resources/stubs/vendor/crud-generator/`

Y faire les modifications souhaitées.

## Utilisation

Lancer la commande :

```
php artisan crud:generate <section> <model> [--stubs] [--langdir] [--viewsdir] [--breadcrumbs|-b]
```

**Arguments :**

* **section :** Nom de la section à créer. Les dossiers/noms/namespaces du contrôleur,
    des routes et des requêtes sont déterminés à partir de ce nom.

* **model :** Nom de la classe du modèle à injecter dans le contrôleur. Cette classe
    doit être existante ou une erreur sera levée. **Attention à bien mettre des quotes
    autour de la classe : les anti-slashs peuvent poser problème sinon.**

**Options :**

* **--stubs :** Permet de spécifier le groupe de templates à utiliser pour générer
    le contrôleur, le fichier de routes et les requêtes. Par défaut : 'default'.

* **--langdir :** Permet de spécifier un sous-répertoire dans lequel générer le fichier
    des traductions. Ce sous-répertoire est ajouté entre le chemin de basedes traductions
    en français (resources/lang/fr) et l'arborescence de la section. Par défaut : ''.

* **--viewsdir :** Permet de spécifier un sous-répertoire dans lequel générer les fichiers
    de vues. Ce sous-répertoire est ajouté entre le chemin de base des vues (resources/views/)
    et l'arborescence de la section. Par défaut : ''.

* **--breadcrumbs (ou -b) :** Si cette option est précisée, les breadcrumbs seront
    concaténés à la fin du fichier app/Http/breadcrumbs.php

Des questions sont de plus posées pour générer les textes en français :

- L'intitulé de la section, au singulier. Ex : "statut de commande".
- L'intitulé de la section, au pluriel. Ex : "statuts des commandes".
- L'intitulé est-il féminin ? y = oui ; n = non. Par défaut : n.

*Exemple concret d'utilisation :*

```
artisan crud:generate params.commande-statuts "App\Models\CommandeStatut" --viewsdir=modules
```

...qui génère les fichiers suivants :

```
app/
    Http/
        Controllers/
            Params/
                CommandeStatutsController.php
        Requests/
            Params/
                CommandeStatuts/
                    StoreRequest.php
                    UpdateRequest.php
                    UpdateContentRequest.php
        routes/
            params/
                commande-statuts.php
resources/
    lang/
        fr/
            params/
                commande-statuts.php
    views/
        modules/ <= viewsdir
            params/
                commandes-statuts/
                    index.blade.php
                    panel-list.blade.php
                    panel-create.blade.php
                    panel-edit.blade.php
                    form.blade.php
```

Attention à ne pas oublier d'inclure le fichier de routes :

```php
// app/Http/routes.php

require __DIR__.'/routes/params/commande-statuts.php';

// Ou en incluant automatiquement tous les fichiers présents dans le dossier "routes" :
foreach (File::allFiles(__DIR__.'/routes') as $file) {
    require $file->getRealPath();
}
```

### Options dans le contrôleur

Les fichiers générés peuvent être modifiés au besoin, mais des options sont proposées
dans le contrôleur (attribut `$options`) pour personnaliser rapidement la section
sans avoir à toucher au code :

- **creatable** : Pour afficher le formulaire de création à côté de la liste et autoriser la création.
- **editable** : Pour afficher le bouton de modification (qui amène au formulaire) dans la liste et autoriser la modification.
- **contentEditable** : Pour autoriser l'édition à la volée du libellé d'un enregistrement directement depuis la liste.
- **activatable** : Pour afficher le bouton actif/inactif dans la liste et autoriser l'activation/désactivation.
- **destroyable** : Pour afficher le bouton de suppression dans la liste et autoriser la suppression.

### Groupes de templates

Comme précisé dans les options de la commande, il est possible de créer différents
groupes de templates et d'ainsi choisir les templates à utiliser pour générer une section.
Par défaut (si l'option `--stubs` de la commande n'est pas renseignée), c'est le groupe
"default" fourni par le package qui est utilisé. Les groupes de templates doivent être
placés dans le répertoire de publication des templates du package.

Exemple :

```
resources/
    stubs/
        vendor/
            crud-generator/
                default/ <= Groupe par défaut
                params/  <= Groupe ajouté
```

Si un template n'existe pas dans un groupe, le fichier correspondant n'est tout simplement
pas généré. Pratique si l'on veut par exemple ne générer que le contrôleur pour un groupe.
