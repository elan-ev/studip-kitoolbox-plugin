<?php

namespace KIToolbox\JsonApi;

trait Routes
{
    public function registerAuthenticatedRoutes(\Slim\Routing\RouteCollectorProxy $group)
    {
        $group->get('/kitoolbox-tools', Routes\ToolsIndex::class);
        $group->post('/kitoolbox-tools', Routes\ToolCreate::class);
        $group->get('/kitoolbox-tools/{id}', Routes\ToolShow::class);
        $group->patch('/kitoolbox-tools/{id}', Routes\ToolUpdate::class);
        $group->delete('/kitoolbox-tools/{id}', Routes\ToolDelete::class);

        $group->get('/kitoolbox-course-tools', Routes\CourseToolsIndex::class);
        $group->get('/courses/{id}/kitoolbox-course-tools', Routes\CourseToolsByCourseIndex::class);
        $group->post('/kitoolbox-course-tools', Routes\CourseToolCreate::class);
        $group->get('/kitoolbox-course-tools/{id}', Routes\CourseToolShow::class);
        $group->patch('/kitoolbox-course-tools/{id}', Routes\CourseToolUpdate::class);
        $group->delete('/kitoolbox-course-tools/{id}', Routes\CourseToolDelete::class);
    }
    public function registerUnauthenticatedRoutes(\Slim\Routing\RouteCollectorProxy $group)
    {
    }
}