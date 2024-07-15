<?php
namespace KIToolbox\JsonApi\Schemas;

use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class Quota extends \JsonApi\Schemas\SchemaProvider
{
    public const TYPE = 'kitoolbox-quotas';

    const REL_TOOL = 'tool';
    const REL_COURSE_TOOL = 'course-tool';
    const REL_COURSE = 'course';
    const REL_USER = 'user';

    public function getId($resource): ?string
    {
        return $resource->id;
    }

    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'type'  => (string) $resource['type'],
        ];
    }


    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];

        $tool = $resource->tool;
        $relationships[self::REL_COURSE_TOOL] = $tool && $this->shouldInclude($context, self::REL_TOOL)
        ? [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->createLinkToResource($tool),
            ],
            self::RELATIONSHIP_DATA => $tool,
        ]
        : [self::RELATIONSHIP_DATA => null];

        $course_tool = $resource->course_tool;
        $relationships[self::REL_COURSE_TOOL] = $course_tool && $this->shouldInclude($context, self::REL_COURSE_TOOL)
        ? [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->createLinkToResource($course_tool),
            ],
            self::RELATIONSHIP_DATA => $course_tool,
        ]
        : [self::RELATIONSHIP_DATA => null];


        $course = $resource->course;
        $relationships[self::REL_COURSE] = $course && $this->shouldInclude($context, self::REL_COURSE)
        ? [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->createLinkToResource($course),
            ],
            self::RELATIONSHIP_DATA => $course,
        ]
        : [self::RELATIONSHIP_DATA => null];


        $user = $resource->user;
        $relationships[self::REL_USER] = $user
            ? [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($user),
                ],
                self::RELATIONSHIP_DATA => $user,
            ]
            : [self::RELATIONSHIP_DATA => null];

        return $relationships;
    }

}