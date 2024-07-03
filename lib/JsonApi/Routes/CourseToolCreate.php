<?php

namespace KIToolbox\JsonApi\Routes;

use JsonApi\Routes\ValidationTrait;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;

use KIToolbox\models\CourseTool;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CourseToolCreate extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);
        $user = $this->getUser($request);
        $course = $this->getCourse($json);

        if (!$course) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canCreateCourseTool($user, $course)) {
            throw new AuthorizationFailedException();
        }

        $resource = $this->createCourseTool($json);

        return $this->getCreatedResponse($resource);
    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document´s top level.';
        }
        if (!self::arrayHas($json, 'data.attributes.tool-id')) {
            return 'New document must not have an `tool-id`.';
        }
        if (!self::arrayHas($json, 'data.attributes.course-id')) {
            return 'New document must not have an `course-id`.';
        }
    }

    private function createCourseTool(array $json): CourseTool
    {
        $tool_id = self::arrayGet($json, 'data.attributes.tool-id', '');
        $course_id = self::arrayGet($json, 'data.attributes.course-id', '');

        $tool = CourseTool::create([
            'tool_id'           => $tool_id,
            'course_id'         => $course_id,
            'active'            => true,
            'max_tokens'        => -1,
            'tokens_per_user'   => -1,
        ]);

        return $tool;
    }

    private function getCourse($json): ?\Course
    {
        $CourseData = self::arrayGet($json, 'data.relationships.course.data');

        return \Course::find($CourseData->id);
    }
}