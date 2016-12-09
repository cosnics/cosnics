<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseEntityRelation;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Cache\DataClassCache;

/**
 * $Id: subscribe.class.php 218 2009-11-13 14:21:26Z kariboe $
 *
 * @package application.lib.weblcms.weblcms_manager.component
 */
/**
 * Weblcms component which allows the user to manage his or her course subscriptions
 */
class GroupSubscribeComponent extends Manager
{
    const PARAM_RETURN_TO_COMPONENT = 'return';

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            throw new \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException();
        }

        $course = $this->get_course();
        $group_ids = $this->getRequest()->get(self :: PARAM_OBJECTS);

        if (isset($group_ids) && ! is_array($group_ids))
        {
            $group_ids = array($group_ids);
        }

        if (isset($course))
        {
            if (isset($group_ids) && count($group_ids) > 0)
            {
                $failures = 0;

                $course_management_rights = CourseManagementRights :: get_instance();

                $parent_group_id = null;

                foreach ($group_ids as $group_id)
                {
                    if($this->isGroupSubscribed($group_id))
                    {
                        $failures++;
                        continue;
                    }

                    if (! $this->get_user()->is_platform_admin() && ! $course_management_rights->is_allowed_for_platform_group(
                        CourseManagementRights :: TEACHER_DIRECT_SUBSCRIBE_RIGHT,
                        $group_id,
                        $course->get_id()))
                    {
                        $failures ++;
                        continue;
                    }

                    if (! $this->get_parent()->subscribe_group_to_course(
                        $course,
                        $group_id,
                        CourseEntityRelation :: STATUS_STUDENT))
                    {
                        $failures ++;
                    }

                    if (! $parent_group_id)
                    {
                        $group = \Chamilo\Core\Group\Storage\DataManager :: retrieve_by_id(
                            \Chamilo\Core\Group\Storage\DataClass\Group :: class_name(),
                            $group_id);
                        $parent_group_id = $group->get_parent_id();
                    }

                    DataClassCache::truncate(CourseEntityRelation::class_name());
                }

                if ($failures == 0)
                {
                    $success = true;

                    if (count($group_ids) == 1)
                    {
                        $message = 'GroupSubscribedToCourse';
                    }
                    else
                    {
                        $message = 'GroupsSubscribedToCourse';
                    }
                }
                elseif ($failures == count($group_ids))
                {
                    $success = false;

                    if (count($group_ids) == 1)
                    {
                        $message = 'GroupNotSubscribedToCourse';
                    }
                    else
                    {
                        $message = 'GroupsNotSubscribedToCourse';
                    }
                }
                else
                {
                    $success = false;
                    $message = 'PartialGroupsNotSubscribedToCourse';
                }

                $returnAction = $this->getRequest()->get(self::PARAM_RETURN_TO_COMPONENT);
                $returnAction = !empty($returnAction) ? $returnAction : self::ACTION_SUBSCRIBE_GROUP_DETAILS;

                $this->redirect(
                    Translation :: get($message),
                    ($success ? false : true),
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => $returnAction,
                        \Chamilo\Application\Weblcms\Manager :: PARAM_GROUP => $parent_group_id));
            }
            else
            {
                return $this->display_error_page(htmlentities(Translation :: get('NoGroupsSelected')));
            }
        }
        else
        {
            return $this->display_error_page(htmlentities(Translation :: get('NoCourseSelected')));
        }
    }
}
