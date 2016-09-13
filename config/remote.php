<?php

return [
	'connections' => array(
        'default' => array( 
            'host'      => env('SSH_HOST', 'http://localhost'),
            'username'  => env('SSH_USERNAME', 'root'),
            'password'  => env('SSH_PASSWORD', 'root'),
            'key'       => '',
            'keyphrase' => '',
            'root'      => env('SSH_ROOT', '/var/www/html'),
        ),
    ),
];