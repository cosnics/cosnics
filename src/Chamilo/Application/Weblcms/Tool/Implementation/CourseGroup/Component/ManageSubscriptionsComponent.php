<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\CourseGroupSubscriptionsForm;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: course_group_manage_subscriptions.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.tool.course_group.component
 */
class ManageSubscriptionsComponent extends TabComponent
{

    public function renderTabContent()
    {
        if (! $this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }
        
        $courseGroup = $this->getCurrentCourseGroup();
        $form = new CourseGroupSubscriptionsForm($courseGroup, $this->get_url(), $this);
        
        if ($form->validate())
        {
            $succes = $form->update_course_group_subscriptions();
            
            if ($succes)
            {
                $message = Translation::get(
                    'CourseGroupSubscriptionsUpdated', 
                    array('OBJECT' => Translation::get('CourseGroup'))) . '<br />' .
                     implode('<br />', $courseGroup->get_errors());
            }
            else
            {
                $message = Translation::get(
                    'ObjectNotUpdated', 
                    array('OBJECT' => Translation::get('CourseGroup')), 
                    Utilities::COMMON_LIBRARIES) . '<br />' . implode('<br />', $courseGroup->get_errors());
            }
            $this->redirect($message, ! $succes, array(self::PARAM_ACTION => self::ACTION_GROUP_DETAILS));
        }
        
        return $form->toHtml();
    }
}
