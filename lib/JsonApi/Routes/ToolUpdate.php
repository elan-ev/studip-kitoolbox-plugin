<?php

namespace KIToolbox\JsonApi\Routes;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use KIToolbox\Models\Tool;
use KIToolbox\JsonApi\Schemas\Tool as ToolSchema;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ToolUpdate extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        $resource = Tool::find($args['id']);
        if (!$resource) {
            throw new RecordNotFoundException();
        }
        $user = $this->getUser($request);
        if (!Authority::canUpdateTool($user)) {
            throw new AuthorizationFailedException();
        }
        $json = $this->validate($request, $resource);
        $resource = $this->updateTool($user, $resource, $json);

        return $this->getContentResponse($resource);
    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document´s top level.';
        }

        if (!self::arrayHas($json, 'data.name')) {
            return 'Document must have an `name`.';
        }

        if (!self::arrayHas($json, 'data.url')) {
            return 'Document must have an `url`.';
        }
    }

    private function updateTool(Tool $resource, array $json) : Tool
    {

        if (isset($json['data']['attributes']['name'])) {
            $resource->name = $json['data']['attributes']['name'];
        }

        if (isset($json['data']['attributes']['description'])) {
            $resource->description = $json['data']['attributes']['description'];
        }

        if (isset($json['data']['attributes']['url'])) {
            $resource->description = $json['data']['attributes']['url'];
        }

        $resource->store();

        return $resource;
    }
}