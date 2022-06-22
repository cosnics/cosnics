<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\CourseGroupSubscriptionsForm;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package application.lib.weblcms.tool.course_group.component
 */
class ManageSubscriptionsComponent extends TabComponent
{

    public function renderTabContent()
    {
        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $courseGroup = $this->getCurrentCourseGroup();
        $form = new CourseGroupSubscriptionsForm(
            $courseGroup, $this->get_url(), $this, $this->getCourseGroupDecoratorsManager()
        );

        if ($form->validate())
        {
            $succes = $form->update_course_group_subscriptions();

            if ($succes)
            {
                $message = Translation::get(
                        'CourseGroupSubscriptionsUpdated',
                        array('OBJECT' => Translation::get('CourseGroup'))
                    ) . '<br />' .
                    implode('<br />', $courseGroup->getErrors());
            }
            else
            {
                $message = Translation::get(
                        'ObjectNotUpdated',
                        array('OBJECT' => Translation::get('CourseGroup')),
                        StringUtilities::LIBRARIES
                    ) . '<br />' . implode('<br />', $courseGroup->getErrors());
            }
            $this->redirectWithMessage($message, !$succes, array(self::PARAM_ACTION => self::ACTION_GROUP_DETAILS));
        }

        return $form->toHtml();
    }
}
