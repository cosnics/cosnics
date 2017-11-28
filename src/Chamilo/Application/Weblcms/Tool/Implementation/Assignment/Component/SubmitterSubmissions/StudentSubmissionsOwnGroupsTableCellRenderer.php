<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmitterSubmissions;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager as CourseGroupDataManager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Description of student_submissions_browser_own_groups_table_cell_renderer
 * 
 * @author Anthony Hurst (Hogeschool Gent)
 */
class StudentSubmissionsOwnGroupsTableCellRenderer extends DataClassTableCellRenderer implements 
    TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $submission)
    {
        switch ($column->get_name())
        {
            case StudentSubmissionsOwnGroupsTableColumnModel::PROPERTY_PUBLICATION_TITLE :
                return $this->construct_title_link($submission);
            case StudentSubmissionsOwnGroupsTableColumnModel::PROPERTY_CONTENT_OBJECT_DESCRIPTION :
                $content_object = $submission->get_content_object();
                $description = $content_object ? $content_object->get_description() : Translation::get('ContentObjectUnknown');
                $description = strip_tags($description);
                $trimmedDescription = StringUtilities::getInstance()->createString($description)->truncate(100, '...');

                return $trimmedDescription;
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_DATE_SUBMITTED :
                return $this->format_date($submission->get_date_submitted());
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_SUBMITTER_ID :
                return $this->get_submitter_name($submission);
            case Manager::PROPERTY_NAME :
                return $this->get_group_name($submission);
            case Manager::PROPERTY_GROUP_MEMBERS :
                return $this->get_group_members($submission);
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::PROPERTY_SCORE :
                return $this->get_score_from_submission($submission->get_id());
            case Manager::PROPERTY_NUMBER_OF_FEEDBACKS :
                return $this->get_number_of_feedback($submission->get_id());
        }
    }

    /**
     * Returns the submitter name of the user who submitted the given submission.
     * 
     * @param $submission type
     * @return string The submitter name
     */
    private function get_submitter_name($submission)
    {
        return DataManager::retrieve_by_id(User::class_name(), $submission->get_user_id())->get_fullname();
    }

    /**
     * Returns the group members that are part of the submission.
     * 
     * @param $submission type
     * @return array
     */
    private function get_group_members($submission)
    {
        $retrieve_limit = 21;
        $order_properties = array();
        $order_properties[] = new OrderBy(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME));
        $order_properties[] = new OrderBy(
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME));
        $users = array();
        
        switch ($submission->get_submitter_type())
        {
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP :
                $user_ids = \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(
                    Group::class_name(), 
                    $submission->get_submitter_id())->get_users(true, true);
                $condition = new InCondition(
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID), 
                    $user_ids);
                $users = DataManager::retrieves(
                    User::class_name(), 
                    new DataClassRetrievesParameters($condition, $retrieve_limit, null, $order_properties))->as_array();
                break;
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP :
                $users = CourseGroupDataManager::retrieve_course_group_users(
                    $submission->get_submitter_id(), 
                    null, 
                    null, 
                    $retrieve_limit, 
                    $order_properties)->as_array();
                break;
        }
        
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
     * Retrieves the group name of the group that submitted the given submission.
     * 
     * @param $submission type
     * @return string The group name
     */
    private function get_group_name($submission)
    {
        switch ($submission->get_submitter_type())
        {
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP :
                $group = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
                    CourseGroup::class_name(), 
                    $submission->get_submitter_id());
                break;
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP :
                $group = \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(
                    Group::class_name(), 
                    $submission->get_submitter_id());
                break;
        }
        return $group->get_name();
    }

    /**
     * Retrieves the score from the submission with the given submission id.
     * 
     * @param $submission_id int
     * @return mixed The score or null
     */
    private function get_score_from_submission($submission_id)
    {
        $tracker = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::PROPERTY_SUBMISSION_ID), 
            new StaticConditionVariable($submission_id));
        
        $trackers = DataManager::retrieves(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::class_name(), 
            new DataClassRetrievesParameters($condition))->as_array();
        
        if (count($trackers) > 0)
        {
            $score_tracker = $trackers[0];
            return $score_tracker->get_score() . '%';
        }
        
        return null;
    }

    /**
     * Returns the number of feedback the submission with the given submission id has.
     * 
     * @param $submission_id int
     * @return int The number of feedback
     */
    private function get_number_of_feedback($submission_id)
    {
        $tracker = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::PROPERTY_SUBMISSION_ID), 
            new StaticConditionVariable($submission_id));
        
        return DataManager::count(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::class_name(), 
            new DataClassCountParameters($condition));
    }

    /**
     * Makes a hyperlink of the given submission.
     * 
     * @param $submission type
     * @return string The hyperlink
     */
    private function construct_title_link($submission)
    {
        if($submission->get_content_object())
        {
            return '<a href="' . $this->construct_title_url($submission) . '">' .
            $submission->get_content_object()->get_title() . '</a>';
        }

        return Translation::getInstance()->getTranslation('ContentObjectUnknown', null, Manager::context());
    }

    /**
     * Returns the url that links to the submission viewer for the given submission.
     * 
     * @param $submission type
     * @return string The url
     */
    private function construct_title_url($submission)
    {
        return $this->get_component()->get_url(
            array(
                Manager::PARAM_SUBMISSION => $submission->get_id(), 
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_VIEW_SUBMISSION, 
                Manager::PARAM_TARGET_ID => $submission->get_submitter_id(), 
                Manager::PARAM_SUBMITTER_TYPE => $submission->get_submitter_type()));
    }

    /**
     * Creates a toolbar with the appropriate actions
     * 
     * @param $submission type
     * @return string The HTML code that represents the actions.
     */
    public function get_actions($submission)
    {
        $toolbar = new Toolbar();
        if ($submission->get_user_id() == $this->get_component()->get_user_id() ||
             $this->get_component()->get_assignment()->get_visibility_submissions() ||
             $this->get_component()->is_allowed(WeblcmsRights::VIEW_RIGHT))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('ViewSubmission'), 
                    Theme::getInstance()->getCommonImagePath('Action/Browser'), 
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_SUBMISSION => $submission->get_id(), 
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_VIEW_SUBMISSION)), 
                    ToolbarItem::DISPLAY_ICON));
        }
        if ($this->get_component()->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('DownloadSubmission'), 
                    Theme::getInstance()->getCommonImagePath('Action/Download'), 
                    $this->get_component()->get_url(), 
                    ToolbarItem::DISPLAY_ICON));
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('DeleteSubmission'), 
                    Theme::getInstance()->getCommonImagePath('Action/Delete'), 
                    $this->get_component()->get_url(), 
                    ToolbarItem::DISPLAY_ICON));
        }
        return $toolbar->as_html();
    }

    /**
     * Formats the given date so it display red when it's greater than the end time of the assignment.
     * 
     * @param $date type
     * @return string The formatted date
     */
    private function format_date($date)
    {
        $formatted_date = DatetimeUtilities::format_locale_date(
            Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES), 
            $date);
        
        if ($date > $this->get_component()->get_assignment()->get_end_time())
        {
            return '<span style="color:red">' . $formatted_date . '</span>';
        }
        return $formatted_date;
    }
}
