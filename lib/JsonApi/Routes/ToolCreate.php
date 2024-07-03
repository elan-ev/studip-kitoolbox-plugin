<?php

namespace KIToolbox\JsonApi\Routes;

use JsonApi\Routes\ValidationTrait;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;

use KIToolbox\models\Tool;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ToolCreate extends JsonApiController
{
    use ValidationTrait;
    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);
        $user = $this->getUser($request);

        if (!Authority::canCreateTool($user)) {
            throw new AuthorizationFailedException();
        }

        $resource = $this->createTool($json);

        return $this->getCreatedResponse($resource);
    }
    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document´s top level.';
        }
        if (!self::arrayHas($json, 'data.attributes.key')) {
            return 'New document must not have an `key`.';
        }
        if (!self::arrayHas($json, 'data.attributes.url')) {
            return 'New document must not have an `url`.';
        }
    }

    private function createTool(array $json): Tool
    {
        $key = self::arrayGet($json, 'data.attributes.key', '');
        $url = self::arrayGet($json, 'data.attributes.url', '');

        //TODO: get name, description, max_quota from tool url
        $name = 'dummy';
        $description= 'lorem ipsum';
        $max_quota = 42;

        $tool = Tool::create([
            'name' => $name,
            'description' => $description,
            'jwt_key' => $key,
            'url' => $url,
            'max_quota' => $max_quota
        ]);

        return $tool;
    }
}