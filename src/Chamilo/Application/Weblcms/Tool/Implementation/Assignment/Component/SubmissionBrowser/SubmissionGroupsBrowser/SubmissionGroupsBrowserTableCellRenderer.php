<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionGroupsBrowser;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser\SubmissionBrowserTableCellRenderer;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * This class is a cell renderer for a group submissions browser table
 *
 * @package application.weblcms.tool.assignment.php.component.submission_browser
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Anthony Hurst (Hogeschool Gent)
 * @author Bert De Clercq (Hogeschool Gent
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class SubmissionGroupsBrowserTableCellRenderer extends SubmissionBrowserTableCellRenderer
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Renders a cell for a given record
     *
     * @param $column \libraries\ObjectTableColumn
     *
     * @param mixed[string] $group
     *
     * @return String
     */
    public function render_cell($column, $group)
    {
        $component = $this->get_component();
        $submitter_id = $group[AssignmentSubmission :: PROPERTY_SUBMITTER_ID];

        switch ($column->get_name())
        {
            case Group :: PROPERTY_NAME :
                $group_name = $group[Group :: PROPERTY_NAME];

                if ($component->get_assignment()->get_visibility_submissions() == 1 ||
                     $component->is_allowed(WeblcmsRights :: EDIT_RIGHT) || ($this->is_subscribed_in_group(
                        $submitter_id,
                        $component->get_user_id())))
                {
                    $url = $component->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $component->get_publication_id(),
                            Manager :: PARAM_TARGET_ID => $submitter_id,
                            Manager :: PARAM_SUBMITTER_TYPE => $this->get_submitter_type(),
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => Manager :: ACTION_BROWSE_SUBMISSIONS));
                    return '<a href=\'' . $url . '\'>' . $group_name . '</a>';
                }
                return $group_name;
            case Manager :: PROPERTY_GROUP_MEMBERS :
                return $this->get_group_members($group);
        }

        return parent :: render_cell($column, $group);
    }

    /**
     * Returns the submitter type for this table cell renderer
     *
     * @return int
     */
    public function get_submitter_type()
    {
        return AssignmentSubmission :: SUBMITTER_TYPE_PLATFORM_GROUP;
    }

    /**
     * Returns whether or not the current user is the submitter or part of the submitter entity
     *
     * @param int $submitter_id - the id of the submitter entity
     * @param int $user_id - the id of the logged in user
     * @return bool
     */
    public function is_submitter($submitter_id, $user_id)
    {
        return $this->is_group_member(
            $this->get_component()->get_submitter($this->get_submitter_type(), $submitter_id),
            $user_id);
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Retrieves the members of the broup and displays them in a list.
     *
     * @param mixed $group the group whose members are to be displayed.
     * @return string
     */
    protected function get_group_members($group)
    {
        $submitter_id = $group[AssignmentSubmission :: PROPERTY_SUBMITTER_ID];

        $retrieve_limit = 21;

        $order_properties = array();
        $order_properties[] = new OrderBy(new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_LASTNAME));
        $order_properties[] = new OrderBy(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_FIRSTNAME));

        $user_ids = $this->retrieve_group_user_ids($submitter_id);

        $condition = new InCondition(new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_ID), $user_ids);

        $users = \Chamilo\Core\User\Storage\DataManager :: retrieves(
            User :: class_name(),
            new DataClassRetrievesParameters($condition, $retrieve_limit, null, $order_properties))->as_array();

        if (count($users) == 0)
        {
            return null;
        }

        $display_limit_breached = false;

        if (count($users) == $retrieve_limit)
        {
            $display_limit_breached = true;
            array_pop($users);
        }

        $html = array();
        $html[] = '<select style="width:180px">';

        foreach ($users as $user)
        {
            $html[] = '<option>' . $user->get_fullname() . '</option>';
        }

        if ($display_limit_breached)
        {
            $html[] = '<option>...</option>';
        }

        $html[] = '</select>';
        return implode(PHP_EOL, $html);
    }

    /**
     * Recursively iterates over a platform group and its subgroups to identify whether the current user is a member of
     * the platform group at any level.
     *
     * @param $group the platform group.
     * @param int $user_id
     *
     * @return boolean
     */
    protected function is_group_member($group, $user_id)
    {
        // DMTODO The GroupDataManager should provide a simpler way to find out
        if ($this->is_subscribed_in_group($group->get_id(), $user_id))
        {
            return true;
        }

        if ($group->has_children())
        {
            return $this->is_subgroup_member($group, $user_id);
        }
        return false;
    }

    /**
     * Recursively iterates over a platform group its subgroups to identify whether the current user is a member of the
     * platform group at any level.
     *
     * @param $group the platform group.
     * @param int $user_id
     *
     * @return boolean
     */
    protected function is_subgroup_member($group, $user_id)
    {
        foreach ($group->get_subgroups() as $subgroup)
        {
            if ($this->is_group_member($subgroup, $user_id))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns whether or not a user is subscribed in a group
     *
     * @param int $group_id
     * @param int $user_id
     *
     * @return bool
     */
    protected function is_subscribed_in_group($group_id, $user_id)
    {
        return \Chamilo\Core\Group\Storage\DataManager :: is_group_member($group_id, $user_id);
    }

    /**
     * Retrieves the user_ids of a group
     *
     * @param $group_id
     * @return int[]
     */
    protected function retrieve_group_user_ids($group_id)
    {
        return \Chamilo\Core\Group\Storage\DataManager :: retrieve_by_id(Group :: class_name(), $group_id)->get_users(
            true,
            true);
    }
}
