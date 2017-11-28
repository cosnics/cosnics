<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmitterSubmissions;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionsManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Description of submitter_submissions_user_browser_table_cell_renderer
 * 
 * @author Anthony Hurst (Hogeschool Gent)
 * @author Bert De Clercq (Hogeschool Gent)
 */
class SubmitterUserSubmissionsTableCellRenderer extends DataClassTableCellRenderer implements 
    TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $submission)
    {
        switch ($column->get_name())
        {
            case SubmitterUserSubmissionsTableColumnModel::PROPERTY_PUBLICATION_TITLE :
                $content_object = $submission->get_content_object();
                $title = $content_object ? $content_object->get_title() : Translation::get('ContentObjectUnknown');
                
                if ($this->get_component()->get_assignment()->get_visibility_submissions() ||
                     $this->get_component()->is_allowed(WeblcmsRights::EDIT_RIGHT) ||
                     $submission->get_submitter_id() == $this->get_component()->get_user_id())
                {
                    $url = $this->get_component()->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_VIEW_SUBMISSION, 
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $submission->get_publication_id(), 
                            Manager::PARAM_TARGET_ID => $submission->get_submitter_id(), 
                            Manager::PARAM_SUBMITTER_TYPE => \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER, 
                            Manager::PARAM_SUBMISSION => $submission->get_id()));
                    
                    return '<a href=\'' . $url . '\'>' . $title . '</a>';
                }
                return $title;
            
            case SubmitterUserSubmissionsTableColumnModel::PROPERTY_CONTENT_OBJECT_DESCRIPTION :
                $content_object = $submission->get_content_object();
                $description = $content_object ? $content_object->get_description() : Translation::get('ContentObjectUnknown');
                $description = strip_tags($description);
                $trimmedDescription = StringUtilities::getInstance()->createString($description)->truncate(100, '...');

                return $trimmedDescription;
            
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_DATE_SUBMITTED :
                return $this->format_date($submission->get_date_submitted());
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::PROPERTY_SCORE :
                return $this->get_score_from_submission($submission->get_id());
            case Manager::PROPERTY_NUMBER_OF_FEEDBACKS :
                return $this->get_number_of_feedback($submission->get_id());
        }
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
             $this->get_component()->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('ViewSubmission'), 
                    Theme::getInstance()->getCommonImagePath('Action/Browser'), 
                    $this->get_component()->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_VIEW_SUBMISSION, 
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $submission->get_publication_id(), 
                            Manager::PARAM_TARGET_ID => $submission->get_submitter_id(), 
                            Manager::PARAM_SUBMITTER_TYPE => \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER, 
                            Manager::PARAM_SUBMISSION => $submission->get_id())), 
                    ToolbarItem::DISPLAY_ICON));
        }
        
        if ($this->get_component()->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            if ($submission->get_content_object() &&
                 SubmissionsManager::is_downloadable($submission->get_content_object()))
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('DownloadSubmission'), 
                        Theme::getInstance()->getCommonImagePath('Action/Download'), 
                        $this->get_component()->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_DOWNLOAD_SUBMISSIONS, 
                                Manager::PARAM_SUBMISSION => $submission->get_id())), 
                        ToolbarItem::DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('DownloadNotPossible'), 
                        Theme::getInstance()->getCommonImagePath('Action/DownloadNa'), 
                        null, 
                        ToolbarItem::DISPLAY_ICON));
            }
            
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('DeleteSubmission'), 
                    Theme::getInstance()->getCommonImagePath('Action/Delete'), 
                    $this->get_component()->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_DELETE_SUBMISSION, 
                            Manager::PARAM_SUBMISSION => $submission->get_id())), 
                    ToolbarItem::DISPLAY_ICON, 
                    true));
        }
        return $toolbar->as_html();
    }

    /**
     * Formats a date.
     * 
     * @param $date type the date to be formatted.
     * @return the formatted representation of the date.
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

    /**
     * Retrieves the score of the submission.
     * 
     * @param $submission_id type the identity of the submission.
     * @return type the score of the submission.
     */
    private function get_score_from_submission($submission_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::PROPERTY_SUBMISSION_ID), 
            new StaticConditionVariable($submission_id));
        
        $trackers = DataManager::retrieves(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::class_name(), 
            new DataClassRetrievesParameters($condition));
        
        if ($trackers->size() > 0)
        {
            return $trackers->next_result()->get_score() . '%';
        }
        
        return null;
    }

    /**
     * Return the number of feedback a submission has.
     * 
     * @param $submission_id int The id of the submission
     * @return int The number of feedback
     */
    private function get_number_of_feedback($submission_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::class_name(), 
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::PROPERTY_SUBMISSION_ID), 
            new StaticConditionVariable($submission_id));
        
        return DataManager::count(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::class_name(), 
            new DataClassCountParameters($condition));
    }
}
