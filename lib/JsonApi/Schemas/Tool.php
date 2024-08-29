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
        // $apiTool = new ToolApi($resource['url'], $resource['api_key']);

        $attributes = [
            'name'          => (string) $resource['name'],
            'description'   => (string) $resource['description'],
            'url'           => (string) $resource['url'],
            'preview'       => (string) $resource['preview_url'],
            'active'        => (bool) $resource['active'],
            'max-quota'     => (int) $resource['max_quota'],
            'quota-type'    => (string) $resource['quota_type'],
            'used-tokens'   => (int) count($resource->quotas),
            // 'metadata'      => $apiTool->getMetadata()
            'metadata' => null
        ];

        if ($isRoot) {
            $attributes['jwt_key'] = (string) $resource['jwt_key'];
            $attributes['api_key'] = (string) $resource['api_key'];
        }

        return $attributes;
    }

    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];

        return $relationships;
    }
}
