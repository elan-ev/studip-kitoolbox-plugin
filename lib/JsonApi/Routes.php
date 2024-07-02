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
    }
    public function registerUnauthenticatedRoutes(\Slim\Routing\RouteCollectorProxy $group)
    {
    }
}