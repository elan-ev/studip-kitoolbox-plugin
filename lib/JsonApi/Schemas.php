<?php

namespace KIToolbox\JsonApi;

trait Schemas
{
    public function registerSchemas(): array
    {
        return [
            \KIToolbox\models\Tool::class => Schemas\Tool::class,
            \KIToolbox\models\CourseTool::class => Schemas\CourseTool::class,
            \KIToolbox\models\Quota::class => Schemas\Quota::class,
            \KIToolbox\models\Rule::class => Schemas\Rule::class,
        ];
    }
}