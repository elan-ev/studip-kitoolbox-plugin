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
 * @property string $user_id database column
 * @property string $course_id database column
 * @property int $course_tool_id database column
 * @property int $tool_id database column
 * @property string $type database column
 **/

class Quota extends SimpleORMap
{

    protected static function configure($config = [])
    {
        $config['db_table'] = 'kit_quotas';

        $config['belongs_to']['user'] = [
            'class_name'  => \User::class,
            'foreign_key' => 'user_id',
            'assoc_foreign_key' => 'user_id',
        ];

        $config['belongs_to']['course'] = [
            'class_name'  => \Course::class,
            'foreign_key' => 'course_id',
            'assoc_foreign_key' => 'seminar_id',
        ];

        $config['belongs_to']['course_tool'] = [
            'class_name'  => CourseTool::class,
            'foreign_key' => 'course_tool_id',
        ];

        $config['belongs_to']['tool'] = [
            'class_name'  => Tool::class,
            'foreign_key' => 'tool_id',
        ];

        parent::configure($config);
    }
}