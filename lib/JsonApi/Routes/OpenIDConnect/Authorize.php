<?php

namespace KIToolbox\JsonApi\Routes\OpenIDConnect;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\NonJsonApiController;
use KIToolbox\OpenIDConnect\AuthorizationServer;
use KIToolbox\OpenIDConnect\Entities\UserEntity;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Authorize extends NonJsonApiController
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $server = AuthorizationServer::getInstance();

        // Validate the HTTP request and return an AuthorizationRequest object.
        // The auth request object can be serialized into a user's session
        $authRequest = $server->validateAuthorizationRequest($request);

        $GLOBALS['auth']->login_if('nobody' === $GLOBALS['user']->id);
        if (!$GLOBALS['perm']->have_perm('user')) {
            throw new AuthorizationFailedException();
        }

        // Once the user has logged in set the user on the AuthorizationRequest
        $authRequest->setUser(new UserEntity($GLOBALS['user']->username));

        // Assume approval is given as we show no user approval prompt
        $authRequest->setAuthorizationApproved(true);

        // Return the HTTP redirect response
        return $server->completeAuthorizationRequest($authRequest, $response);
    }
}