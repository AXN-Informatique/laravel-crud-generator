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
        $section = $this->argument('section');
        $modelClass = $this->argument('model');
        $stubsGroup = $this->option('stubs');

        try {
            $generator = new Generator($section, $modelClass, $stubsGroup);

            if ($generator->generateController()) {
                $this->line("Controller generated");
            }

            if ($generator->generateRoutes()) {
                $this->line("Routes file generated");
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
        }
        catch (Exception $e) {
            $this->error('Exception catched: '.$e->getMessage());
            $this->line($e->getTraceAsString());
        }
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
		];
	}
}
