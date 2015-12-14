<?php

if (!function_exists('crud')) {
    /**
     * Retourne l'instance de la classe Crud du package axn/laravel-crud-generator.
     *
     * @return Axn\CrudGenerator\Crud
     */
    function crud()
    {
        return app('crud');
    }
}
