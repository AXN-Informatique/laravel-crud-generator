<?php

namespace Axn\CrudGenerator;

use ReflectionClass, Exception;

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
    public function __construct($section, $modelClass, $stubsGroup)
    {
        $this->validateModelClass($modelClass);
        $explodedModelClass = explode('\\', $modelClass);

        $this->section = $section;
        $this->appNs = $explodedModelClass[0];
        $this->modelClass = $modelClass;
        $this->stubsGroup = $stubsGroup;

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
            '{{section}}'              => $this->section,
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
            '{{baseUrl}}'    => str_replace('.', '/', $this->section),
            '{{section}}'    => $this->section,
            '{{controller}}' => implode('\\', $this->sectionSegmentsStudly).'Controller',
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

            $path = __DIR__."/../resources/stubs/$name.stub";
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
