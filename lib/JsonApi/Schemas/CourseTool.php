<?php
namespace KIToolbox\JsonApi\Schemas;

use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class CourseTool extends \JsonApi\Schemas\SchemaProvider
{
    public const TYPE = 'kitoolbox-course-tools';

    const REL_TOOL = 'tool';
    const REL_COURSE = 'course';

    public function getId($resource): ?string
    {
        return $resource->id;
    }

    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'name'              => (string) $resource['tool']['name'],
            'description'       => (string) $resource['tool']['description'],
            'preview'           => (string) 'https://picsum.photos/300/200?grayscale&random=' . $resource['tool']['id'],
            'tool-id'           => (int) $resource['tool_id'],
            'course-id'         => (int) $resource['course_id'],
            'active'            => (bool) $resource['active'],
            'max-tokens'        => (string) $resource['max_tokens'],
            'tokens-per-user'   => (int) $resource['tokens_per_user']
        ];
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


        return $relationships;
    }
}