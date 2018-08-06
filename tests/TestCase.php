<?php
namespace BenBjurstrom\JwtClaims\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Orchestra\Testbench\TestCase as Orchestra;
use Firebase\JWT\JWT;

abstract class TestCase extends Orchestra
{
    use DatabaseTransactions;
    public function setUp()
    {
        parent::setUp();
        // Path to Model Factories (within your package
        $this->withFactories(__DIR__ . '/factories');

        // Migrate test tables
        $this->artisan('migrate', ['--path' => 'migrations']);

        // Migrate laravel/passport tables
        $this->artisan('migrate');
        $this->artisan('passport:install');

        // Chmod private key to 600
        chmod(__DIR__ . '/../vendor/orchestra/testbench-core/laravel/storage/oauth-private.key', 0600);

    }
    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'BenBjurstrom\JwtClaims\JwtClaimsServiceProvider'
        ];
    }
    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('auth.guards.api', [
            'driver' => 'passport',
            'provider' => 'users',
        ]);
        $app['config']->set('auth.providers.users', [
            'driver' => 'eloquent',
            'model' => User::class,
        ]);
        $app['config']->set('app', [
            'debug' => true,
            'key'   => str_random(32),
            'cipher'=> 'AES-256-CBC',
            'log'   => 'single'
        ]);
        $base_path = $app['path.base'];
        $db_path = $app['path.database'];
    }
}
