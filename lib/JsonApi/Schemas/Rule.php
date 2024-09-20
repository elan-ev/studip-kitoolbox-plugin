<?php
namespace KIToolbox\JsonApi\Schemas;

use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class Rule extends \JsonApi\Schemas\SchemaProvider
{
    public const TYPE = 'kitoolbox-rules';

    const REL_COURSE = 'course';

    const REL_EDITOR = 'editor';

    public function getId($resource): ?string
    {
        return $resource->id;
    }

    public function getAttributes($resource, ContextInterface $context): iterable
    {
        $attributes =  [
            'content'   => (string) $resource['content'],
            'released'  => (boolean) $resource['released'],
            'course-id' => (string) $resource['course_id']
        ];

        return $attributes;
    }

    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];

        $course = $resource->course;

        $relationships[self::REL_COURSE] = $course
        ? [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->createLinkToResource($course),
            ],
            self::RELATIONSHIP_DATA => $course,
        ]
        : [self::RELATIONSHIP_DATA => null];


        $editor = $resource->editor;

        $relationships[self::REL_EDITOR] = $editor
            ? [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($editor),
                ],
                self::RELATIONSHIP_DATA => $editor,
            ]
            : [self::RELATIONSHIP_DATA => null];


        return $relationships;
    }
}