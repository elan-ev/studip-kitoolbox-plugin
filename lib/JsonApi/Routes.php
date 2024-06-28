<?php

namespace KIToolbox\JsonApi;

trait Routes
{
    public function registerAuthenticatedRoutes(\Slim\Routing\RouteCollectorProxy $group)
    {
        $group->get('/kitoolbox-tools', Routes\ToolsIndex::class);
        $group->post('/kitoolbox', Routes\ToolCreate::class);
        $group->patch('/kitoolbox/{id}', Routes\ToolUpdate::class);
        $group->delete('/kitoolbox/{id}', Routes\ToolDelete::class);
    }
    public function registerUnauthenticatedRoutes(\Slim\Routing\RouteCollectorProxy $group)
    {
    }
}