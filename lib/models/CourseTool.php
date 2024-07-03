<?php

namespace KIToolbox\models;

use SimpleORMap;

/**
 * KI-Toolbox Tools
 *
 * @author  Ron Lucke <lucke@elan-ev.de>
 * @license GPL2 or any later version
 * 
 * @property int $id database column
 * @property int $tool_id database column
 * @property string $course_id database column
 * @property bool $active database column
 * @property int $max_tokens database column
 * @property int $tokens_per_user database column
 * @property Tool $container belongs_to Tool
 **/

class CourseTool extends SimpleORMap
{

    protected static function configure($config = [])
    {
        $config['db_table'] = 'kit_course_tools';

        $config['belongs_to']['tool'] = [
            'class_name' => Tool::class,
            'foreign_key' => 'tool_id',
        ];

        $config['belongs_to']['course'] = [
            'class_name'  => \Course::class,
            'foreign_key' => 'course_id',
            'assoc_foreign_key' => 'seminar_id',
        ];

        parent::configure($config);
    }
}