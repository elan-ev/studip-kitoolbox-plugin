<?php

class IndexController extends StudipController
{
    
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
    }

    public function index_action()
    {
        if (Navigation::hasItem('course/kitoolbox')) {
            Navigation::activateItem('course/kitoolbox/index');
            PageLayout::setBodyElementId('kitoolbar-index');
            PageLayout::setTitle('KI-Toolbox');
            $this->buildSidebar();
            $this->getHelpbarContent();
        }
        
    }

    private function buildSidebar()
    {
        global $perm, $user;

        if ( $perm->have_studip_perm('tutor', Context::getId(), $user->id)) {
            $sidebar = Sidebar::get();
            $views = new ViewsWidget();
            $views->setId('ki-toolbox-view-widget');
            $views->addLink('','',null, '');
            $sidebar->addWidget($views);
        }
    }

    protected function getHelpbarContent()
    {
        $description = 'Lorem ipsum';
        Helpbar::get()->addPlainText('', $description, '');
    }
}