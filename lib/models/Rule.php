<?php

namespace KIToolbox\models;

use SimpleORMap;
use User;
use Course;

/**
 * KI-Toolbox Tools
 *
 * @author  Ron Lucke <lucke@elan-ev.de>
 * @license GPL2 or any later version
 * 
 * @property int $id database column
 * @property string $course_id database column
 * @property string $content database column
 * @property bool $released database column
 * @property string $last_edit_id database column
 * @property Course $course belongs_to Course
 * @property User $editor belongs_to User
 *
 **/

 class Rule extends SimpleORMap
 {
    protected static function configure($config = [])
    {
        $config['db_table'] = 'kit_rules';

        $config['belongs_to']['course'] = [
            'class_name' => Course::class,
            'foreign_key' => 'course_id',
        ];
        $config['belongs_to']['editor'] = [
            'class_name' => User::class,
            'foreign_key' => 'last_edit_id',
        ];

        parent::configure($config);
    }

    public static function findByCourseId($cid)
    {
        return self::findOneBySQL('course_id = ?', [$cid]);
    }
 }
