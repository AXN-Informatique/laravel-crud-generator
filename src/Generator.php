<?php

namespace Axn\CrudGenerator;

use Illuminate\Support\Str;

class Generator
{
    /**
     * Nom de la section à générer.
     *
     * @var string
     */
    protected $section;

    /**
     * Namespace de base de l'application.
     *
     * @var string
     */
    protected $appNs;

    /**
     * Nom complet de la classe du modèle.
     *
     * @var string
     */
    protected $modelClass;

    /**
     * Nom du groupe de templates à utiliser.
     *
     * @var string
     */
    protected $stubsGroup;

    /**
     * Sous-répertoire dans lequel générer le fichier de langue.
     *
     * @var string
     */
    protected $langDir;

    /**
     * Sous-répertoire dans lequel générer les vues.
     *
     * @var string
     */
    protected $viewsDir;

    /**
     * Tableau des segments qui composent la section.
     *
     * @var array
     */
    protected $sectionSegments;

    /**
     * Idem $sectionSegments mais avec chaque segment en studly case.
     *
     * @var array
     */
    protected $sectionSegmentsStudly;

    /**
     * Force la génération d'un fichier même s'il existe déjà.
     *
     * @var boolean
     */
    protected $force = false;

    /**
     * Constructeur.
     *
     * @param string $section
     * @param string $modelClass
     * @param string $stubsGroup
     * @param string $langDir
     * @param string $viewsDir
     * @return void
     */
    public function __construct($section, $modelClass, $stubsGroup, $langDir = null, $viewsDir = null)
    {
        $explodedModelClass = explode('\\', $modelClass);

        $this->section = $section;
        $this->modelClass = $modelClass;
        $this->stubsGroup = $stubsGroup;
        $this->langDir = $langDir;
        $this->viewsDir = $viewsDir;

        $this->appNs = $explodedModelClass[0];
        $this->sectionSegments = explode('.', $section);
        $this->sectionSegmentsStudly = array_map('Str::studly', $this->sectionSegments);
    }

    /**
     * Définit s'il faut forcer ou pas la génération d'un fichier même s'il existe déjà.
     *
     * @param boolean $force
     */
    public function force($force)
    {
        $this->force = (bool) $force;
    }

    /**
     * Indique s'il faut forcer la génération d'un fichier même s'il existe déjà.
     *
     * @return boolean
     */
    public function forcing()
    {
        return $this->force;
    }

    /**
     * Indique s'il ne faut pas forcer la génération d'un fichier quand il existe déjà.
     *
     * @return boolean
     */
    public function notForcing()
    {
        return ! $this->force;
    }

    /**
     * Est-ce que le générateur devra générer le fichier des traductions ?
     *
     * @return boolean
     */
    public function shouldGenerateLang()
    {
        return $this->getLangStub() !== '' && ! is_file($this->getLangPath());
    }

    /**
     * Retourne les noms de tous les templates contenus dans un répertoire.
     *
     * @return array
     */
    public function getStubsNamesInDirectory($directoryName)
    {
        if (! $path = $this->getStubDirPath($directoryName)) {
            return [];
        }

        return array_map(function ($file) {
            return basename($file, '.stub');
        }, glob("$path/*.stub"));
    }

    // ------------------------------------------------------------------------
    // GENERATE
    // ------------------------------------------------------------------------

    /**
     * Génère le fichier du contrôleur.
     *
     * @return string
     */
    public function generateController()
    {
        $path = app_path('Http/Controllers/' . implode('/', $this->sectionSegmentsStudly) . 'Controller.php');

        if (is_file($path) && $this->notForcing()) {
            return '';
        }

        if (! $content = $this->getControllerContent()) {
            return '';
        }

        $this->createMissingDirs($path);

        return file_put_contents($path, $content) !== false ? $path : '';
    }

    /**
     * Génère le fichier d'options.
     *
     * @return string
     */
    public function generateOptionsTrait()
    {
        $path = app_path('Traits/' . implode('/', $this->sectionSegmentsStudly) . 'OptionsTrait.php');

        if (is_file($path) && $this->notForcing()) {
            return '';
        }

        if (! $content = $this->getOptionsTraitContent()) {
            return '';
        }

        $this->createMissingDirs($path);

        return file_put_contents($path, $content) !== false ? $path : '';
    }

    /**
     * Génère le fichier du listing.
     *
     * @return string
     */
    public function generateListing()
    {
        $path = app_path('Http/Listings/' . implode('/', $this->sectionSegmentsStudly) . 'Listing.php');

        if (is_file($path) && $this->notForcing()) {
            return '';
        }

        if (! $content = $this->getListingContent()) {
            return '';
        }

        $this->createMissingDirs($path);

        return file_put_contents($path, $content) !== false ? $path : '';
    }

    /**
     * Génère le fichier des routes.
     *
     * @return string
     */
    public function generateRoutes()
    {
        if (version_compare(app()->version(), '5.3.0', '<')) {
            $path = app_path('Http/routes/' . implode('/', $this->sectionSegments) . '.php');
        } else {
            $path = base_path('routes/web/' . implode('/', $this->sectionSegments) . '.php');
        }

        if (is_file($path) && $this->notForcing()) {
            return '';
        }

        if (! $content = $this->getRoutesContent()) {
            return '';
        }

        $this->createMissingDirs($path);

        return file_put_contents($path, $content) !== false ? $path : '';
    }

    /**
     * Génère le fichier des traductions.
     *
     * @param string $singular
     * @param string $plural
     * @param boolean $feminine
     * @return string
     */
    public function generateLang($singular, $plural, $feminine)
    {
        $path = $this->getLangPath();

        if (is_file($path) && $this->notForcing()) {
            return '';
        }

        if (! $content = $this->getLangContent($singular, $plural, $feminine)) {
            return '';
        }

        $this->createMissingDirs($path);

        return file_put_contents($path, $content) !== false ? $path : '';
    }

    /**
     * Génère le fichier d'une requête.
     *
     * @param string $name
     * @return string
     */
    public function generateRequest($name)
    {
        $path = app_path('Http/Requests/' . implode('/', $this->sectionSegmentsStudly) . '/' . Str::studly($name) . '.php');

        if (is_file($path) && $this->notForcing()) {
            return '';
        }

        if (! $content = $this->getRequestContent($name)) {
            return '';
        }

        $this->createMissingDirs($path);

        return file_put_contents($path, $content) !== false ? $path : '';
    }

    /**
     * Génère le fichier d'une vue.
     *
     * @param string $name
     * @return boolean
     */
    public function generateView($name)
    {
        $sectionSegments = $this->sectionSegments;

        if ($this->viewsDir) {
            array_unshift($sectionSegments, $this->viewsDir);
        }

        $path = base_path('resources/views/' . implode('/', $sectionSegments) . '/' . $name . '.php');

        if (is_file($path) && $this->notForcing()) {
            return '';
        }

        if (! $content = $this->getViewContent($name)) {
            return '';
        }

        $this->createMissingDirs($path);

        return file_put_contents($path, $content) !== false ? $path : '';
    }

    /**
     * Ajoute les breadcrumbs au fichier de breadcrumbs app/Http/breadcrumbs.php
     *
     * @param string $title
     * @param string $path
     * @return string
     */
    public function appendBreadcrumbs($title, $path = null)
    {
        if ($path === null) {
            $path = app_path('Http/breadcrumbs.php');
        }

        if (! file_exists($path)) {
            return '';
        }

        if (! $content = $this->getBreadcrumbsContent($title)) {
            return '';
        }

        return file_put_contents($path, $content, FILE_APPEND) !== false ? $path : '';
    }

    // ------------------------------------------------------------------------
    // GET CONTENTS
    // ------------------------------------------------------------------------

    /**
     * Retourne le contenu généré pour le contrôleur.
     *
     * @return string
     */
    protected function getControllerContent()
    {
        if (! $stub = $this->getControllerStub()) {
            return '';
        }

        $sectionSegmentsStudly = $this->sectionSegmentsStudly;

        $name = array_pop($sectionSegmentsStudly) . 'Controller';
        $namespace = $this->appNs . '\\Http\Controllers';
        $requestNs = $this->appNs . '\\Http\Requests\\' . implode('\\', $this->sectionSegmentsStudly);

        if ($sectionSegmentsStudly) {
            $namespace .= '\\' . implode('\\', $sectionSegmentsStudly);
        }

        return strtr($stub, array_merge($this->getCommonReplacements(), [
            '{{namespace}}' => $namespace,
            '{{name}}' => $name,
            '{{requestNamespace}}' => $requestNs
        ]));
    }

    /**
     * Retourne le contenu généré pour le trait d'options.
     *
     * @return string
     */
    protected function getOptionsTraitContent()
    {
        if (! $stub = $this->getOptionsTraitStub()) {
            return '';
        }

        $sectionSegmentsStudly = $this->sectionSegmentsStudly;

        $name = array_pop($sectionSegmentsStudly) . 'OptionsTrait';
        $namespace = $this->appNs . '\Traits';

        if ($sectionSegmentsStudly) {
            $namespace .= '\\' . implode('\\', $sectionSegmentsStudly);
        }

        return strtr($stub, array_merge($this->getCommonReplacements(), [
            '{{namespace}}' => $namespace,
            '{{name}}' => $name
        ]));
    }

    /**
     * Retourne le contenu généré pour le listing.
     *
     * @return string
     */
    protected function getListingContent()
    {
        if (! $stub = $this->getListingStub()) {
            return '';
        }

        $sectionSegmentsStudly = $this->sectionSegmentsStudly;

        $name = array_pop($sectionSegmentsStudly) . 'Listing';
        $namespace = $this->appNs . '\\Http\\Listings';

        if ($sectionSegmentsStudly) {
            $namespace .= '\\' . implode('\\', $sectionSegmentsStudly);
        }

        return strtr($stub, array_merge($this->getCommonReplacements(), [
            '{{namespace}}' => $namespace,
            '{{name}}' => $name
        ]));
    }

    /**
     * Retourne le contenu généré pour le fichier de routes.
     *
     * @return string
     */
    protected function getRoutesContent()
    {
        if (! $stub = $this->getRoutesStub()) {
            return '';
        }

        return strtr($stub, array_merge($this->getCommonReplacements(), [
            '{{baseUrl}}' => implode('/', $this->sectionSegments),
            '{{controller}}' => implode('\\', $this->sectionSegmentsStudly) . 'Controller'
        ]));
    }

    /**
     * Retourne le contenu généré pour les breadcrumbs.
     *
     * @param string $title
     * @return array
     */
    protected function getBreadcrumbsContent($title)
    {
        if (! $stub = $this->getBreadcrumbsStub()) {
            return '';
        }

        return strtr($stub, array_merge($this->getCommonReplacements(), [
            '{{title}}' => ucfirst($title)
        ]));
    }

    /**
     * Retourne le contenu généré pour le fichier des traductions (en français).
     *
     * @param string $singular
     * @param string $plural
     * @param boolean $feminine
     * @return array
     */
    protected function getLangContent($singular, $plural, $feminine)
    {
        if (! $stub = $this->getLangStub()) {
            return '';
        }

        $lcfSingular = lcfirst($singular);
        $lcfPlural = lcfirst($plural);
        $startsWithVowel = Str::startsWith($lcfSingular, [
            'a',
            'e',
            'i',
            'o',
            'u'
        ]);
        $lcfDefArticle = ($startsWithVowel ? "l’" : ($feminine ? 'la ' : 'le '));
        $ucfDefArticle = ucfirst($lcfDefArticle);
        $lcfUndefArticle = ($feminine ? 'une' : 'un');

        return strtr($stub, [
            '{{storeSuccess}}' => "{$ucfDefArticle}$lcfSingular a été " . ($feminine ? 'créée' : 'créé') . " avec succès.",
            '{{updateSuccess}}' => "{$ucfDefArticle}$lcfSingular a été " . ($feminine ? 'mise' : 'mis') . " à jour avec succès.",
            '{{enableSuccess}}' => "{$ucfDefArticle}$lcfSingular a été " . ($feminine ? 'activée' : 'activé') . " avec succès.",
            '{{disableSuccess}}' => "{$ucfDefArticle}$lcfSingular a été " . ($feminine ? 'désactivée' : 'désactivé') . " avec succès.",
            '{{sortSuccess}}' => "L’ordre des $lcfPlural a été modifié avec succès.",
            '{{archiveSuccess}}' => "{$ucfDefArticle}$lcfSingular a été " . ($feminine ? 'archivée' : 'archivé') . " avec succès.",
            '{{deleteSuccess}}' => "{$ucfDefArticle}$lcfSingular a été " . ($feminine ? 'supprimée' : 'supprimé') . " avec succès.",
            '{{restoreSuccess}}' => "{$ucfDefArticle}$lcfSingular a été " . ($feminine ? 'restaurée' : 'restauré') . " avec succès.",
            '{{destroySuccess}}' => "{$ucfDefArticle}$lcfSingular a été définitivement " . ($feminine ? 'supprimée' : 'supprimé') . " avec succès.",
            '{{destroyFailure}}' => "Suppression impossible : {$lcfDefArticle}$lcfSingular est peut-être " . ($feminine ? 'liée' : 'lié') . " à d’autres enregistrements.",

            '{{countZero}}' => ($feminine ? 'aucune' : 'aucun') . " $singular",
            '{{countOne}}' => "$lcfUndefArticle $singular",
            '{{countMany}}' => ":count $plural",
            '{{breadcrumbsIndex}}' => ucfirst($lcfPlural),
            '{{breadcrumbsList}}' => ucfirst($lcfPlural),

            '{{listTitle}}' => "Liste des $lcfPlural",
            '{{listSummary}}' => "Liste des :count $lcfPlural de <strong>:firstItem à :lastItem</strong> sur un total de :total",
            '{{listSummarySimple}}' => ":count $lcfPlural de <strong>:firstItem à :lastItem</strong> sur :total",
            '{{listEmpty}}' => "Il n’y a " . ($feminine ? 'aucune' : 'aucun') . " $lcfSingular.",

            '{{archivesTitle}}' => "Liste des $lcfPlural dans les archives",
            '{{archivesSummary}}' => "Liste des :count $lcfPlural dans les archives de <strong>:firstItem à :lastItem</strong> sur un total de :total",
            '{{archivesSummarySimple}}' => ":count $lcfPlural dans les archives de <strong>:firstItem à :lastItem</strong> sur :total",
            '{{archivesEmpty}}' => "Il n’y a " . ($feminine ? 'aucune' : 'aucun') . " $lcfSingular dans les archives.",

            '{{recycleBinTitle}}' => "Liste des $lcfPlural dans la corbeille",
            '{{recycleBinSummary}}' => "Liste des :count $lcfPlural dans la corbeille de <strong>:firstItem à :lastItem</strong> sur un total de :total",
            '{{recycleBinSummarySimple}}' => ":count $lcfPlural dans la corbeille de <strong>:firstItem à :lastItem</strong> sur :total",
            '{{recycleBinEmpty}}' => "Il n’y a " . ($feminine ? 'aucune' : 'aucun') . " $lcfSingular dans la corbeille.",

            '{{statusActive}}' => ($feminine ? trans('common::status.active_fem') : trans('common::status.active')),
            '{{statusInactive}}' => ($feminine ? trans('common::status.inactive_fem') : trans('common::status.inactive')),

            '{{createButton}}' => ($feminine ? 'Nouvelle' : ($startsWithVowel ? 'Nouvel' : 'Nouveau')) . " $lcfSingular",
            '{{editTooltip}}' => "Modifier {$lcfDefArticle}$lcfSingular «&nbsp;:name&nbsp;»",

            '{{enableTooltip}}' => "Activer {$lcfDefArticle}$lcfSingular «&nbsp;:name&nbsp;»",
            '{{disableTooltip}}' => "Désactiver {$lcfDefArticle}$lcfSingular «&nbsp;:name&nbsp;»",
            '{{archiveTooltip}}' => "Archiver {$lcfDefArticle}$lcfSingular «&nbsp;:name&nbsp;»",
            '{{deleteTooltip}}' => "Supprimer {$lcfDefArticle}$lcfSingular «&nbsp;:name&nbsp;»",
            '{{restoreTooltip}}' => "Restaurer {$lcfDefArticle}$lcfSingular «&nbsp;:name&nbsp;»",
            '{{destroyTooltip}}' => "Supprimer définitivement {$lcfDefArticle}$lcfSingular «&nbsp;:name&nbsp;»",

            '{{archiveConfirm}}' => "Êtes-vous sûr de vouloir archiver {$lcfDefArticle}$lcfSingular «&nbsp;:name&nbsp;»&nbsp;?",
            '{{deleteConfirm}}' => "Êtes-vous sûr de vouloir supprimer {$lcfDefArticle}$lcfSingular «&nbsp;:name&nbsp;»&nbsp;?",
            '{{restoreConfirm}}' => "Êtes-vous sûr de vouloir restaurer {$lcfDefArticle}$lcfSingular «&nbsp;:name&nbsp;»&nbsp;?",
            '{{destroyConfirm}}' => "Êtes-vous sûr de vouloir supprimer définitivement {$lcfDefArticle}$lcfSingular «&nbsp;:name&nbsp;»&nbsp;?",

            '{{createTitle}}' => "Création d’{$lcfUndefArticle} " . ($feminine ? 'nouvelle' : ($startsWithVowel ? 'nouvel' : 'nouveau')) . " $lcfSingular",
            '{{createFormTitle}}' => ($feminine ? 'Nouvelle' : ($startsWithVowel ? 'Nouvel' : 'Nouveau')) . " $lcfSingular",

            '{{editTitle}}' => "Modification d’{$lcfUndefArticle} $lcfSingular",
            '{{editFormTitle}}' => "Modification d’{$lcfUndefArticle} $lcfSingular"
        ]);
    }

    /**
     * Retourne le contenu généré pour une requête.
     *
     * @param string $name
     * @return string
     */
    protected function getRequestContent($name)
    {
        if (! $stub = $this->getRequestStub($name)) {
            return '';
        }

        $namespace = $this->appNs . '\Http\Requests\\' . implode('\\', $this->sectionSegmentsStudly);

        return strtr($stub, array_merge($this->getCommonReplacements(), [
            '{{namespace}}' => $namespace
        ]));
    }

    /**
     * Retourne le contenu généré pour une vue.
     *
     * @param string $name
     * @return string
     */
    protected function getViewContent($name)
    {
        if (! $stub = $this->getViewStub($name)) {
            return '';
        }

        return strtr($stub, $this->getCommonReplacements());
    }

    /**
     * Retourne les remplacements communs à tous les templates (sauf traductions).
     *
     * @return array
     */
    protected function getCommonReplacements()
    {
        return [
            '{{sectionBaseName}}' => end($this->sectionSegments),
            '{{studlySectionBaseName}}' => end($this->sectionSegmentsStudly),
            '{{routeBaseAlias}}' => $this->section,
            '{{langBaseKey}}' => ($this->langDir ? $this->langDir . '/' : '') . implode('/', $this->sectionSegments),
            '{{viewBaseName}}' => ($this->viewsDir ? $this->viewsDir . '.' : '') . $this->section,
            '{{htmlIdentifierBase}}' => str_replace('.', '-', $this->section),
            '{{modelClass}}' => $this->modelClass,
            '{{listingClass}}' => $this->appNs . '\\Http\\Listings\\' . implode('\\', $this->sectionSegmentsStudly) . 'Listing',
            '{{optionsTrait}}' => $this->appNs . '\\Traits\\' . implode('\\', $this->sectionSegmentsStudly) . 'OptionsTrait'
        ];
    }

    // ------------------------------------------------------------------------
    // GET PATHS
    // ------------------------------------------------------------------------

    /**
     * Retourne le contenu du template du contrôleur.
     *
     * @return string
     */
    protected function getControllerStub()
    {
        return $this->getStub('controller');
    }

    /**
     * Retourne le contenu du template du trait des options.
     *
     * @return string
     */
    protected function getOptionsTraitStub()
    {
        return $this->getStub('optionsTrait');
    }

    /**
     * Retourne le contenu du template du listing.
     *
     * @return string
     */
    protected function getListingStub()
    {
        return $this->getStub('listing');
    }

    /**
     * Retourne le contenu du template des routes.
     *
     * @return string
     */
    protected function getRoutesStub()
    {
        return $this->getStub('routes');
    }

    /**
     * Retourne le contenu du template des breadcrumbs.
     *
     * @param string $name
     * @return string
     */
    protected function getBreadcrumbsStub()
    {
        return $this->getStub('breadcrumbs');
    }

    /**
     * Retourne le contenu du template du fichier des traductions.
     *
     * @param string $name
     * @return string
     */
    protected function getLangStub()
    {
        return $this->getStub('lang');
    }

    /**
     * Retourne le contenu du template d'une requête.
     *
     * @param string $name
     * @return string
     */
    protected function getRequestStub($name)
    {
        return $this->getStub("requests/$name");
    }

    /**
     * Retourne le contenu du template d'une vue.
     *
     * @param string $name
     * @return string
     */
    protected function getViewStub($name)
    {
        return $this->getStub("views/$name");
    }

    /**
     * Retourne le contenu d'un template.
     *
     * @param string $name
     * @return string
     */
    protected function getStub($name)
    {
        if (is_file($path = base_path("resources/stubs/vendor/crud-generator/{$this->stubsGroup}/$name.stub"))) {
            return file_get_contents($path);
        }

        if (is_file($path = __DIR__ . "/../resources/stubs/{$this->stubsGroup}/$name.stub")) {
            return file_get_contents($path);
        }

        return '';
    }

    /**
     * Retourne le chemin complet vers un répertoire de remplates.
     *
     * @param string $dirName
     * @return string
     */
    protected function getStubDirPath($dirName)
    {
        if (is_dir($path = base_path("resources/stubs/vendor/crud-generator/{$this->stubsGroup}/$dirName/"))) {
            return $path;
        }

        if (is_dir($path = __DIR__ . "/../resources/stubs/{$this->stubsGroup}/$dirName/")) {
            return $path;
        }

        return '';
    }

    /**
     * Retourne le chemin vers le fichier des traductions.
     *
     * @return string
     */
    protected function getLangPath()
    {
        $sectionSegments = $this->sectionSegments;

        if ($this->langDir) {
            array_unshift($sectionSegments, $this->langDir);
        }

        return base_path('resources/lang/fr/' . implode('/', $sectionSegments) . '.php');
    }

    // ------------------------------------------------------------------------
    // HELPERS
    // ------------------------------------------------------------------------

    /**
     * Crée les sous-dossiers d'un fichier si ceux-ci n'existent pas.
     *
     * @param string $filePath
     * @return void
     */
    protected function createMissingDirs($filePath)
    {
        if (! is_dir($dirPath = dirname($filePath))) {
            mkdir($dirPath, 0755, true);
        }
    }
}
