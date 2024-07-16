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
 * @property string $preview_url database column
 * @property string $jwt_key database column
 * @property string $quota_type database column
 * @property int $max_quota database column
 * @property bool $active database column
 * @property Quota $quotas has_many Quota
 **/

class Tool extends SimpleORMap
{

    protected static function configure($config = [])
    {
        $config['db_table'] = 'kit_tools';

        $config['has_many']['course_tools'] = [
            'class_name' => CourseTool::class,
            'foreign_key' => 'id',
            'assoc_foreign_key' => 'tool_id',
        ];

        $config['has_many']['quotas'] = [
            'class_name' => Quota::class,
            'foreign_key' => 'id',
            'assoc_foreign_key' => 'tool_id',
        ];

        $config['registered_callbacks']['before_delete'][] = 'cbBeforeDelete';

        parent::configure($config);
    }

    protected function cbBeforeDelete()
    {
        CourseTool::deleteBySQL('tool_id = ?', [$this->id]);
        Quota::deleteBySQL('tool_id = ?', [$this->id]);
    }

    public function tokenLimitReached()
    {
        $quotas = Quota::findBySQL('tool_id = ?', [$this->id]);

        return $this->max_tokens <= count($quotas);
    }
}