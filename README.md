# Laravel Passport Custom JWT Claims
[![Build Status](https://travis-ci.org/benbjurstrom/passport-custom-jwt-claims.svg)](https://travis-ci.org/benbjurstrom/passport-custom-jwt-claims)
Customize the JWT claims in [Laravel/Passport](https://github.com/laravel/passport) access tokens

## What are JWT claims?
All access tokens issued by [Laravel/Passport](https://github.com/laravel/passport) are in fact [JSON web tokens](https://jwt.io/) (JWT). 
Each token contains a set of claims consisting of JSON key value pairs. Because the token is cryptographically signed 
using a public/private RSA key pair we can trust that the claims contained in the token were issued by Laravel/Passport.

Here is an example token containing the default Laravel/Passport claims:
```$xslt
eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjllNzAxMjhmOTkwZTFlZjI0NGFmMDc0YjQzMzA2YTRmNDViZWFiNjU1MzM5NjE2ODIyOGJmODc2Y2UwMTAwNTIyNGZhMTc5MzdkMGYwMTU3In0.eyJhdWQiOiJjOGUxMDRmMC0wNTYyLTExZTctOTA1Yi0zZDc3ZGY5N2YyZjgiLCJqdGkiOiI5ZTcwMTI4Zjk5MGUxZWYyNDRhZjA3NGI0MzMwNmE0ZjQ1YmVhYjY1NTMzOTYxNjgyMjhiZjg3NmNlMDEwMDUyMjRmYTE3OTM3ZDBmMDE1NyIsImlhdCI6MTQ4OTkwMTc1NSwibmJmIjoxNDg5OTAxNzU1LCJleHAiOjE1MjE0Mzc3NTUsInN1YiI6ImM4ZGY5OWEwLTA1NjItMTFlNy05MDgyLWJmZDdhYTMzMTFlOCIsInNjb3BlcyI6W119.qFGwfeWezJZZaxNIZyPfnnGHkUdAPhHvJ3Nf3NYa8Y5Ba2ubfil21KgzeugY1aDSU93oWLMcUzGkoVblT1U79IlPV6JiGhMA4x7jHB5yJPKZeH-maaB8HKzQ8CoFG0YEAc_60G2ZwCDLv-NhuaxgDOXFc7FaX1qc3U1MpyJixEIjZc0xQ_CuRRVf3Kzx1rTXedJpbqFxTDYGDnKx4HLo5l96t8mdlmiToU6TphYDRAIkQjsTZKP9YRRIahm3cZF56nO9qaqpTpANjhiV4IJqejDki53NkBEqnhDLS4ZPJFK2qLD62Aiw7wBxKhmfNyYQJNxeC6D1PaftFzudbAi7RtQikn0xIgzKl1jmMpgjyGmAPQfnqMlE68rMIw-KqICh2nPQJcr5OO8ZsBMzL5EbjBOjemBHAm2sBViijqaU2-Ig3bwCB_kfKLrtumuUPIDbWV3tTMzBBSdY6P9dnVGJZawYiheU4rAqiru1fWZ8WpdGASrAxfRmiRTqDnRMQ82unbi5MC-f-NJhmhRwFN4QAgmxGm2T4gy0uRdKZ3ER_FDE4MEsKGb0qIkkGtjt77eLBq_jA6GXbVP948lbJAKTJsi3KOR5rMhZSAI-MywTMXWUISn5ZwgCAHfwUofPJNpGqRAkm9l5lcjMVTf2-VYCB7VdREizvg-fidZ9HcYUfSo
```

If we decode the token you get the following JWT payload object:
```json
{
  "aud": "c8e104f0-0562-11e7-905b-3d77df97f2f8",
  "jti": "9e70128f990e1ef244af074b43306a4f45beab6553396168228bf876ce01005224fa17937d0f0157",
  "iat": 1489901755,
  "nbf": 1489901755,
  "exp": 1521437755,
  "sub": "c8df99a0-0562-11e7-9082-bfd7aa3311e8",
  "scopes": []
}
```

For reference, the claim `aud` is the Laravel/Passport client_id that issued the token and the key `sub` refers to the
user id in your laravel users table. Note that my client_id and user_id columns are UUID data types.

## Why would you need custom claims?
The [OpenID Connect](http://openid.net/connect/) protocol requires JWT claims that are not included in Laravel/Passport 
access tokens. Adding custom claims allows us to use access tokens issued by Laravel/Passport to authenticate with other services
using OpenID Connect. For example, with this package it is possible to use a Laravel/Passport access token to authenticate a laravel user on a 
[Couchbase Sync Gateway](https://developer.couchbase.com/documentation/mobile/1.4/guides/authentication/openid/index.html) 
server.

##  Installation
Install the package via composer:
```bash
composer require benbjurstrom/passport-custom-jwt-claims
```

Add the service provider to the config/app.php providers array. 

```php
// config/app.php
'providers' => [
    ...
    BenBjurstrom\JwtClaims\JwtClaimsServiceProvider::class
];
```

Do not include `Laravel\Passport\PassportServiceProvider` in your providers array 
as `JwtClaimsServiceProvider` extends from it.

##  Configuration
To set your custom claims you must publish the config file:
                          
```bash
php artisan vendor:publish --provider="JwtClaimsServiceProvider"
```

This is the contents of the published file. Add additional claims as needed.

```php
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
```
