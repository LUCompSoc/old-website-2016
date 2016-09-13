<?php namespace System\Controllers;

use SSH;
use System\Classes\Controller;

/**
 * Server controller
 *
 * @package october\system
 * @author Alexander Jung
 *
 */
class Server extends Controller
{
    public function deploy()
    {
        SSH::define('deploy', array(
            'cd ' . env('SSH_ROOT', '/var/www/html'),
            'git pull origin master'
            'php artisan migrate',
        ));

        SSH::task('deploy', function($line)
        {
            echo $line . PHP_EOL;
        });
    }
}