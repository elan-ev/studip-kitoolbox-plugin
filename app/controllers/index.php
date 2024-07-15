<?php

use KIToolbox\JWT\JWTHandler;
use KIToolbox\models\CourseTool;
use KIToolbox\models\Quota;

class IndexController extends StudipController
{

    public function before_filter(&$action, &$args)
    {
        SimpleORMap::expireTableScheme();
        parent::before_filter($action, $args);
    }

    public function index_action()
    {
        global $perm, $user;

        if (Navigation::hasItem('course/kitoolbox')) {
            Navigation::activateItem('course/kitoolbox/index');
            PageLayout::setBodyElementId('kitoolbar-index');
            PageLayout::setTitle('KI-Toolbox');
            $this->isTeacher = $perm->have_studip_perm('tutor', Context::getId(), $user->id);
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
            $views->addLink('','',null, ['view-dummy-item' => true]);
            $sidebar->addWidget($views);
        }
    }

    protected function getHelpbarContent()
    {
        $description = 'Lorem ipsum';
        Helpbar::get()->addPlainText('', $description, '');
    }

    public function redirect_action()
    {
        global $user;

        PageLayout::setBodyElementId('kitoolbar-index');
        PageLayout::setTitle('KI-Toolbox');
        $cid = Request::get('cid') ?? Context::getId();
        $validated = false;
        if ($token = Request::get('token')) {
            $validated = JWTHandler::validateRefreshToken($token, $cid);
        }

        if ($validated) {
            $ktcid = JWTHandler::getClaims($token, 'ktcid');
            $courseTool = CourseTool::find($ktcid);

            if ($courseTool && !empty($courseTool->tool->url)) {
                $issuedToken = (new JWTHandler($courseTool))->issueToolToken();
                $toolUrl = $courseTool->tool->url;
                if (!filter_var($toolUrl, FILTER_VALIDATE_URL)) {
                    PageLayout::postError(_("URL nicht zulässig! Bitte wenden Sie sich an Ihren Administrator."));
                    $this->redirect(\PluginEngine::getURL('kitoolbox', ['cid' => $cid], 'index'));
                    return;
                }

                if ($courseTool->tokenLimitReached($user->id) || $courseTool->tool->tokenLimitReached()) {
                    PageLayout::postError(_("Die Tokens für dieses Tool wurden bereits verbraucht."));
                    $this->redirect(\PluginEngine::getURL('kitoolbox', ['cid' => $cid], 'index'));
                    return;
                }

                $this->createQuota($cid, $courseTool->id, $courseTool->tool->id);
                // session_start();
                // $_SESSION['token'] = $issuedToken;
                setcookie(
                    'token',
                    $issuedToken,
                    0,
                    '/',
                    $toolUrl,
                    true,
                    true
                );
                // header('Set-Cookie: name=token; Secure; Path=/; SameSite=None; Partitioned;');
                header('Location:' . $toolUrl);
                die;
            }
        }
        PageLayout::postError(_("Zugriff abgelehnt! Bitte versuche es erneut."));
        $this->redirect(\PluginEngine::getURL('kitoolbox', ['cid' => $cid], 'index'));
    }

    private function createQuota($cid, $course_tool_id, $tool_id)
    {
        global $perm, $user;
        if ($perm->have_studip_perm('tutor', Context::getId(), $user->id)) {
            return;
        }

        Quota::create([
            'user_id' => $user->id,
            'course_id' => $cid,
            'course_tool_id' => $course_tool_id,
            'tool_id' => $tool_id,
            'type' => 'token'
        ]);
    }
}
