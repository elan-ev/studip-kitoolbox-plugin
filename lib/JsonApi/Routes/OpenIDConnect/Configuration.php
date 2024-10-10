<?php

namespace KIToolbox\JsonApi\Routes\OpenIDConnect;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use JsonApi\NonJsonApiController;

class Configuration extends NonJsonApiController
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $configuration = [
            'issuer' => $this->getUrl(''),
            'authorization_endpoint' => $this->getUrl('authorize'),
            'token_endpoint' => $this->getUrl('access_token'),
            'jwks_uri' => $this->getUrl('.well-known/jwks.json'),
            'userinfo_endpoint' => $this->getUrl('userinfo'),
            'response_types_supported' => ['code'],
            'subject_types_supported' => ['public'],
            'id_token_signing_alg_values_supported' => ['RS256'],
            'scopes_supported' => ['openid', 'profile', 'email'],
            'claims_supported' => [
                'sub',
                'name',
                'given_name',
                'family_name',
                'locale',
                'email',
                'email_verified',
            ],
        ];

        $response->getBody()->write(json_encode($configuration));

        return $response->withHeader('Content-Type', 'application/json');
    }

    function getUrl(string $path): string
    {
        $url = $GLOBALS['ABSOLUTE_URI_STUDIP'] . 'jsonapi.php/v1/kitoolbox';
        if ($path) {
            $url .= '/' . ltrim($path, '/');
        }

        return $url;
    }
}