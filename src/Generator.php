<?php

namespace Axn\CrudGenerator;

use ReflectionClass, Exception;
use Illuminate\Filesystem\Filesystem;

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
     * Constructeur.
     *
     * @param  string $section
     * @param  string $modelClass
     * @param  string $stubsGroup
     * @return void
     */
    public function __construct($section, $modelClass, $stubsGroup, $langDir, $viewsDir)
    {
        $this->validateModelClass($modelClass);
        $explodedModelClass = explode('\\', $modelClass);

        $this->section    = $section;
        $this->modelClass = $modelClass;
        $this->stubsGroup = $stubsGroup;
        $this->langDir    = $langDir;
        $this->viewsDir   = $viewsDir;

        $this->appNs = $explodedModelClass[0];
        $this->sectionSegments = explode('.', $section);
        $this->sectionSegmentsStudly = array_map('studly_case', $this->sectionSegments);
    }

    /**
     * Génère le fichier du contrôleur.
     *
     * @return boolean
     */
    public function generateController()
    {
        if (!$content = $this->getControllerContent()) return false;

        $path = app_path('Http/Controllers/'.implode('/', $this->sectionSegmentsStudly).'Controller.php');
        $this->createMissingDirs($path);

        return file_put_contents($path, $content) !== false;
    }

    /**
     * Génère le fichier des routes.
     *
     * @return boolean
     */
    public function generateRoutes()
    {
        if (!$content = $this->getRoutesContent()) return false;

        $path = app_path('Http/routes/'.implode('/', $this->sectionSegments).'.php');
        $this->createMissingDirs($path);

        return file_put_contents($path, $content) !== false;
    }

    /**
     * Génère le fichier des traductions.
     *
     * @param  string  $singular
     * @param  string  $plural
     * @param  boolean $feminine
     * @return boolean
     */
    public function generateLang($singular, $plural, $feminine)
    {
        if (!$content = $this->getLangContent($singular, $plural, $feminine)) return false;

        $sectionSegments = $this->sectionSegments;

        if ($this->langDir) {
            array_unshift($sectionSegments, $this->langDir);
        }

        $path = base_path('resources/lang/fr/'.implode('/', $sectionSegments).'.php');
        $this->createMissingDirs($path);

        return file_put_contents($path, $content) !== false;
    }

    /**
     * Génère le fichier d'une requête.
     *
     * @param  string $name
     * @return boolean
     */
    public function generateRequest($name)
    {
        if (!$content = $this->getRequestContent($name)) return false;

        $path = app_path('Http/Requests/'.implode('/', $this->sectionSegmentsStudly).'/'.studly_case($name).'Request.php');
        $this->createMissingDirs($path);

        return file_put_contents($path, $content) !== false;
    }

    /**
     * Copie l'ensemble des vues du répertoire "views" vers la destination.
     *
     * @param  Filesystem $fs
     * @return boolean
     */
    public function copyViews(Filesystem $fs)
    {
        if (!is_dir($src = base_path("resources/stubs/vendor/crud-generator/{$this->stubsGroup}/views/"))) {
            if ($this->stubsGroup !== 'default') return false;

            $src = __DIR__."/../resources/stubs/default/views/";
        }

        $sectionSegments = $this->sectionSegments;

        if ($this->viewsDir) {
            array_unshift($sectionSegments, $this->viewsDir);
        }

        $dest = base_path('resources/views/'.implode('/', $sectionSegments).'/');

        return $fs->copyDirectory($src, $dest);
    }

    /**
     * Retourne le contenu généré pour le contrôleur.
     *
     * @return string
     */
    protected function getControllerContent()
    {
        if (!$stub = $this->getControllerStub()) return '';

        $sectionSegmentsStudly = $this->sectionSegmentsStudly;
        $name = array_pop($sectionSegmentsStudly).'Controller';
        $namespace = $this->appNs.'\Http\Controllers';
        $requestsNs = $this->appNs.'\Http\Requests\\'.implode('\\', $this->sectionSegmentsStudly);

        if ($sectionSegmentsStudly) {
            $namespace .= '\\'.implode('\\', $sectionSegmentsStudly);
        }

        return strtr($stub, [
            '{{namespace}}'            => $namespace,
            '{{name}}'                 => $name,
            '{{routeBaseAlias}}'       => $this->section,
            '{{langBaseKey}}'          => ($this->langDir ? $this->langDir.'/' : '').implode('/', $this->sectionSegments),
            '{{viewBaseName}}'         => ($this->viewsDir ? $this->viewsDir.'.' : '').$this->section,
            '{{model}}'                => $this->modelClass,
            '{{storeRequest}}'         => $requestsNs.'\StoreRequest',
            '{{updateRequest}}'        => $requestsNs.'\UpdateRequest',
            '{{updateContentRequest}}' => $requestsNs.'\UpdateContentRequest',
        ]);
    }

    /**
     * Retourne le contenu généré pour le fichier de routes.
     *
     * @return string
     */
    protected function getRoutesContent()
    {
        if (!$stub = $this->getRoutesStub()) return '';

        return strtr($stub, [
            '{{baseUrl}}'    => implode('/', $this->sectionSegments),
            '{{baseAlias}}'  => $this->section,
            '{{controller}}' => implode('\\', $this->sectionSegmentsStudly).'Controller',
        ]);
    }

    /**
     * Retourne le contenu généré pour le fichier des traductions (en français).
     *
     * @param  string  $singular
     * @param  string  $plural
     * @param  boolean $feminine
     * @return array
     */
    protected function getLangContent($singular, $plural, $feminine)
    {
        if (!$stub = $this->getLangStub()) return '';

        $lcfSingular     = lcfirst($singular);
        $lcfPlural       = lcfirst($plural);
        $startsWithVowel = starts_with($lcfSingular, ['a', 'e', 'i', 'o', 'u']);
        $lcfDefArticle   = ($startsWithVowel ? "l'" : ($feminine ? 'la ' : 'le '));
        $ucfDefArticle   = ucfirst($lcfDefArticle);
        $lcfUndefArticle = ($feminine ? 'une' : 'un');

        return strtr($stub, [
            '{{storeSuccess}}'   => "{$ucfDefArticle}$lcfSingular a été ".($feminine ? 'créée' : 'créé')." avec succès.",
            '{{updateSuccess}}'  => "{$ucfDefArticle}$lcfSingular a été ".($feminine ? 'mise' : 'mis')." à jour avec succès.",
            '{{enableSuccess}}'  => "{$ucfDefArticle}$lcfSingular a été ".($feminine ? 'activée' : 'activé')." avec succès.",
            '{{disableSuccess}}' => "{$ucfDefArticle}$lcfSingular a été ".($feminine ? 'désactivée' : 'désactivé')." avec succès.",
            '{{sortSuccess}}'    => "L'ordre des $lcfPlural a été modifié avec succès.",
            '{{destroySuccess}}' => "{$ucfDefArticle}$lcfSingular a été ".($feminine ? 'supprimée' : 'supprimé')." avec succès.",
            '{{destroyFailure}}' => "Suppression impossible : {$lcfDefArticle}$lcfSingular est peut-être ".($feminine ? 'liée' : 'lié')." à d'autres enregistrements.",
            '{{listTitle}}'      => "Liste des $lcfPlural",
            '{{listEmpty}}'      => "Il n'y a ".($feminine ? 'aucune' : 'aucun')." enregistrement à afficher.",
            '{{editTooltip}}'    => "Modifier {$lcfDefArticle}$lcfSingular «&nbsp;:name&nbsp;».",
            '{{enableTooltip}}'  => "Activer {$lcfDefArticle}$lcfSingular «&nbsp;:name&nbsp;».",
            '{{disableTooltip}}' => "Désactiver {$lcfDefArticle}$lcfSingular «&nbsp;:name&nbsp;».",
            '{{destroyTooltip}}' => "Supprimer {$lcfDefArticle}$lcfSingular «&nbsp;:name&nbsp;».",
            '{{destroyConfirm}}' => "Êtes-vous sûr de vouloir supprimer {$lcfDefArticle}$lcfSingular «&nbsp;:name&nbsp;»&nbsp;?",
            '{{createTitle}}'    => "Création d'{$lcfUndefArticle} ".($feminine ? 'nouvelle' : ($startsWithVowel ? 'nouvel' : 'nouveau'))." $lcfSingular",
            '{{editTitle}}'      => "Modification d'{$lcfUndefArticle} $lcfSingular"
        ]);
    }

    /**
     * Retourne le contenu généré pour une requête.
     *
     * @param  string $name
     * @return string
     */
    protected function getRequestContent($name)
    {
        if (!$stub = $this->getRequestStub(camel_case($name))) return '';

        $namespace = $this->appNs.'\Http\Requests\\'.implode('\\', $this->sectionSegmentsStudly);

        return strtr($stub, [
            '{{namespace}}' => $namespace,
        ]);
    }

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
     * Retourne le contenu du template des routes.
     *
     * @return string
     */
    protected function getRoutesStub()
    {
        return $this->getStub('routes');
    }

    /**
     * Retourne le contenu du template du fichier des traductions.
     *
     * @param  string $name
     * @return string
     */
    protected function getLangStub()
    {
        return $this->getStub('lang');
    }

    /**
     * Retourne le contenu du template d'une requête.
     *
     * @param  string $name
     * @return string
     */
    protected function getRequestStub($name)
    {
        return $this->getStub("requests/$name");
    }

    /**
     * Retourne le contenu d'un template.
     *
     * @param  string $name
     * @return string
     */
    protected function getStub($name)
    {
        if (!is_file($path = base_path("resources/stubs/vendor/crud-generator/{$this->stubsGroup}/$name.stub"))) {
            if ($this->stubsGroup !== 'default') return '';

            $path = __DIR__."/../resources/stubs/default/$name.stub";
        }

        return file_get_contents($path);
    }

    /**
     * Vérifie que la classe modèle est bien instanciable et est bien une instance
     * de Illuminate\Database\Eloquent\Model. Lève une exception le cas échéant.
     *
     * @param  string $modelClass
     * @return void
     */
    protected function validateModelClass($modelClass)
    {
        $rc = new ReflectionClass($modelClass);

        if (!$rc->isInstantiable() || !$rc->isSubclassOf('Illuminate\Database\Eloquent\Model')) {
            throw new Exception("$modelClass is not instantiable or is not an instance of Illuminate\Database\Eloquent\Model");
        }
    }

    /**
     * Crée les sous-dossiers d'un fichier si ceux-ci n'existent pas.
     *
     * @param  string $filePath
     * @return void
     */
    protected function createMissingDirs($filePath)
    {
        if (!is_dir($dirPath = dirname($filePath))) {
            mkdir($dirPath, 0755, true);
        }
    }
}
