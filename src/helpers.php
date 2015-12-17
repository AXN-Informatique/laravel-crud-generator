<?php

if (!function_exists('crud_view_name')) {
    /**
     * Retourne le nom complet d'une vue pour un écran CRUD :
     * - {viewsMainPath}{view}, si existant
     * - {viewsDefaultPath}{view}, sinon
     *
     * @param  array  $crudOptions
     * @param  string $view
     * @return string
     */
    function crud_view_name(array $crudOptions, $view)
    {
        $mainPath = array_get($crudOptions, 'viewsMainPath', '');

        if (view()->exists($mainPath.$view)) {
            return $mainPath.$view;
        }

        $defaultPath = array_get($crudOptions, 'viewsDefaultPath', '');

        return $defaultPath.$view;
    }
}

if (!function_exists('crud_trans_id')) {
    /**
     * Retourne un id de traduction complet pour un écran CRUD :
     * - {langMainFile}.{id}, si existant
     * - {langDefaultFile}.{id}, sinon
     *
     * @param  array       $crudOptions
     * @param  string      $id
     * @param  string|null $locale
     * @return string
     */
    function crud_trans_id(array $crudOptions, $id, $locale = null)
    {
        $mainFile = str_replace('.', '/', array_get($crudOptions, 'langMainFile', 'app'));

        if (trans()->has("$mainFile.$id", $locale)) {
            return "$mainFile.$id";
        }

        $defaultFile = str_replace('.', '/', array_get($crudOptions, 'langDefaultFile', 'app'));

        return "$defaultFile.$id";
    }
}
