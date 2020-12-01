<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Manager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class UserOvertimeComponent extends Manager
{

    public function run() {
        $pid = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID) ? Request::get(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID) : Request::post(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);

        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(),
            $pid);

        $users = [];
        $usersExtraTime = $this->getUserOvertimeService()->getUserOvertimeDataByPublication($publication);
        $content_object = $publication->get_content_object();

        BreadcrumbTrail::getInstance()->add(
            new Breadcrumb(
                $this->get_url(),
                Translation::get('ToolContentObjectUpdateComponent', array('TITLE' => $content_object->get_title()))));

        $parameters = [
            'HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(),
            'PUBLICATION_ID' => $pid,
            'USERS' => $users, 'USERS_OVERTIME' => $usersExtraTime,
            'ADD_USER_OVERTIME_AJAX_URL' => $this->getAjaxUrl(
                \Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Ajax\Manager::ACTION_ADD_USER_OVERTIME
            ),
            'UPDATE_USER_OVERTIME_AJAX_URL' => $this->getAjaxUrl(
                \Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Ajax\Manager::ACTION_UPDATE_USER_OVERTIME
            ),
            'DELETE_USER_OVERTIME_AJAX_URL' => $this->getAjaxUrl(
            \Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Ajax\Manager::ACTION_DELETE_USER_OVERTIME
            )
        ];
        return $this->getTwig()->render(Manager::context() . ':UserOvertime.html.twig', $parameters);
    }
}
