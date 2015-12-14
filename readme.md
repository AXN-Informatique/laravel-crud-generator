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

Publier les vues, les traductions et les templates (stubs) du package via la commande :

```
php artisan vendor:publish
```

Les templates sont publiés dans `resources/stubs/vendor/crud-generator/`
Les vues sont publiées dans `resources/views/vendor/crud-generator/`
Les traductions sont publiées dans `resources/lang/packages/{lang}/crud-generator/`

Les modifier si nécessaire.

## Utilisation

Lancer la commande :

```
php artisan crud:generate {nom_section} {nom_classe_modèle}
```

**Attention à bien doubler les slashs dans le nom de classe du modèle !!**

Exemple :

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

Au besoin, les fichiers générés peuvent être modifiés, mais des options sont disponibles
dans le contrôleur (attribut `$options`) pour activer/désactiver rapidement des fonctions
de la section sans avoir à toucher au code :

- **creatable** : Indique si la section permet d'ajouter des enregistrements.
- **editable** : Indique si la section permet de modifier des enregistrements.
- **contentEditable** : Indique si la section permet l'édition à la volée du libellé d'un enregistrement.
- **activatable** : Indique si la section permet d'activer/désactiver un enregistrement.
- **sortable** : Indique si la section permet d'ordonner manuellement les enregistrements.
- **destroyable** : Indique si la section permet de supprimer des enregistrements.

Par défaut, la section utilise les vues et traductions publiées via la commande
`vendor:publish`, mais il est possible de demander à la section d'utiliser ses
propres vues et traductions.

Par exemple, pour que la section "params.commande-statuts" utilise sa propre vue
"form", il suffit de créer le fichier suivant :

```
resources/
    views/
        params/
            commande-statuts/
                form.blade.php
```

De même pour les traductions :

```
resources/
    lang/
        fr/
            params/
                commande-statuts.php
```

Concernant les traductions, ne renseigner que les clés à personnaliser. Exemple :

```php
// resources/lang/fr/params/commande-statuts.php

return [
    'list_title' => "Liste des statuts des commandes",
    'edit_title' => "Modification d'un statut de commande"
    // Et pour le reste, la section utilisera les traductions par défaut.
];
```
