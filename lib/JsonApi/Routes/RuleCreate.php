<?php

namespace KIToolbox\JsonApi\Routes;

use JsonApi\Routes\ValidationTrait;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;

use KIToolbox\models\Rule;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RuleCreate extends JsonApiController
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

        if (!Authority::canCreateRule($user, $course)) {
            throw new AuthorizationFailedException();
        }

        $resource = $this->createRule($json, $user, $course);

        return $this->getCreatedResponse($resource);
    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document´s top level.';
        }
        if (!self::arrayHas($json, 'data.attributes.course-id')) {
            return 'New document must not have an `course-id`.';
        }
    }

    private function createRule(array $json, \User $user, \Course $course): Rule
    {
        $content = self::arrayGet($json, 'data.attributes.content', '');
        $released = self::arrayGet($json, 'data.attributes.released', false);

        $rule = Rule::create([
            'course_id'         => $course->id,
            'content'           => $content,
            'released'          => $released,
            'last_edit_id'      => $user->id
        ]);

        return $rule;
    }

    private function getCourse($json): ?\Course
    {
        $CourseId = self::arrayGet($json, 'data.attributes.course-id');

        return \Course::find($CourseId);
    }
}