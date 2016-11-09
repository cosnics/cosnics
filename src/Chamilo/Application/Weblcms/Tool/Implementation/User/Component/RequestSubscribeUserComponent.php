<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Form\CourseRequestForm;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseRequest;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: request.class.php 224 2010-04-06 14:40:30Z Yannick $
 *
 * @package applicatie.lib.weblcms.weblcms_manager.component
 */
class RequestSubscribeUserComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $course = $this->get_course();
        $request = new CourseRequest();

        $course_management_rights = CourseManagementRights :: getInstance();

        if (! $this->get_user()->is_platform_admin() && ! $course_management_rights->is_allowed(
            CourseManagementRights :: TEACHER_REQUEST_SUBSCRIBE_RIGHT,
            $this->get_course_id(),
            CourseManagementRights :: TYPE_COURSE,
            Request :: get(self :: PARAM_OBJECTS)))
        {
            throw new NotAllowedException();
        }

        $form = new CourseRequestForm(
            CourseRequestForm :: TYPE_CREATE,
            $this->get_url(),
            $course,
            $this,
            $request,
            false,
            Request :: get(self :: PARAM_OBJECTS));

        if ($form->validate())
        {
            $success_request = $form->create_request();

            $this->redirect(
                Translation :: get($success_request ? 'RequestSent' : 'RequestNotSent'),
                ($success_request ? false : true),
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_USER_BROWSER));
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     * Returns the available parameters for registration
     *
     * @return string[]
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_OBJECTS);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE_BROWSER,
                        self :: PARAM_TAB => Request :: get(self :: PARAM_TAB))),
                Translation :: get('UserToolUnsubscribeBrowserComponent')));

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_USER_BROWSER)),
                Translation :: get('UserToolSubscribeBrowserComponent')));
    }
}
