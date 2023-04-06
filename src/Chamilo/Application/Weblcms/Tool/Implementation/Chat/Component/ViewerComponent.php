<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Chat\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Chat\Manager;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * @package application.lib.weblcms.tool.chat.component
 */
require_once Path::getInstance()->getPluginPath() . '/phpfreechat/src/phpfreechat.class.php';

class ViewerComponent extends Manager
{

    public function run()
    {
//        // $html = array();
//        //
//        // $html[] = $this->render_header();
//        // $html[] = '<div class="alert alert-danger">' . Translation::getInstance()->getTranslation(
//        // 'ChatNotWorking', null, Manager::context()
//        // );
//        //
//        // $html[] = $this->render_footer();
//        //
//        // return implode(PHP_EOL, $html);
//        $course = $this->get_course();
//        $user = $this->get_user();
//
//        $course_rel_user = \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_course_user_relation_by_course_and_user(
//            $course->get_id(),
//            $user->get_id());
//
//        $params = array();
//
//        if (($course_rel_user && $course_rel_user->get_status() == 1) || $user->is_platform_admin())
//        {
//            $params["isadmin"] = true;
//        }
//
//        $params["data_public_url"] = Path::getInstance()->getPublicStoragePath(self::package() . '\Public', true);
//        $params["data_public_path"] = Path::getInstance()->getPublicStoragePath(self::package() . '\Public');
//        $params["data_private_path"] = Path::getInstance()->getLogPath() . 'phpfreechat';
//        $params["server_script_url"] = $_SERVER['REQUEST_URI'];
//        $params["serverid"] = $course->get_id();
//        $params["title"] = $course->get_title();
//        $params["nick"] = $user->get_username();
//        $params["frozen_nick"] = true;
//        $params["channels"] = array($course->get_title());
//        $params["max_channels"] = 1;
//        $params["theme"] = "blune";
//        $params["display_pfc_logo"] = false;
//        $params["display_ping"] = false;
//        $params["displaytabclosebutton"] = false;
//        $params["btn_sh_whosonline"] = false;
//        $params["btn_sh_smileys"] = false;
//        $params["displaytabimage"] = false;
//
//        $chat = new phpFreeChat($params);
//
//        $html = array();
//
//        $html[] = $this->render_header();
//
//        if (! function_exists('filemtime'))
//        {
//            $html[] = Translation::get('FileMTimeWarning');
//        }
//
//        $html[] = $chat->printChat(true);
//        $html[] = $this->render_footer();
//
//        return implode(PHP_EOL, $html);

        $html = [];

        $html[] = $this->render_header();
        $html[] = '<div class="alert alert-warning">' .
            $this->getTranslator()->trans('NoLongerSupported', [], Manager::context()) . '</div>';
        $html[] = $this->render_footer();

        return new Response(implode(PHP_EOL, $html));
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_tool_chat_viewer');
    }

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }
}
