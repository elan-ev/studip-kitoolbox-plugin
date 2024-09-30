<?php

namespace KIToolbox\OpenIDConnect;

use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;

class IdTokenResponse extends \OpenIDConnectServer\IdTokenResponse {

    protected function getBuilder(AccessTokenEntityInterface $accessToken, UserEntityInterface $userEntity)
    {
        if (class_exists("Lcobucci\JWT\Token\Builder")) {
            $claimsFormatter = \Lcobucci\JWT\Encoding\ChainedFormatter::withUnixTimestampDates();
            $builder = new \Lcobucci\JWT\Token\Builder(new \Lcobucci\JWT\Encoding\JoseEncoder(), $claimsFormatter);
        } else {
            $builder = new \Lcobucci\JWT\Builder();
        }

        // Since version 8.0 league/oauth2-server returns \DateTimeImmutable
        $expiresAt = $accessToken->getExpiryDateTime();
        if ($expiresAt instanceof \DateTime) {
            $expiresAt = \DateTimeImmutable::createFromMutable($expiresAt);
        }

        // Add required id_token claims
        $builder
            ->permittedFor($accessToken->getClient()->getIdentifier())
            ->issuedBy($GLOBALS['ABSOLUTE_URI_STUDIP'] . 'jsonapi.php/v1/kitoolbox')  // Only change
            ->issuedAt(new \DateTimeImmutable())
            ->expiresAt($expiresAt)
            ->relatedTo($userEntity->getIdentifier());

        return $builder;
    }
}
