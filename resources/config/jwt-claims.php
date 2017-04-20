<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Claims
    |--------------------------------------------------------------------------
    |
    | User claims will be loaded from the properties of the auth providers model
    | specified in the auth config file.
    |
    */
    'user_claims' => [
        'name' => 'name',
        'email' => 'email',
    ],

    /*
    |--------------------------------------------------------------------------
    | App claims
    |--------------------------------------------------------------------------
    |
    | App claims are static and will be given the specified value across all
    | tokens issued by the app.
    |
    */
    'app_claims' => [
        'iss' => url('')
    ]
];