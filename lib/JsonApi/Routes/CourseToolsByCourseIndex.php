<?php

namespace KIToolbox\JsonApi\Routes;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use KIToolbox\models\CourseTool;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CourseToolsByCourseIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];

    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);
        $course = \Course::find($args['id']);

        if (!$course) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canIndexCourseToolsByCourse($user, $course)) {
            throw new AuthorizationFailedException();
        }

        list($offset, $limit) = $this->getOffsetAndLimit();
        $resources = CourseTool::findBySQL('course_id = ? ORDER BY mkdate LIMIT ? OFFSET ?', [$course->id, $limit, $offset]);
        $resources = array_filter($resources, function($course_tool) {
            return $course_tool->tool->active;
        });

        return $this->getPaginatedContentResponse($resources, count($resources));
    }
}