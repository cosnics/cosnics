<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\User\UserCourseGroups;
use Chamilo\Core\User\UserDetails;
use Chamilo\Core\User\UserGroups;
use Chamilo\Libraries\Platform\Session\Request;

/**
 * $Id: user_details.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.user.component
 */
class DetailsComponent extends Manager
{

    public function run()
    {
        $html = array();

        $html[] = $this->render_header();

        if (Request :: get(\Chamilo\Application\Weblcms\Manager :: PARAM_USERS))
        {
            $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                Request :: get(\Chamilo\Application\Weblcms\Manager :: PARAM_USERS));
            $details = new UserDetails($user);
            $groups = new UserGroups($user->get_id());
            $course_groups = new UserCourseGroups($user->get_id(), $this->get_course_id());

            $html[] = $details->toHtml();
            $html[] = $groups->toHtml();
            $html[] = $course_groups->toHtml();
        }

        if (isset($_POST['user_id']))
        {
            foreach ($_POST['user_id'] as $user_id)
            {
                $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                    \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                    $user_id);
                $details = new UserDetails($user);
                $groups = new UserGroups($user->get_id());
                $course_groups = new UserCourseGroups($user->get_id(), $this->get_course_id());

                $html[] = $details->toHtml();
                $html[] = $groups->toHtml();
                $html[] = $course_groups->toHtml();
            }
        }

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Manager :: PARAM_USERS, self :: PARAM_TAB);
    }
}
