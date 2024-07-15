<?php

class AdminController extends StudipController
{
    
    public function before_filter(&$action, &$args)
    {
        SimpleORMap::expireTableScheme();
        parent::before_filter($action, $args);

        if ($GLOBALS['user']->perms !== 'root') {
            throw new AccessDeniedException();
        }

        Navigation::activateItem('/admin/config/kitoolbox');
    }

    public function index_action()
    {
        PageLayout::setTitle('KI-Toolbox Konfiguration');
    }
}