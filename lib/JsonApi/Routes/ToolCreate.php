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
        if (!self::arrayHas($json, 'data.attributes.auth_method')) {
            return 'New document must have an `auth_method`.';
        }

        switch (self::arrayGet($json, 'data.attributes.auth_method', 'none')) {
            case 'oidc':
                if (!self::arrayHas($json, 'data.attributes.oidc_client_id')) {
                    return 'New document must have an `oidc_client_id`.';
                }
                if (!self::arrayHas($json, 'data.attributes.oidc_client_secret')) {
                    return 'New document must have an `oidc_client_secret`.';
                }
                if (!self::arrayHas($json, 'data.attributes.oidc_redirect_url')) {
                    return 'New document must have an `oidc_redirect_url`.';
                }
                break;
            case 'jwt':
                if (!self::arrayHas($json, 'data.attributes.jwt_key')) {
                    return 'New document must have an `jwt_key`.';
                }
                break;
        }

        if (!self::arrayHas($json, 'data.attributes.api_key')) {
            return 'New document must have an `api_key`.';
        }
        if (!self::arrayHas($json, 'data.attributes.url')) {
            return 'New document must have an `url`.';
        }
        if (!self::arrayHas($json, 'data.attributes.name')) {
            return 'New document must have an `name`.';
        }
        if (!self::arrayHas($json, 'data.attributes.description')) {
            return 'New document must have an `description`.';
        }
        if (!self::arrayHas($json, 'data.attributes.max-quota')) {
            return 'New document must have an `max-quota`.';
        }
    }

    private function createTool(array $json): Tool
    {
        $auth_method = self::arrayGet($json, 'data.attributes.auth_method', 'none');
        $oidc_client_id = self::arrayGet($json, 'data.attributes.oidc_client_id', '');
        $oidc_client_secret = self::arrayGet($json, 'data.attributes.oidc_client_secret', '');
        $oidc_redirect_url = self::arrayGet($json, 'data.attributes.oidc_redirect_url', '');
        $jwt_key = self::arrayGet($json, 'data.attributes.jwt_key', '');
        $api_key = self::arrayGet($json, 'data.attributes.api_key', '');
        $url = self::arrayGet($json, 'data.attributes.url', '');
        $preview_url = self::arrayGet($json, 'data.attributes.preview-url', '');
        $name = self::arrayGet($json, 'data.attributes.name', '');
        $description= self::arrayGet($json, 'data.attributes.description', '');
        $max_quota = self::arrayGet($json, 'data.attributes.max-quota', '-1');

        $tool = Tool::create([
            'name' => $name,
            'description' => $description,
            'auth_method' => $auth_method,
            'oidc_client_id' => $oidc_client_id,
            'oidc_client_secret' => $oidc_client_secret,
            'oidc_redirect_url' => $oidc_redirect_url,
            'api_key' => $api_key,
            'jwt_key' => $jwt_key,
            'url' => $url,
            'preview_url' => $preview_url,
            'quota_type' => 'token',
            'max_quota' => $max_quota
        ]);

        return $tool;
    }
}
