<?php

require_once __DIR__ . '/vendor/autoload.php';

class KIToolbox extends StudIPPlugin implements StandardPlugin
{
    public function __construct()
    {
        parent::__construct();

        PageLayout::addScript($this->getPluginUrl() . '/dist/kitoolbox.js', [
            'type' => 'module',
            'rel'  => 'preload',
        ]);

        PageLayout::addStylesheet($this->getPluginUrl() . '/dist/kitoolbox.css');
    }

    public function perform($unconsumedPath)
    {
        $trails_root  = $this->getPluginPath() . '/app';

        $dispatcher         = new Trails_Dispatcher($trails_root,
            rtrim(PluginEngine::getURL($this, [], ''), '/'),
            'index');

        $dispatcher->current_plugin = $this;
        $dispatcher->dispatch($unconsumedPath);
    }

    public function getPluginName()
    {
        return 'KI-Toolbox';
    }

    public function getTabNavigation($courseId)
    {
        $tabs = array();

        $nav = new Navigation($this->getPluginName(),PluginEngine::getURL($this, [], 'index'));
        $tabs['kitoolbox'] = $nav;
        $nav->addSubNavigation('index', new Navigation(
            $this->getPluginName(),
            PluginEngine::getURL($this, [], 'index')
        ));
        return $tabs;
    }

    public function getIconNavigation($courseId, $last_visit, $user_id)
    {
    }

    public function getInfoTemplate($courseId)
    {
        return null;
    }

}