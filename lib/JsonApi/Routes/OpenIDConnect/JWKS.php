<?php

namespace KIToolbox\JsonApi\Routes\OpenIDConnect;

use JsonApi\NonJsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class JWKS extends NonJsonApiController
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        // Get the public key
        $publicKey = \Config::get()->getValue('KITOOLBOX_OIDC_PUBLIC_KEY');
        $publicKey = openssl_pkey_get_public($publicKey);


        // Fetch key details of public key
        $keyDetails = openssl_pkey_get_details($publicKey);

        // Extract modulo and exponent
        $modulus = $this->base64url_encode($keyDetails['rsa']['n']);
        $exponent = $this->base64url_encode($keyDetails['rsa']['e']);

        $jwks = [
            "keys" => [
                [
                    "alg" => "RS256",
                    "kty" => "RSA",
                    "use" => "sig",
                    "n" => $modulus,
                    "e" => $exponent,
                    "kid" => "1",
                ]
            ]
        ];

        $response->getBody()->write(json_encode($jwks));
        return $response->withHeader('Content-Type', 'application/json');
    }

    function base64url_encode($data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

}