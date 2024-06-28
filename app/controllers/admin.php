<?php

class AdminController extends StudipController
{
    
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
        Navigation::activateItem('/admin/config/kitoolbox');
    }

    public function index_action()
    {
        
    }
}