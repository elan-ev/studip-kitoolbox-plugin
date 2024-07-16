<?php
namespace KIToolbox\JsonApi\Schemas;

use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class Tool extends \JsonApi\Schemas\SchemaProvider
{
    public const TYPE = 'kitoolbox-tools';

    public function getId($resource): ?string
    {
        return $resource->id;
    }

    public function getAttributes($resource, ContextInterface $context): iterable
    {
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
            'used-tokens'   => (int) count($resource->quotas)
        ];

        if ($isRoot) {
            $attributes['key'] = (string) $resource['jwt_key'];
        }

        return $attributes;
    }

    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];

        return $relationships;
    }
}