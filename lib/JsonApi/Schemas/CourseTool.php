<?php
namespace KIToolbox\JsonApi\Schemas;

use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class CourseTool extends \JsonApi\Schemas\SchemaProvider
{
    public const TYPE = 'kitoolbox-course-tools';

    const REL_TOOL = 'tool';
    const REL_COURSE = 'course';
    const REL_QUOTAS = 'quotas';

    public function getId($resource): ?string
    {
        return $resource->id;
    }

    public function getAttributes($resource, ContextInterface $context): iterable
    {
        $attributes =  [
            'name'                      => (string) $resource['tool']['name'],
            'description'               => (string) $resource['tool']['description'],
            'preview'                   => (string) $resource['tool']['preview_url'],
            'tool-id'                   => (int) $resource['tool_id'],
            'course-id'                 => (int) $resource['course_id'],
            'active'                    => (bool) $resource['active'],
            'max-tokens'                => (int) $resource['max_tokens'],
            'max-tokens-unlimited'      => (bool) $resource->maxTokensUnlimited(),
            'tokens-per-user'           => (int) $resource['tokens_per_user'],
            'tokens-per-user-unlimited' => (int) $resource->tokensPerUserUnlimited(),
        ];

        switch ($resource['tool']['auth_method']) {
            case 'oidc':
                $attributes['redirect'] = $resource['tool']['url'];
                break;
            case 'jwt':
                $attributes['redirect'] =  \PluginEngine::getURL(
                    'kitoolbox',
                    [
                        'cid' => $resource['course_id'],
                        'ktcid' => $resource['id']
                    ],
                    'index/redirect'
                );
                break;
        }

        return $attributes;
    }

    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];

        $tool = $resource->tool;

        $relationships[self::REL_TOOL] = $tool
        ? [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->createLinkToResource($tool),
            ],
            self::RELATIONSHIP_DATA => $tool,
        ]
        : [self::RELATIONSHIP_DATA => null];


        $course = $resource->course;

        $relationships[self::REL_COURSE] = $course
        ? [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->createLinkToResource($course),
            ],
            self::RELATIONSHIP_DATA => $course,
        ]
        : [self::RELATIONSHIP_DATA => null];

        $quotas = $resource->quotas;

        $relationships[self::REL_QUOTAS] = $quotas
        ? [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->getRelationshipRelatedLink($resource, self::REL_QUOTAS),
            ],
            self::RELATIONSHIP_DATA => $quotas
        ]
        : [];


        return $relationships;
    }
}
