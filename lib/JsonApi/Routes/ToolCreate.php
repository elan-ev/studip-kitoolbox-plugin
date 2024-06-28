<?php

namespace KIToolbox\JsonApi\Routes;

use JsonApi\Routes\ValidationTrait;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;

use KIToolbox\Models\Tool;

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
    protected function validateResourceDocument($json, $data): ?string
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document´s top level.';
        }
        if (self::arrayHas($json, 'data.name')) {
            return 'New document must not have an `name`.';
        }
        if (self::arrayHas($json, 'data.url')) {
            return 'New document must not have an `url`.';
        }
    }

    private function createTool(array $json): Tool
    {
        $name = self::arrayGet($json, 'data.attributes.name', '');
        $description = self::arrayGet($json, 'data.attributes.description', '');
        $url = self::arrayGet($json, 'data.attributes.url', '');

        $tool = Tool::create([
            'name' => $name,
            'description' =>  $description,
            'url' => $url
        ]);

        return $tool;
    }
}