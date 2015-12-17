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

Publier si besoin les vues, les traductions et les templates (stubs) du package via les commandes :

```
// vues
php artisan vendor:publish --tag=views --provider=Axn\\CrudGenerator\\ServiceProvider

// traductions
php artisan vendor:publish --tag=lang --provider=Axn\\CrudGenerator\\ServiceProvider

// stubs
php artisan vendor:publish --tag=stubs --provider=Axn\\CrudGenerator\\ServiceProvider
```

Les templates sont publiés dans `resources/stubs/vendor/crud-generator/`
Les vues sont publiées dans `resources/views/vendor/crud-generator/`
Les traductions sont publiées dans `resources/lang/vendor/crud-generator/`

Y faire les modifications souhaitées.

## Utilisation

Lancer la commande :

```
php artisan crud:generate <section> <model> [--stubs]
```

**Arguments :**

* **section :** Nom de la section à créer. Les dossiers/noms/namespaces du contrôleur,
    des routes et des requêtes sont déterminés à partir de ce nom.

* **model :** Nom de la classe du modèle à injecter dans le contrôleur. Cette classe
    doit être existante ou une erreur sera levée. **Attention à bien doubler les slashs
    dans le nom !!**

**Options :**

* **--stubs :** Permet de spécifier le groupe de templates à utiliser pour générer
    le contrôleur, le fichier de routes et les requêtes. Par défaut : 'default'.


*Exemple concret d'utilisation :*

```
artisan crud:generate params.commande-statuts App\\Models\\CommandeStatut
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

Les fichiers générés peuvent être modifiés au besoin, mais des options sont disponibles
dans le contrôleur (attribut `$crud`) pour personnaliser rapidement la section sans avoir
à toucher au code (ces options sont disponibles dans les vues via la variable $crud) :

- **section** : Nom de la section (ce qui a été renseigné en argument dans la commande)
    qui sert notamment au nommage des routes. Par exemple pour récupérer la route "index"
    d'une section, faire : `route($crud['section'].'.index')`
- **viewsMainPath** : Chemin principal vers les vues de la section. Si une vue n'est pas trouvée
    à cette endroit, on ira la chercher dans `viewsDefaultPath`. Voir le helper `crud_view_name()`
    pour faire cette sélection.
- **viewsDefaultPath** : Chemin utilisé par défaut si vue non trouvée dans `viewsMainPath`.
- **langMainFile** : Chemin principal vers le fichier de traduction de la section. Si une traduction
    n'est pas trouvée à cette endroit, on ira la chercher dans `langDefaultFile`. Voir le helper
    `crud_trans_id()` pour faire cette sélection.
- **langDefaultFile** : Chemin utilisé par défaut si traduction non trouvée dans `langMainFile`.
- **creatable** : Indique si la section permet d'ajouter des enregistrements.
- **editable** : Indique si la section permet de modifier des enregistrements.
- **contentEditable** : Indique si la section permet l'édition à la volée du libellé d'un enregistrement.
- **activatable** : Indique si la section permet d'activer/désactiver un enregistrement.
- **sortable** : Indique si la section permet d'ordonner manuellement les enregistrements.
- **destroyable** : Indique si la section permet de supprimer des enregistrements.

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
                params/ <= Groupe ajouté
```

Si un template n'existe pas dans un groupe, le fichier correspondant n'est tout simplement
pas généré. Pratique si on veut par exemple ne générer que le contrôleur pour un groupe.
