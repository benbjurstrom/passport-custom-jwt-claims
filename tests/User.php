<?php

namespace BenBjurstrom\JwtClaims\Tests;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
class User extends Authenticatable
{
    use HasApiTokens;
}