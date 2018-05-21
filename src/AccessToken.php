<?php

namespace BenBjurstrom\JwtClaims;

use RuntimeException;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use League\OAuth2\Server\CryptKey;
use Laravel\Passport\Bridge\AccessToken as PassportAccessToken;
use Illuminate\Support\Arr;

class AccessToken extends PassportAccessToken
{

    public function convertToJWT(CryptKey $privateKey)
    {
        $claims = config('jwt-claims');
        $user = $this->getUser();

        $builder = (new Builder())
            ->setAudience($this->getClient()->getIdentifier())
            ->setId($this->getIdentifier(), true)
            ->setIssuedAt(time())
            ->setNotBefore(time())
            ->setExpiration($this->getExpiryDateTime()->getTimestamp())
            ->setSubject($this->getUserIdentifier())
            ->set('scopes', $this->getScopes());


        if($claims['user_object']) {
            // set user object
            $userData = [];
            foreach($claims['user_claims'] as $key => $claim) {
                $userData[$key] = $user->$claim;
            }
            $builder = $builder->set($claims['user_object_name'], $userData);
        } else {
            // set user claims
            foreach($claims['user_claims'] as $key => $claim) {
                $builder = $builder->set($key, $user->$claim);
            }
        }

        // set app claims
        foreach($claims['app_claims'] as $key => $claim) {
            $builder = $builder->set($key, $claim);
        }

        // sign and return the token
        return $builder->sign(new Sha256(), new Key($privateKey->getKeyPath(), $privateKey->getPassPhrase()))->getToken();
    }


    public function getUser()
    {
        $provider = config('auth.guards.api.provider');

        if (is_null($model = config('auth.providers.'.$provider.'.model'))) {
            throw new RuntimeException('Unable to determine authentication model from configuration.');
        }

        $user = (new $model)->findOrFail($this->getUserIdentifier());

        return $user;
    }
}
