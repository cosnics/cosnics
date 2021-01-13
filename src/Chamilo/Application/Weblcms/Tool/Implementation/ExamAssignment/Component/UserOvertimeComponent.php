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

    /**
     * @param BreadcrumbTrail $breadcrumbtrail
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(),
                $this->getContentObjectPublication()->getContentObject()->get_title()
            )
        );

    }

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function run()
    {
        $publication = $this->getContentObjectPublication();
        $usersExtraTime = $this->getUserOvertimeService()->getUserOvertimeDataByPublication($publication);

        $parameters = [
            'HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(),
            'PUBLICATION_ID' => $publication->getId(),
            'USERS_OVERTIME' => $usersExtraTime,
            'LIST_USERS_AJAX_URL' => $this->getAjaxUrl(
                \Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Ajax\Manager::ACTION_LIST_USERS
            ),
            'ADD_USER_OVERTIME_AJAX_URL' => $this->getAjaxUrl(
                \Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Ajax\Manager::ACTION_ADD_USER_OVERTIME
            ),
            'UPDATE_USER_OVERTIME_AJAX_URL' => $this->getAjaxUrl(
                \Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Ajax\Manager::ACTION_UPDATE_USER_OVERTIME
            ),
            'DELETE_USER_OVERTIME_AJAX_URL' => $this->getAjaxUrl(
            \Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Ajax\Manager::ACTION_DELETE_USER_OVERTIME
            ),
            'SET_MULTIPLE_USERS_OVERTIME_AJAX_URL' => $this->getAjaxUrl(
                \Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Ajax\Manager::ACTION_SET_MULTIPLE_USERS_OVERTIME
            )
        ];
        return $this->getTwig()->render(Manager::context() . ':UserOvertime.html.twig', $parameters);
    }
}
