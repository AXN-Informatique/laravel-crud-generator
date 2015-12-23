<?php

namespace Axn\CrudGenerator\Console;

use Exception;
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
    protected $description = 'Generates CRUD files';

    /**
     * Exécute la commande.
     *
     * @return void
     */
    public function handle()
    {
        // Arguments
        $section     = $this->argument('section');
        $modelClass  = $this->argument('model');

        // Options
        $stubsGroup  = $this->option('stubs');
        $langDir     = $this->option('langdir');
        $viewsDir    = $this->option('viewsdir');
        $breadcrumbs = $this->option('breadcrumbs');

        // Questions
        $singular    = $this->fixEncoding($this->ask('Singular name of the section (fr)'));
        $plural      = $this->fixEncoding($this->ask('Plural name of the section (fr)'));
        $feminine    = $this->confirm('Feminine? [y|n]', false);

        try {
            $generator = new Generator($section, $modelClass, $stubsGroup, $langDir, $viewsDir);

            if ($generator->generateController()) {
                $this->line("Controller generated");
            }

            if ($generator->generateRoutes()) {
                $this->line("Routes file generated");
            }

            if ($generator->generateLang($singular, $plural, $feminine)) {
                $this->line("Lang file (fr) generated");
            }

            if ($generator->generateRequest('store')) {
                $this->line("Store request generated");
            }

            if ($generator->generateRequest('update')) {
                $this->line("Update request generated");
            }

            if ($generator->generateRequest('updateContent')) {
                $this->line("Update content request generated");
            }

            if ($generator->copyViews($this->laravel['files'])) {
                $this->line("Views copied");
            }

            if ($breadcrumbs && $generator->appendBreadcrumbs($plural)) {
                $this->line("Breadcrumbs appended to breadcrumbs file");
            }
        }
        catch (Exception $e) {
            $this->error('Exception catched: '.$e->getMessage());
            $this->line($e->getTraceAsString());
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
			['section', InputArgument::REQUIRED, 'Section name'],
            ['model', InputArgument::REQUIRED, 'Model class name (with namespace)'],
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
            ['stubs', null, InputOption::VALUE_OPTIONAL, 'Stubs group to use', 'default'],
            ['langdir', null, InputOption::VALUE_OPTIONAL, 'Sub-directory for lang file', ''],
            ['viewsdir', null, InputOption::VALUE_OPTIONAL, 'Sub-directory for views files', ''],
            ['breadcrumbs', 'b', InputOption::VALUE_NONE, 'Append breadcrumbs to breadcrumbs file'],
		];
	}
}
