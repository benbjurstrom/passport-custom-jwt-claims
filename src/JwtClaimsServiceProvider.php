<?php
namespace BenBjurstrom\JwtClaims;
use App;
use Laravel\Passport\Passport;
use Laravel\Passport\PassportServiceProvider;
use Laravel\Passport\Bridge\ClientRepository;
use Laravel\Passport\Bridge\ScopeRepository;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;

class JwtClaimsServiceProvider extends PassportServiceProvider
{

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->setupConfig();
        parent::boot();
    }

    /**
     * Make the authorization service instance.
     *
     * @return AuthorizationServer
     */
    public function makeAuthorizationServer()
    {
        return new AuthorizationServer(
            $this->app->make(ClientRepository::class),
            $this->app->make(AccessTokenRepository::class), // BenBjurstrom\JwtClaims\AccessTokenRepository
            $this->app->make(ScopeRepository::class),
            new CryptKey( 'file://'.Passport::keyPath('oauth-private.key'), null, false ),
            new CryptKey('file://'.Passport::keyPath('oauth-public.key'), null, false )
        );
    }

    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/../resources/config/jwt-claims.php');
        $this->publishes([$source => config_path('jwt-claims.php')]);
        $this->mergeConfigFrom($source, 'jwt-claims');
    }
}