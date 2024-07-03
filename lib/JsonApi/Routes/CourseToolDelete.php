<?php

namespace KIToolbox\JsonApi\Routes;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use KIToolbox\models\CourseTool;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CourseToolDelete extends JsonApiController
{

    public function __invoke(Request $request, Response $response, $args)
    {

        $resource = CourseTool::find($args['id']);

        if (!$resource) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);

        if(!Authority::canDeleteCourseTool($user, $resource)) {
            throw new AuthorizationFailedException();
        }
        
        $resource->delete();

        return $this->getCodeResponse(204);
    }
}