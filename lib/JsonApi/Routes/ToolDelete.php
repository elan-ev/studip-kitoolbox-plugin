<?php

namespace KIToolbox\JsonApi\Routes;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use KIToolbox\models\Tool;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ToolDelete extends JsonApiController
{

    public function __invoke(Request $request, Response $response, $args)
    {

        $resource = Tool::find($args['id']);

        if (!$resource) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);

        if(!Authority::canDeleteTool($user)) {
            throw new AuthorizationFailedException();
        }
        
        $resource->delete();

        return $this->getCodeResponse(204);
    }
}