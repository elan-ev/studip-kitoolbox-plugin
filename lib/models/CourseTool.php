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
 * @property Tool $tool belongs_to Tool
 * @property Quota $quotas has_many Quota
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

        $config['has_many']['quotas'] = [
            'class_name' => Quota::class,
            'foreign_key' => 'id',
            'assoc_foreign_key' => 'course_tool_id',
        ];

        $config['registered_callbacks']['before_delete'][] = 'cbBeforeDelete';

        parent::configure($config);
    }

    protected function cbBeforeDelete()
    {
        Quota::deleteBySQL('course_tool_id = ?', [$this->id]);
    }

    public function maxTokensUnlimited()
    {
        return $this->max_tokens === -1;
    }

    public function tokensPerUserUnlimited()
    {
        return $this->tokens_per_user === -1;
    }

    public function tokenLimitReached($user_id)
    {
        if ($GLOBALS['perm']->have_studip_perm('tutor', $this->course->id, $user_id)) {
            return false;
        }

        $quotas = Quota::findBySQL('course_tool_id = ?', [$this->id]);

        $toolTokensLeft = $this->maxTokensUnlimited() || $this->max_tokens > count($quotas);

        $user_quotas = array_filter($quotas, function($quota) use ($user_id) {
            return $quota->user_id === $user_id;
        });

        $personalTokensLeft = $this->tokensPerUserUnlimited() || $this->tokens_per_user > count($user_quotas);

        return !($toolTokensLeft || $personalTokensLeft);
    }
}