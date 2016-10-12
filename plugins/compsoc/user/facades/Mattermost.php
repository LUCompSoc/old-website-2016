<?php namespace CompSoc\User\Facades;

use October\Rain\Support\Facade;

class Mattermost extends Facade
{
    /**
     * Get the registered name of the component.
     * @return string
     */
    protected static function getFacadeAccessor() { return 'Mattermost'; }
}
