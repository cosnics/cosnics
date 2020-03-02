<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\Subscribed\SubscribedUserTable;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.lib.weblcms.tool.course_group.component
 */
class DetailsComponent extends TabComponent implements TableSupport
{

    /**
     * Builds the group button toolbar for the management of a single group
     */
    protected function getGroupButtonToolbarRenderer()
    {
        $translator = Translation::getInstance();
        $theme = Theme::getInstance();

        $courseGroup = $this->getCurrentCourseGroup();

        $buttonToolbar = new ButtonToolBar();
        $managementButtonGroup = new ButtonGroup();

        if ($courseGroup->is_self_registration_allowed() && !$courseGroup->is_member($this->getUser()))
        {
            $buttonToolbar->addItem(
                new Button(
                    $translator->getTranslation('SubscribeToGroup', array(), Manager::context()), '',
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_USER_SELF_SUBSCRIBE)),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL, false, 'btn-success'
                )
            );
        }

        if ($courseGroup->is_self_unregistration_allowed() && $courseGroup->is_member($this->getUser()))
        {
            $buttonToolbar->addItem(
                new Button(
                    $translator->getTranslation('UnSubscribeFromGroup', array(), Manager::context()), '',
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_USER_SELF_UNSUBSCRIBE)),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL, false, 'btn-danger'
                )
            );
        }

        if ($this->is_allowed(WeblcmsRights::DELETE_RIGHT))
        {
            $managementButtonGroup->addButton(
                new Button(
                    $translator->getTranslation('Export', null, Utilities::COMMON_LIBRARIES),
                    new FontAwesomeGlyph('upload'),
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_EXPORT_SUBSCRIPTIONS_OVERVIEW)),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $managementButtonGroup->addButton(
                new Button(
                    $translator->getTranslation('Delete', array(), Utilities::COMMON_LIBRARIES),
                    new FontAwesomeGlyph('times'),
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_DELETE_COURSE_GROUP)),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL, $translator->getTranslation(
                    'DeleteConfirm', array('NAME' => $courseGroup->get_name()), Manager::context()
                )
                )
            );
        }

        $buttonToolbar->addButtonGroup($managementButtonGroup);

        if ($courseGroup->is_member($this->getUser()) || $this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $navigateToOptions = new DropdownButton(
                $translator->getTranslation('NavigateTo', array(), Manager::context())
            );

            //            if ($courseGroup->get_document_category_id())
            //            {
            //                $type_name = 'Document';
            //
            //                $params = array();
            //                $params[Application::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::context();
            //                $params[Application::PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
            //                $params[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $courseGroup->get_course_code();
            //                $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] = $type_name;
            //                $params[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION] = \Chamilo\Application\Weblcms\Tool\Implementation\Document\Manager::ACTION_BROWSE;
            //                $params[\Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY] = $courseGroup->get_document_category_id();
            //                $url = $this->get_url($params);
            //
            //                $namespace = \Chamilo\Application\Weblcms\Tool\Manager::get_tool_type_namespace($type_name);
            //                $navigateToOptions->addSubButton(
            //                    new SubButton(
            //                        $translator->getTranslation('DocumentCategory', null, Manager::context()),
            //                        Theme::getInstance()->getImagePath($namespace, 'Logo/16'),
            //                        $url,
            //                        ToolbarItem::DISPLAY_ICON_AND_LABEL));
            //            }

            if ($navigateToOptions->hasButtons())
            {
                $buttonToolbar->addItem($navigateToOptions);
            }
        }

        return new ButtonToolBarRenderer($buttonToolbar);
    }

    /**
     * Returns the table condition
     *
     * @param string $table_class_name
     *
     * @return Condition
     */
    public function get_table_condition($table_class_name)
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_USERNAME), '*' . $query . '*'
            );

            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME), '*' . $query . '*'
            );

            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME), '*' . $query . '*'
            );

            return new OrCondition($conditions);
        }

        return null;
    }

    /**
     * Handles the unsubscribe action
     *
     * @param CourseGroup $course_group
     */
    protected function handleUnsubscribeAction($course_group)
    {
        $users = $this->getRequest()->get(\Chamilo\Application\Weblcms\Manager::PARAM_USERS);

        if ($users)
        {
            if (!is_array($users))
            {
                $users = array($users);
            }

            foreach ($users as $user)
            {
                $course_group->unsubscribe_users($user);

                $userObject = new User();
                $userObject->setId($user);
                $this->getCourseGroupDecoratorsManager()->unsubscribeUser($course_group, $userObject);
            }

            $message = Translation::get(count($users) > 1 ? 'UsersUnsubscribed' : 'UserUnsubscribed');
            $this->redirect(
                $message, false, array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_GROUP_DETAILS,
                    self::PARAM_COURSE_GROUP => $course_group->get_id()
                )
            );
        }
    }

    /**
     * Renders the details of the course group
     *
     * @param CourseGroup $currentCourseGroup
     *
     * @return array
     */
    protected function renderDetails($currentCourseGroup)
    {
        $html = array();

        $html[] = '<div class="container-fluid">';

        $html[] = '<div class="row">';
        $html[] = '<div class="col-sm-12">';
        $html[] = $currentCourseGroup->get_description();
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="row">';
        $html[] = '<div class="col-sm-12">';

        $html[] = '<b>' . Translation::get('NumberOfMembers') . ':</b> ' . $currentCourseGroup->count_members();
        $html[] = '<br /><b>' . Translation::get('MaximumMembers') . ':</b> ' .
            $currentCourseGroup->get_max_number_of_members();
        $html[] = '<br /><b>' . Translation::get('SelfRegistrationAllowed') . ':</b> ' .
            ($currentCourseGroup->is_self_registration_allowed() ? Translation::get(
                'ConfirmYes', null, Utilities::COMMON_LIBRARIES
            ) : Translation::get('ConfirmNo', null, Utilities::COMMON_LIBRARIES));
        $html[] = '<br /><b>' . Translation::get('SelfUnRegistrationAllowed') . ':</b> ' .
            ($currentCourseGroup->is_self_unregistration_allowed() ? Translation::get(
                'ConfirmYes', null, Utilities::COMMON_LIBRARIES
            ) : Translation::get('ConfirmNo', null, Utilities::COMMON_LIBRARIES));
        $html[] = '<br /><b>' . Translation::get('RandomlySubscribed') . ':</b> ' .
            ($currentCourseGroup->is_random_registration_done() ? Translation::get(
                'ConfirmYes', null, Utilities::COMMON_LIBRARIES
            ) : Translation::get('ConfirmNo', null, Utilities::COMMON_LIBRARIES));

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="row">';
        $html[] = '<div class="col-sm-12">';
        $html[] = '<div style="margin-top: 20px;">';
        $html[] = $this->getGroupButtonToolbarRenderer()->render();
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders the integration actions for a given
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup $courseGroup
     *
     * @return string
     */
    protected function renderIntegrations(CourseGroup $courseGroup)
    {
        $html = array();

        $integrationLinksButtonToolbar = new ButtonToolBar();
        $renderer = new ButtonToolBarRenderer($integrationLinksButtonToolbar);

        $this->getCourseGroupDecoratorsManager()->addCourseGroupActions(
            $integrationLinksButtonToolbar, $courseGroup, $this->getUser(), $this->is_allowed(WeblcmsRights::EDIT_RIGHT)
        );

        if ($integrationLinksButtonToolbar->hasItems())
        {
            $html[] = '<div class="tab-content-header">';
            $html[] =
                '<h5>' . Translation::getInstance()->getTranslation('Integrations', null, Manager::context()) . '</h5>';
            $html[] = '</div>';
            $html[] = $renderer->render();
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders the content for the tab
     *
     * @return string
     *
     * @throws ObjectNotExistException
     */
    protected function renderTabContent()
    {
        if ($this->isCurrentGroupRoot())
        {
            return null;
        }

        $currentCourseGroup = $this->getCurrentCourseGroup();
        $this->handleUnsubscribeAction($currentCourseGroup);

        $html = array();

        $html[] = $this->renderDetails($currentCourseGroup);
        $html[] = $this->renderIntegrations($currentCourseGroup);
        $html[] = $this->renderUsersTable();

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders the users table
     *
     * @return string
     */
    protected function renderUsersTable()
    {
        $courseGroup = $this->getCurrentCourseGroup();
        if (!$courseGroup->is_member($this->getUser()) && !$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            return null;
        }

        $table = new SubscribedUserTable($this);

        $html = array();

        $html[] = '<div class="tab-content-header">';
        $html[] = '<h5>' . Translation::getInstance()->getTranslation('Users', null, Manager::context()) . '</h5>';
        $html[] = '</div>';
        $html[] = $table->as_html();

        return implode(PHP_EOL, $html);
    }
}
