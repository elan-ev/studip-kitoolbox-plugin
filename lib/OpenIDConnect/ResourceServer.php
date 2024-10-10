<?php

namespace KIToolbox\OpenIDConnect;

use KIToolbox\OpenIDConnect\Repositories\AccessTokenRepository;

class ResourceServer
{
    /**
     * @var \League\OAuth2\Server\ResourceServer|null
     */
    private static $instance = null;

    private function __construct()
    {
        // Prevent instantiation
    }

    public static function getInstance(): \League\OAuth2\Server\ResourceServer
    {
        if (self::$instance === null) {
            self::$instance = self::createResourceServer();
        }

        return self::$instance;
    }

    private static function createResourceServer(): \League\OAuth2\Server\ResourceServer
    {
        require_once __DIR__ . '/../../vendor/autoload.php';

        // Init our repositories
        $accessTokenRepository = new AccessTokenRepository();

        // Set up the ressource server
        return new \League\OAuth2\Server\ResourceServer(
            $accessTokenRepository,
            \Config::get()->getValue('KITOOLBOX_OIDC_PUBLIC_KEY')
        );
    }
}