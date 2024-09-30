<?php

namespace KIToolbox\OpenIDConnect;

use KIToolbox\OpenIDConnect\IdTokenResponse;
use KIToolbox\OpenIDConnect\Repositories\AccessTokenRepository;
use KIToolbox\OpenIDConnect\Repositories\AuthCodeRepository;
use KIToolbox\OpenIDConnect\Repositories\ClientRepository;
use KIToolbox\OpenIDConnect\Repositories\IdentityRepository;
use KIToolbox\OpenIDConnect\Repositories\RefreshTokenRepository;
use KIToolbox\OpenIDConnect\Repositories\ScopeRepository;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use OpenIDConnectServer\ClaimExtractor;

class AuthorizationServer
{
    /**
     * @var \League\OAuth2\Server\AuthorizationServer|null
     */
    private static $instance = null;

    private function __construct()
    {
        // Prevent instantiation
    }

    public static function getInstance(): \League\OAuth2\Server\AuthorizationServer
    {
        if (self::$instance === null) {
            self::$instance = self::createAuthorizationServer();
        }

        return self::$instance;
    }

    private static function createAuthorizationServer(): \League\OAuth2\Server\AuthorizationServer
    {
        require_once __DIR__ . '/../../vendor/autoload.php';

        // Init repositories
        $clientRepository = new ClientRepository();
        $scopeRepository = new ScopeRepository();
        $accessTokenRepository = new AccessTokenRepository();
        $authCodeRepository = new AuthCodeRepository();
        $refreshTokenRepository = new RefreshTokenRepository();

        $responseType = new IdTokenResponse(new IdentityRepository(), new ClaimExtractor());

        // Set up the authorization server
        $server = new \League\OAuth2\Server\AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            \Config::get()->getValue('KITOOLBOX_OIDC_PRIVATE_KEY'),
            \Config::get()->getValue('KITOOLBOX_OIDC_ENCRYPTION_KEY'),
            $responseType
        );

        // Enable the authentication code grant on the server
        $server->enableGrantType(
            new AuthCodeGrant(
                $authCodeRepository,
                $refreshTokenRepository,
                new \DateInterval('PT10M')  // Auth Code: 10 Minutes TTL
            ),
            new \DateInterval('PT1H') // Access Token: 1 hour TTL
        );

        return $server;
    }
}