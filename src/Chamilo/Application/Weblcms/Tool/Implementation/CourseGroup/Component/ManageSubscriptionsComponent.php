<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\CourseGroupSubscriptionsForm;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

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
                    implode('<br />', $courseGroup->get_errors());
            }
            else
            {
                $message = Translation::get(
                        'ObjectNotUpdated',
                        array('OBJECT' => Translation::get('CourseGroup')),
                        Utilities::COMMON_LIBRARIES
                    ) . '<br />' . implode('<br />', $courseGroup->get_errors());
            }
            $this->redirect($message, !$succes, array(self::PARAM_ACTION => self::ACTION_GROUP_DETAILS));
        }

        $html = [];

        $html[] = '<div class="pull-right">' . $this->getGroupButtonToolbarRenderer()->render() . '</div>';
        $html[] = '<div class="clearfix"></div>';
        $html[] = $form->toHtml();

        return implode(PHP_EOL, $html);
    }

    /**
     * Builds the group button toolbar for the management of a single group
     */
    protected function getGroupButtonToolbarRenderer()
    {
        $buttonToolbar = new ButtonToolBar();
        $hasEditRight = $this->is_allowed(WeblcmsRights::EDIT_RIGHT);

        if ($hasEditRight)
        {
            $buttonToolbar->addItem(
                new Button(
                    $this->getTranslator()->trans('SubscribePlatformGroupUsers', [], Manager::context()),
                    new FontAwesomeGlyph('group'),
                    $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_SUBSCRIBE_PLATFORM_GROUP_USERS,
                            self::PARAM_COURSE_GROUP => $this->getCurrentCourseGroup()->getId()
                        )
                    ),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );
        }

        $this->getCourseGroupDecoratorsManager()->addCourseGroupSubscriptionActions(
            $buttonToolbar, $this->getCurrentCourseGroup(), $this->getUser(), $hasEditRight
        );

        return new ButtonToolBarRenderer($buttonToolbar);
    }
}
