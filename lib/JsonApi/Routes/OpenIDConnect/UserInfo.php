<?php

namespace KIToolbox\JsonApi\Routes\OpenIDConnect;

use JsonApi\NonJsonApiController;
use KIToolbox\OpenIDConnect\Entities\UserEntity;
use KIToolbox\OpenIDConnect\ResourceServer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserInfo extends NonJsonApiController
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $server = ResourceServer::getInstance();

        // Validate access token
        $request = $server->validateAuthenticatedRequest($request);

        // Get user claims
        $user = new UserEntity($request->getAttribute('oauth_user_id'));
        $claims = $user->getClaims();
        $claims['sub'] = $user->getIdentifier();

        // Create response with user claims
        $response->getBody()->write(json_encode($claims));

        return $response->withHeader('Content-Type', 'application/json');
    }
}