<?php

namespace BenBjurstrom\JwtClaims\Tests;

use Firebase\JWT\JWT;

class JwtTest extends TestCase
{
    /**
     * @test
     */
    public function it_tests_tokens_contain_custom_jwt()
    {
        $user  = factory(User::class)->create();
        $token = $user->createToken('Test Token')->accessToken;

        $key = file_get_contents(__DIR__ . '/../vendor/orchestra/testbench/fixture/storage/oauth-public.key');
        $token = JWT::decode($token, $key, ['RS256']);

        $this->assertObjectHasAttribute('iss', $token);
        $this->assertObjectHasAttribute('name', $token);
        $this->assertObjectHasAttribute('email', $token);

        $this->assertEquals($token->name, $user->name);
        $this->assertEquals($token->email, $user->email);
        $this->assertEquals($token->iss, url(''));
    }
}