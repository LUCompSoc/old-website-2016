<?php namespace CompSoc\User\Facades;

use October\Rain\Support\Facade;

class JWT extends Facade
{
    /**
     * Get the registered name of the component.
     * @return string
     */
    protected static function getFacadeAccessor() { return 'JWT'; }
}
