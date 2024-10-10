<?php

namespace KIToolbox\JsonApi\Routes\OpenIDConnect;

use JsonApi\NonJsonApiController;
use KIToolbox\OpenIDConnect\AuthorizationServer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AccessToken extends NonJsonApiController
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $server = AuthorizationServer::getInstance();
        return $server->respondToAccessTokenRequest($request, $response);
    }
}