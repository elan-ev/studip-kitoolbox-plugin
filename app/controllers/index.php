<?php
use CoursewareSnapshots\Models\Snapshot;
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
            $this->getHelpbarContent();
        }
        
    }


    protected function getHelpbarContent()
    {
        $description = 'Lorem ipsum';
        Helpbar::get()->addPlainText('', $description, '');
    }
}