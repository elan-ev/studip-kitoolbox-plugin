<?php

namespace KIToolbox\JsonApi\Routes;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use KIToolbox\models\CourseTool;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CourseToolUpdate extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        $resource = CourseTool::find($args['id']);
        if (!$resource) {
            throw new RecordNotFoundException();
        }
        $user = $this->getUser($request);
        if (!Authority::canUpdateCourseTool($user, $resource)) {
            throw new AuthorizationFailedException();
        }
        $json = $this->validate($request, $resource);
        $updated_resource = $this->updateCourseTool($resource, $json);

        return $this->getContentResponse($updated_resource);
    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document´s top level.';
        }

        if (!self::arrayHas($json, 'data.attributes.tool-id')) {
            return 'Document must have an `tool-id`.';
        }

        if (!self::arrayHas($json, 'data.attributes.course-id')) {
            return 'Document must have an `course-id`.';
        }
    }

    private function updateCourseTool(CourseTool $resource, array $json) : CourseTool
    {

        if (isset($json['data']['attributes']['active'])) {
            $resource->active = $json['data']['attributes']['active'];
        }

        if (isset($json['data']['attributes']['max-tokens'])) {
            $resource->max_tokens = $json['data']['attributes']['max-tokens'];
        }

        if (isset($json['data']['attributes']['tokens-per-user'])) {
            $resource->tokens_per_user = $json['data']['attributes']['tokens-per-user'];
        }

        $resource->store();

        return $resource;
    }
}