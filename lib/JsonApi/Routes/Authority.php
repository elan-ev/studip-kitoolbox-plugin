<?php

namespace KIToolbox\JsonApi\Routes;

class Authority
{
    public static function canIndexTools($user) : bool
    {
        return true;
    }

    public static function canShowTool($user, $resource) : bool
    {
        return true;
    }

    public static function canUpdateTool($user) : bool
    {
        return $GLOBALS['perm']->have_perm('root', $user->id);
    }

    public static function canDeleteTool($user) : bool
    {
        return self::canUpdateTool($user);
    }

    public static function canCreateTool($user) : bool
    {
        return self::canUpdateTool($user);
    }


    public static function canCreateCourseTool($user, $course): bool
    {
        return $GLOBALS['perm']->have_studip_perm('tutor', $course->id, $user->id);
    }

    public static function canDeleteCourseTool($user, $resource): bool
    {
        return $GLOBALS['perm']->have_studip_perm('tutor', $resource->course_id, $user->id);
    }

    public static function canShowCourseTool($user, $resource): bool
    {
        return $GLOBALS['perm']->have_studip_perm('autor', $resource->course_id, $user->id);
    }

    public static function canIndexCourseTools($user): bool
    {
        return $GLOBALS['perm']->have_perm('root', $user->id);
    }

    public static function canUpdateCourseTool($user, $resource): bool
    {
        return $GLOBALS['perm']->have_studip_perm('tutor', $resource->course_id, $user->id);
    }

    public static function canIndexCourseToolsByCourse($user, $course): bool
    {
        return $GLOBALS['perm']->have_studip_perm('autor', $course->id, $user->id);
    }
}