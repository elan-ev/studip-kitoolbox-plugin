<?php

namespace KIToolbox\JsonApi\Routes;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use KIToolbox\models\Rule;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RuleShow extends JsonApiController
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $resource = Rule::find($args['id']);
        if (!$resource) {
            throw new RecordNotFoundException();
        }
        $user = $this->getUser($request);
        if (!Authority::canShowRule($user, $resource)) {
            throw new AuthorizationFailedException();
        }

        return $this->getContentResponse($resource);
    }
}