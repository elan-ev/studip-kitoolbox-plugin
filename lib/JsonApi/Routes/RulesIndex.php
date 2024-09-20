<?php

namespace KIToolbox\JsonApi\Routes;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use KIToolbox\models\Rule;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RulesIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];

    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);

        if (!Authority::canIndexRule($user)) {
            throw new AuthorizationFailedException();
        }

        list($offset, $limit) = $this->getOffsetAndLimit();
        $resources = Rule::findBySQL('1 ORDER BY course_id LIMIT ? OFFSET ?', [$limit, $offset]);

        return $this->getPaginatedContentResponse($resources, count($resources));
    }
}