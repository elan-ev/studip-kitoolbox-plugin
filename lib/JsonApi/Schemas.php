<?php

namespace KIToolbox\JsonApi;

trait Schemas
{
    public function registerSchemas(): array
    {
        return [
            \KIToolbox\Models\Tool::class => Schemas\Tool::class,
        ];
    }
}