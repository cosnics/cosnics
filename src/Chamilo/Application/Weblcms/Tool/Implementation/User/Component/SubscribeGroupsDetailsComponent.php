<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Table\GroupUsers\GroupUsersTable;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * $Id: user_group_subscribe_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.user.component
 */
class SubscribeGroupsDetailsComponent extends SubscribeGroupsTabComponent
{

    /**
     * Renders the content for the tab
     *
     * @return string
     */
    protected function renderTabContent()
    {
        $html = array();

        $html[] = $this->renderGroupDetails();
        $html[] = $this->renderGroupUsersTable();

        return implode($html, "\n");
    }

    /**
     * Renders the details for the currently selected group
     */
    protected function renderGroupDetails()
    {
        $group = $this->getCurrentGroup();

        $html = array();

        $html[] = '<div class="row">';
        $html[] = '<div class="col-sm-12">';
        $html[] = '<b>' . $this->getTranslation('Code') . ':</b> ' . $group->get_code();
        $html[] = '<br /><b>' . $this->getTranslation('Description') . ':</b> ' . $group->get_fully_qualified_name();
        $html[] = '<br /><b>' . $this->getTranslation('NumberOfUsers') . ':</b> ' . $group->count_users();
        $html[] = '<br /><b>' . $this->getTranslation('NumberOfSubgroups') . ':</b> ' . $group->count_subgroups(true);
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="row">';
        $html[] = '<div class="col-sm-12">';
        $html[] = '<div style="margin-top: 20px;">';
        $html[] = $this->getGroupButtonToolbarRenderer($group)->render();
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode($html, "\n");
    }

    /**
     * Renders the table of subgroups
     */
    protected function renderGroupUsersTable()
    {
        $table = new GroupUsersTable($this);

        $html = array();
        $html[] = '<div class="tab-content-header">';
        $html[] = '<h5>' . $this->getTranslation('Users') . '</h5>';
        $html[] = '</div>';

        $html[] = '<div>' . $this->buttonToolbarRenderer->render() . '</div>';
        $html[] = $table->as_html();

        return implode($html, "\n");
    }

    /**
     * Builds the group button toolbar for the management of a single group
     */
    protected function getGroupButtonToolbarRenderer(Group $group)
    {
        $buttonToolbar = new ButtonToolBar();

        $courseManagementRights = CourseManagementRights::getInstance();

        $isAllowed = $courseManagementRights->is_allowed_for_platform_group(
            CourseManagementRights :: TEACHER_DIRECT_SUBSCRIBE_RIGHT,
            $group->getId(),
            $this->get_course_id()
        );

        if (!in_array($group->getId(), $this->subscribedGroups) && ($this->getUser()->is_platform_admin() || $isAllowed))
        {
            $buttonToolbar->addItem(
                new Button(
                    $this->getTranslation('SubscribeGroup'),
                    '',
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_GROUPS,
                            self :: PARAM_OBJECTS => $group->getId())),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL,
                    false,
                    'btn-success'));
        }

        return new ButtonToolBarRenderer($buttonToolbar);
    }

    /**
     * Returns the condition for the table
     *
     * @param string $table_class_name
     *
     * @return Condition
     */
    public function get_table_condition($table_class_name)
    {
        $group = $this->getCurrentGroup();
        $subscribedUserIds = $group->get_users();

        $conditions = array();

        $conditions[] = new InCondition(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID),
            $subscribedUserIds);

        $conditionProperties = array();
        $conditionProperties[] = new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_FIRSTNAME);
        $conditionProperties[] = new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_LASTNAME);
        $conditionProperties[] = new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_USERNAME);
        $conditionProperties[] = new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_EMAIL);

        $searchCondition = $this->buttonToolbarRenderer->getConditions($conditionProperties);
        if ($searchCondition)
        {
            $conditions[] = $searchCondition;
        }

        if (count($conditions))
        {
            return new AndCondition($conditions);
        }
    }
}
