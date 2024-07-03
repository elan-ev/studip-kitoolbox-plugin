<?php

namespace KIToolbox\JsonApi;

trait Schemas
{
    public function registerSchemas(): array
    {
        return [
            \KIToolbox\models\Tool::class => Schemas\Tool::class,
            \KIToolbox\models\CourseTool::class => Schemas\CourseTool::class,
        ];
    }
}