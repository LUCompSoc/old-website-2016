<?php

return [
	'plugin' => [
        'name' => 'LU CSS Integrations',
        'description' => 'Allows authentication through University JWT for all services on the VPN.',

        'profile' => [
            'tab_label' => 'Profile',
            'fields' => [
                'university_id' => 'Library Card Number',
                'union_id' => 'LUSU Number',
                'title' => 'Title',
                'irc_id' => 'IRC ID',
                'subscribed_newsletters' => 'Subscribed newsletter IDs'
                'position' => 'Position'
            ]
        ]
    ],

    'login' => [
        'name' => 'Login Form Component',
        'desc' => 'A form component for system-wide authentication.',
        'redirect_to' => 'Redirect to',
        'redirect_to_desc' => 'Page name to redirect to after sign in.',
        'error' => [
            'general' => 'An error occured.',
            'not_registerd' => 'You are not registered.',
            'bad_response' => 'Bad response. Try again?',
            'bad_request' => 'An error occured during your account authentication. Please try again.'
        ]
    ],

    'register' => [
    	'name' => 'Registration Form',
    	'description' => 'The main registration form for signing up to the society.',
        'redirect_to' => 'Redirect to',
        'redirect_to_desc' => 'Page name to redirect to after registration.',
        'code_param' => 'Activation Code Param',
        'code_param_desc' => 'The page URL parameter used for the registration activation code',
        'error' => [
            'general' => 'An error occured.',
            'already_registered' => 'You are already registered!',
            'bad_response' => 'Bad response. Try again?',
            'bad_request' => 'An error occured during your account registration. Please try again.',
            'irc_password' => 'You have selected an insecure password. Please make sure it is at least :num characters long and contains at least one uppercase, lowercase and alphanumeric character.'
        ]
    ],

    'settings' => [

        'menu_label' => 'CompSoc Profile',
        'menu_description' => 'Settings for user details and authorisation.',

        'fields' => [
            'jwt_private_key' => 'JWT Private Key',
            'jwt_private_key_description' => 'The key is a \'shared secret\', this application will need to be configured with the private key provided by ISS.',
            'jwt_auth_url' => 'JWT Authorisation API',
            'jwt_auth_url_description' => 'This is the JWT Authenticator Service used to accept new users. This is provided by the University.'
        ],
        'tabs' => [
            'jwt' => 'JWT Authorisation'
        ],
    ]
];