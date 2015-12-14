<?php

namespace Axn\CrudGenerator\Console;

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

        $generator = new Generator($section, $modelClass);

        if ($generator->generateController()) {
            $this->line("Controller generated");
        } else {
            $this->error("Error while writing controller");
        }

        if ($generator->generateRoutes()) {
            $this->line("Routes file generated");
        } else {
            $this->error("Error while writing routes file");
        }

        if ($generator->generateRequest('store')) {
            $this->line("Store request generated");
        } else {
            $this->error("Error while writing store request");
        }

        if ($generator->generateRequest('update')) {
            $this->line("Update request generated");
        } else {
            $this->error("Error while writing update request");
        }

        if ($generator->generateRequest('updateContent')) {
            $this->line("Update content request generated");
        } else {
            $this->error("Error while writing update content request");
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
			//['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}
}
