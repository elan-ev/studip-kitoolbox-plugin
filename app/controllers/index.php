<?php

use KIToolbox\JWT\JWTHandler;
use KIToolbox\ToolsApi\ToolApi;
use KIToolbox\models\CourseTool;
use KIToolbox\models\Quota;

class IndexController extends StudipController
{

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
    }

    public function index_action()
    {
        global $perm, $user;

        if (Navigation::hasItem('course/kitoolbox')) {
            Navigation::activateItem('course/kitoolbox/index');
            PageLayout::setBodyElementId('kitoolbox-index');
            PageLayout::setTitle('KI-Toolbox');
            PageLayout::disableSidebar();
            $this->isTeacher = $perm->have_studip_perm('tutor', Context::getId(), $user->id);
            $this->preferredLanguage = str_replace('_', '-', $_SESSION['_language']);
            $this->getHelpbarContent();

            $this->staticTexts = [
                'landing-page-teacher-settings' => Config::get()->KITOOLBOX_TEXT_LANDING_PAGE_TEACHER_SETTINGS,
                'landing-page-teacher' => Config::get()->KITOOLBOX_TEXT_LANDING_PAGE_TEACHER,
                'landing-page-student' => Config::get()->KITOOLBOX_TEXT_LANDING_PAGE_STUDENT,
                'rules-for-tools-template' => Config::get()->KITOOLBOX_TEXT_RULES_FOR_TOOLS_TEMPLATE,
                'essential' => Config::get()->KITOOLBOX_TEXT_ESSENTIAL,
            ];


            $this->staticTexts = json_encode($this->staticTexts, JSON_HEX_QUOT);
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

        PageLayout::setBodyElementId('kitoolbox-index');
        PageLayout::setTitle('KI-Toolbox');
        $cid = Request::get('cid') ?? Context::getId();
        $ktcid = Request::get('ktcid');
        if (empty($ktcid)) {
            PageLayout::postError(_("Ungültige Parameter zum Ausführen der Anfrage"));
            $this->redirect(\PluginEngine::getURL('kitoolbox', ['cid' => $cid], 'index'));
        }

        $courseTool = CourseTool::find($ktcid);

        if ($courseTool &&
            !empty($courseTool->tool->url) &&
            !empty($courseTool->tool->api_key) &&
            !empty($courseTool->tool->jwt_key))
        {
            $issuedToken = (new JWTHandler($courseTool))->issueToolToken();
            $toolUrl = $courseTool->tool->url;
            if (!filter_var($toolUrl, FILTER_VALIDATE_URL)) {
                PageLayout::postError(_("URL nicht zulässig! Bitte wenden Sie sich an Ihren Administrator."));
                $this->redirect(\PluginEngine::getURL('kitoolbox', ['cid' => $cid], 'index'));
                return;
            }

            $toolApiClient = new ToolApi($courseTool->tool->url, $courseTool->tool->api_key);

            if ($courseTool->tokenLimitReached($user->id) || $courseTool->tool->tokenLimitReached()) {
                PageLayout::postError(_("Die Tokens für dieses Tool wurden bereits verbraucht."));
                $this->redirect(\PluginEngine::getURL('kitoolbox', ['cid' => $cid], 'index'));
                return;
            }

            $this->createQuota($cid, $courseTool->id, $courseTool->tool->id);

            $res = $toolApiClient->AccessTool($issuedToken);

            if ($res['code'] === 200) {
                setcookie(
                    'token',
                    $issuedToken,
                    0,
                    '/',
                    $toolUrl,
                    true,
                    true
                );
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
