<?php

namespace Axn\CrudGenerator;

use Symfony\Component\Translation\TranslatorInterface as Translator;
use Illuminate\Contracts\View\Factory as ViewFactory;

class Crud
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var ViewFactory
     */
    protected $viewFactory;

    /**
     * Constructeur.
     *
     * @param  Translator $translator
     * @param  ViewFactory $viewFactory
     * @return void
     */
    public function __construct(Translator $translator, ViewFactory $viewFactory)
    {
        $this->translator = $translator;
        $this->viewFactory = $viewFactory;
    }

    /**
     * Retourne la traduction correspondant à l'id pour une section donnée.
     *
     * @param  string      $section
     * @param  string      $id
     * @param  array       $parameters
     * @param  string|null $domain
     * @param  string|null $locale
     * @return string
     */
    public function trans($section, $id, array $parameters = [], $domain = 'messages', $locale = null)
    {
        $id = $this->transId($section, $id, $locale);

        return $this->translator->trans($id, $parameters, $domain, $locale);
    }

    /**
     * Retourne l'id de traduction pour une section donnée. Si non existant,
     * on retourne la valeur par défaut.
     *
     * @param  string      $section
     * @param  string      $id
     * @param  string|null $locale
     * @return string
     */
    public function transId($section, $id, $locale = null)
    {
        $section = str_replace('.', '/', $section);

        if ($this->translator->has("$section.$id", $locale)) {
            return "$section.$id";
        }

        return "crud-generator::default.$id";
    }

    /**
     * Retourne une vue pour une section donnée.
     *
     * @param  string $section
     * @param  string $view
     * @param  array  $data
     * @param  array  $mergeData
     * @return \Illuminate\View\View
     */
    public function view($section, $view, array $data = [], array $mergeData = [])
    {
        $view = $this->viewName($section, $view);

        return $this->viewFactory->make($view, $data, $mergeData);
    }

    /**
     * Retourne le nom d'une vue pour une section donnée. Si non existant,
     * on retourne le nom de la vue par défaut.
     *
     * @param  string $section
     * @param  string $view
     * @return string
     */
    public function viewName($section, $view)
    {
        if ($this->viewFactory->exists("$section.$view")) {
            return "$section.$view";
        }

        return "crud-generator::$view";
    }
}
