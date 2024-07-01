<?php

require_once __DIR__ . '/vendor/autoload.php';

class KIToolbox extends StudIPPlugin implements StandardPlugin, SystemPlugin
{
    public function __construct()
    {
        parent::__construct();

        $perm = $GLOBALS['perm'];
        if ($perm->have_perm('root')) {
            $item = new Navigation($this->_('KI-Toolbox konfigurieren'), PluginEngine::getLink($this, array(), 'admin'));
            if (Navigation::hasItem('/admin/config') && !Navigation::hasItem('/admin/config/kitoolbox')) {
                Navigation::addItem('/admin/config/kitoolbox', $item);
            }
        }

        PageLayout::addScript($this->getPluginUrl() . '/dist/kitoolbox.js', [
            'type' => 'module',
            'rel'  => 'preload',
        ]);

        PageLayout::addScript($this->getPluginUrl() . '/dist/kitoolbox-admin.js', [
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
        $icon = new AutoNavigation(
            $this->getPluginName(),
            PluginEngine::getURL($this, array('cid' => $courseId, 'iconnav' => 'true'), 'index', true)
        );
        $icon->setImage(Icon::create('network2', 'inactive', ['title' => 'KI-Toolbox']));

        return $icon;
    }

    public function getInfoTemplate($courseId)
    {
        return null;
    }

}