<?php

namespace KIToolbox\JsonApi\Routes;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use KIToolbox\Models\Tool;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ToolsIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];

    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);

        if(!Authority::canIndexTools($user)) {
            throw new AuthorizationFailedException();
        }
        list($offset, $limit) = $this->getOffsetAndLimit();
        $resources = Tool::findBySQL('1 ORDER BY mkdate LIMIT ? OFFSET ?', [$limit, $offset]);

        return $this->getPaginatedContentResponse($resources, count($resources));
    
    }
}