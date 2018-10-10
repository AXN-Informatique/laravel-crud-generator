<?php

namespace Axn\CrudGenerator\Console;

use ReflectionClass, ReflectionException, Exception;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Axn\CrudGenerator\Generator;

class GenerateCommand extends Command
{
    /**
     * Nom de la commande.
     *
     * @var string
     */
    protected $name = 'crud:generate';

    /**
     * Description de la commande.
     *
     * @var string
     */
    protected $description = 'Generates CRUD files for a section';

    /**
     * Exécute la commande.
     *
     * @return void
     */
    public function handle()
    {
        // Arguments
        $section    = $this->argument('section');
        $modelClass = $this->argument('model');

        if (!$this->isValidModel($modelClass)) {
            $this->error("$modelClass is not instantiable or is not an instance of Illuminate\Database\Eloquent\Model");
            return;
        }

        // Options
        $stubsGroup  = $this->option('stubs');
        $langDir     = $this->option('langdir');
        $viewsDir    = $this->option('viewsdir');
        $breadcrumbs = $this->option('breadcrumbs');

        $generator = new Generator($section, $modelClass, $stubsGroup, $langDir, $viewsDir);

        // Questions, si nécessaire
        if ($generator->shouldGenerateLang()) {
            $singular = $this->fixEncoding($this->ask('Singular name of the section (fr)'));
            $plural   = $this->fixEncoding($this->ask('Plural name of the section (fr)'));
            $feminine = $this->confirm('Feminine? [y|n]', false);
        }
        elseif ($breadcrumbs) {
            $plural   = $this->fixEncoding($this->ask('Plural name of the section (fr)'));
        }

        try {
            if ($generatedFile = $generator->generateController()) {
                $this->line('<info>Controller generated:</info> '.realpath($generatedFile));
            }

            if ($generatedFile = $generator->generateListing()) {
                $this->line('<info>Listing generated:</info> '.realpath($generatedFile));
            }

            if ($generatedFile = $generator->generateRoutes()) {
                $this->line('<info>Routes generated:</info> '.realpath($generatedFile));
            }

            if ($generator->shouldGenerateLang()) {
                if ($generatedFile = $generator->generateLang($singular, $plural, $feminine)) {
                    $this->line('<info>Translations generated:</info> '.realpath($generatedFile));
                }
            }

            foreach ($generator->getStubsNamesInDirectory('requests') as $requestName) {
                if ($generatedFile = $generator->generateRequest($requestName)) {
                    $this->line('<info>Request generated:</info> '.realpath($generatedFile));
                }
            }

            foreach ($generator->getStubsNamesInDirectory('views') as $viewName) {
                if ($generatedFile = $generator->generateView($viewName)) {
                    $this->line('<info>View generated:</info> '.realpath($generatedFile));
                }
            }

            if ($breadcrumbs && ($breadcrumbsFile = $generator->appendBreadcrumbs($plural))) {
                $this->line('<info>Breadcrumbs appended to:</info> '.realpath($breadcrumbsFile));
            }
        }
        catch (Exception $e) {
            $this->error('Exception catched: '.$e->getMessage());
            $this->line($e->getTraceAsString());
        }
    }

    /**
     * Indique si la classe modèle est bien instanciable et est bien une instance
     * de Illuminate\Database\Eloquent\Model.
     *
     * @param  string $modelClass
     * @return boolean
     */
    protected function isValidModel($modelClass)
    {
        try {
            $rc = new ReflectionClass($modelClass);

            return $rc->isInstantiable()
                && $rc->isSubclassOf('Illuminate\Database\Eloquent\Model');
        }
        catch (ReflectionException $e) {
            return false;
        }
    }

    /**
     * Corrige le problème d'encodage des caractères sous Windows lorsque l'input
     * provient de la console (saisie utilisateur).
     *
     * @param  string $input
     * @return string
     */
    protected function fixEncoding($input)
    {
        // Windows only
        if (DIRECTORY_SEPARATOR === '\\') {
            preg_match('/[0-9]+/', shell_exec('chcp'), $m);

            return iconv("CP{$m[0]}", 'UTF-8', $input);
        }

        return $input;
    }

    /**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['section', InputArgument::REQUIRED, 'Section name.'],
            ['model', InputArgument::REQUIRED, 'Model class name (with namespace).'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
            ['stubs', null, InputOption::VALUE_OPTIONAL, 'Stubs group to use.', 'default'],
            ['langdir', null, InputOption::VALUE_OPTIONAL, 'Sub-directory for lang file.', ''],
            ['viewsdir', null, InputOption::VALUE_OPTIONAL, 'Sub-directory for views files.', ''],
            ['breadcrumbs', 'b', InputOption::VALUE_NONE, 'Append breadcrumbs to breadcrumbs file.'],
		];
	}
}
