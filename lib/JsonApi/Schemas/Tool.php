<?php
namespace KIToolbox\JsonApi\Schemas;

use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use KIToolbox\ToolsApi\ToolApi;

class Tool extends \JsonApi\Schemas\SchemaProvider
{
    public const TYPE = 'kitoolbox-tools';

    public function getId($resource): ?string
    {
        return $resource->id;
    }

    public function getAttributes($resource, ContextInterface $context): iterable
    {
        require_once __DIR__ . '../../../../vendor/autoload.php';
        $userId = $this->currentUser->id;
        $isRoot = $GLOBALS['perm']->have_perm('root', $userId);

        $attributes = [
            'name'          => (string) $resource['name'],
            'description'   => (string) $resource['description'],
            'url'           => (string) $resource['url'],
            'preview'       => (string) $resource['preview_url'],
            'active'        => (bool) $resource['active'],
            'max-quota'     => (int) $resource['max_quota'],
            'quota-type'    => (string) $resource['quota_type'],
            'used-tokens'   => (int) count($resource->quotas),
            'metadata'      => null
        ];

        if ($isRoot) {
            $attributes['auth_method']          = (string) $resource['auth_method'];
            $attributes['oidc_client_id']       = $resource['oidc_client_id'];
            $attributes['oidc_client_secret']   = (string) $resource['oidc_client_secret'];
            $attributes['oidc_redirect_url']    = (string) $resource['oidc_redirect_url'];
            $attributes['jwt_key']              = (string) $resource['jwt_key'];
            $attributes['api_key']              = (string) $resource['api_key'];
            $apiTool = null;
            if (!empty($resource['url']) && !empty($resource['api_key'])) {
                $apiTool = new ToolApi($resource['url'], $resource['api_key']);
                $attributes['metadata'] = $apiTool->getMetadata();
            }
            
        }

        return $attributes;
    }

    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];

        return $relationships;
    }
}
