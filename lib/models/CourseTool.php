<?php

namespace KIToolbox\Models;

use SimpleORMap;

/**
 * KI-Toolbox Tools
 *
 * @author  Ron Lucke <lucke@elan-ev.de>
 * @license GPL2 or any later version
 * 
 * @property int $id database column
 * @property int $tool_id database column
 * @property string $cid database column
 * @property bool $active database column
 * @property int $max_tokens database column
 * @property int $tokens_per_user database column
 **/

class CourseTool extends SimpleORMap
{

    protected static function configure($config = [])
    {
        $config['db_table'] = 'kit_course_tools';

        parent::configure($config);
    }
}