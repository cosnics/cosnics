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
    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(),
                $this->getPublication()->getContentObject()->get_title()
            )
        );

    }

    public function run() {

        $publication = $this->getPublication();
        $usersExtraTime = $this->getUserOvertimeService()->getUserOvertimeDataByPublication($publication);

        $parameters = [
            'HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(),
            'PUBLICATION_ID' => $publication->getId(),
            'USERS_OVERTIME' => $usersExtraTime,
            'LIST_USERS_AJAX_URL' => $this->getAjaxUrl(
                \Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Ajax\Manager::ACTION_LIST_USERS,
                [self::PARAM_PUBLICATION_ID => $pid]
            ),
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

    /**
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass|ContentObjectPublication
     */
    protected function getPublication()
    {
        $pid = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID) ? Request::get(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID) : Request::post(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);

        return \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(),
            $pid);
    }

}
