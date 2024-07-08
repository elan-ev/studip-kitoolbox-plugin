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
 * @property string $name database column
 * @property string $description database column
 * @property string $url database column
 * @property string $jwt_key database column
 * @property int $max_quota database column
 * @property bool $active database column
 **/

class Tool extends SimpleORMap
{

    protected static function configure($config = [])
    {
        $config['db_table'] = 'kit_tools';

        $config['registered_callbacks']['before_delete'][] = 'cbBeforeDelete';

        parent::configure($config);
    }

    protected function cbBeforeDelete()
    {
        CourseTool::deleteBySQL('tool_id = ?', [$this->id]);
    }
}