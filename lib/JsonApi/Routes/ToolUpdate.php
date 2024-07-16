<?php

namespace KIToolbox\JsonApi\Routes;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use KIToolbox\models\Tool;
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
        $updated_resource = $this->updateTool($resource, $json);

        return $this->getContentResponse($updated_resource);
    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document´s top level.';
        }

        if (!self::arrayHas($json, 'data.attributes.name')) {
            return 'Document must have an `name`.';
        }

        if (!self::arrayHas($json, 'data.attributes.url')) {
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
            $resource->url = $json['data']['attributes']['url'];
        }

        if (isset($json['data']['attributes']['preview-url'])) {
            $resource->preview_url = $json['data']['attributes']['preview-url'];
        }

        if (isset($json['data']['attributes']['key'])) {
            $resource->jwt_key = $json['data']['attributes']['key'];
        }

        if (isset($json['data']['attributes']['max-quota'])) {
            $resource->max_quota = $json['data']['attributes']['max-quota'];
        }

        if (isset($json['data']['attributes']['active'])) {
            $resource->active = $json['data']['attributes']['active'];
        }

        $resource->store();

        return $resource;
    }
}