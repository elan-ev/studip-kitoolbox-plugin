<?php

namespace KIToolbox\JsonApi\Routes;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use KIToolbox\models\Rule;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RuleUpdate extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        $resource = Rule::find($args['id']);
        if (!$resource) {
            throw new RecordNotFoundException();
        }
        $user = $this->getUser($request);
        if (!Authority::canUpdateRule($user, $resource)) {
            throw new AuthorizationFailedException();
        }
        $json = $this->validate($request, $resource);
        $updated_resource = $this->updateRule($resource, $user, $json);

        return $this->getContentResponse($updated_resource);
    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document´s top level.';
        }

        if (!self::arrayHas($json, 'data.attributes.content')) {
            return 'Document must have `content`.';
        }
    }

    private function updateRule(Rule $resource, \User $user, array $json) : Rule
    {

        if (isset($json['data']['attributes']['released'])) {
            $resource->released = $json['data']['attributes']['released'];
        }

        if (isset($json['data']['attributes']['content'])) {
            $resource->content = $json['data']['attributes']['content'];
        }

        $resource->last_edit_id = $user->id;

        $resource->store();

        return $resource;
    }
}