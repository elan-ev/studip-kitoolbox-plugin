<?php

namespace KIToolbox\JsonApi\Routes;

class Authority
{
    public static function canIndexTools($user) : Bool
    {
        return true;
    }

    public static function canShowTool($user, $resource) : Bool
    {
        return true;
    }

    public static function canUpdateTool($user) : Bool
    {
        return $GLOBALS['perm']->have_perm('root', $user->id);
    }

    public static function canDeleteTool($user) : Bool
    {
        return self::canUpdateTool($user);
    }

    public static function canCreateTool($user) : Bool
    {
        return self::canUpdateTool($user);
    }
}