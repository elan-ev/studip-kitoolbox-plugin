<?php
namespace KIToolbox\JsonApi\Schemas;

use KIToolbox\Models\Tool as ToolModel;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class Tool extends \JsonApi\Schemas\SchemaProvider
{
    public const TYPE = 'kitoolbox-tools';

    public function getId($resource): ?string
    {
        return $resource->id;
    }

    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'name'          => (string) $resource['name'],
            'description'   => (string) $resource['description'],
            'url'           => (string) $resource['url'],
            'active'        => (bool) $resource['active'],
            'key'           => (string) $resource['jwt_key'],
            'max-quota'     => (int) $resource['max_quota'],
        ];
    }

    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];

        return $relationships;
    }
}