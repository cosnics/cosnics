<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionBrowser;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Abstract table cell renderer to browse submitters
 *
 * @package application.weblcms.tool.assignment.php.component.submission_browser
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Bert De Clercq (Hogeschool Gent)
 * @author Anthony Hurst (Hogeschool Gent)
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
abstract class SubmissionBrowserTableCellRenderer extends RecordTableCellRenderer implements
    TableCellRendererActionsColumnSupport
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
     * @param mixed[string] $submitter
     *
     * @return String
     */
    public function render_cell($column, $submitter)
    {
        $submitter_id = $submitter[AssignmentSubmission :: PROPERTY_SUBMITTER_ID];

        switch ($column->get_name())
        {
            case Manager :: PROPERTY_FIRST_SUBMISSION :
                if (is_null($submitter[Manager :: PROPERTY_FIRST_SUBMISSION]))
                {
                    return '-';
                }
                return $this->format_date($submitter[Manager :: PROPERTY_FIRST_SUBMISSION]);

            case Manager :: PROPERTY_LAST_SUBMISSION :
                if (is_null($submitter[Manager :: PROPERTY_LAST_SUBMISSION]))
                {
                    return '-';
                }
                return $this->format_date($submitter[Manager :: PROPERTY_LAST_SUBMISSION]);

            case Manager :: PROPERTY_NUMBER_OF_SUBMISSIONS :
                return $submitter[Manager :: PROPERTY_NUMBER_OF_SUBMISSIONS];

            case Manager :: PROPERTY_NUMBER_OF_FEEDBACKS :
                $feedbacks = $this->get_component()->get_submitter_feedbacks($this->get_submitter_type(), $submitter_id);

                if ($feedbacks != null)
                {
                    return $feedbacks['count'];
                }
                else
                {
                    return '0';
                }
        }

        return parent :: render_cell($column, $submitter);
    }

    /**
     * Renders the identifier of the table rows
     *
     * @param mixed[string] $row
     *
     * @return int
     */
    public function render_id_cell($row)
    {
        return $row[AssignmentSubmission :: PROPERTY_SUBMITTER_ID];
    }

    /**
     * Creates a toolbar with the appropriate actions
     *
     * @param User $submitter
     *
     * @return string
     */
    public function get_actions($submitter)
    {
        $toolbar = new Toolbar();

        $submitter_id = $submitter[AssignmentSubmission :: PROPERTY_SUBMITTER_ID];
        $is_submitter = $this->is_submitter($submitter_id, $this->get_component()->get_user_id());

        if ($is_submitter || $this->get_component()->get_assignment()->get_visibility_submissions() == 1 ||
             $this->get_component()->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $toolbar->add_item($this->get_view_submissions_action($submitter));
        }

        if ($this->get_component()->is_allowed(WeblcmsRights :: EDIT_RIGHT) &&
             $submitter[Manager :: PROPERTY_NUMBER_OF_SUBMISSIONS] > 0)
        {
            $toolbar->add_item($this->get_download_all_submissions_action($submitter));
        }

        if ($is_submitter)
        {
            $toolbar->add_item($this->get_submission_submit_action($submitter));
        }

        return $toolbar->as_html();
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Builds a toolbar item for the submission submit action
     *
     * @param mixed[string] $submitter
     *
     * @return ToolbarItem
     */
    protected function get_submission_submit_action($submitter)
    {
        $submitter_id = $submitter[AssignmentSubmission :: PROPERTY_SUBMITTER_ID];

        return new ToolbarItem(
            Translation :: get('SubmissionSubmit'),
            Theme :: getInstance()->getCommonImagePath('Action/Add'),
            $this->get_component()->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => Manager :: ACTION_SUBMIT_SUBMISSION,
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $this->get_component()->get_publication_id(),
                    Manager :: PARAM_SUBMITTER_TYPE => $this->get_submitter_type(),
                    Manager :: PARAM_TARGET_ID => $submitter_id)),
            ToolbarItem :: DISPLAY_ICON);
    }

    /**
     * Builds a toolbar item for the view submissions action
     *
     * @param mixed[string] $submitter
     *
     * @return ToolbarItem
     */
    protected function get_view_submissions_action($submitter)
    {
        $submitter_id = $submitter[AssignmentSubmission :: PROPERTY_SUBMITTER_ID];

        return new ToolbarItem(
            Translation :: get('ViewSubmissions'),
            Theme :: getInstance()->getCommonImagePath('Action/Browser'),
            $this->get_component()->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => Manager :: ACTION_BROWSE_SUBMISSIONS,
                    Manager :: PARAM_SUBMITTER_TYPE => $this->get_submitter_type(),
                    Manager :: PARAM_TARGET_ID => $submitter_id)),
            ToolbarItem :: DISPLAY_ICON);
    }

    /**
     * Builds a toolbar item for the download all submitions action
     *
     * @param mixed[string] $submitter
     *
     * @return ToolbarItem
     */
    protected function get_download_all_submissions_action($submitter)
    {
        $submitter_id = $submitter[AssignmentSubmission :: PROPERTY_SUBMITTER_ID];

        return new ToolbarItem(
            Translation :: get('DownloadAllSubmissions'),
            Theme :: getInstance()->getCommonImagePath('Action/Download'),
            $this->get_component()->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => Manager :: ACTION_DOWNLOAD_SUBMISSIONS,
                    Manager :: PARAM_TARGET_ID => $submitter_id)),
            ToolbarItem :: DISPLAY_ICON);
    }

    /**
     * Formats a date.
     *
     * @param int $date the date to be formatted.
     * @return string
     */
    protected function format_date($date)
    {
        $formatted_date = DatetimeUtilities :: format_locale_date(
            Translation :: get('DateTimeFormatLong', null, Utilities :: COMMON_LIBRARIES),
            $date);
        if ($date > $this->get_component()->get_assignment()->get_end_time())
        {
            return '<span style="color:red">' . $formatted_date . '</span>';
        }
        // $this->render_id_cell($object);
        return $formatted_date;
    }

    /**
     * **************************************************************************************************************
     * Abstract Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the submitter type
     *
     * @abstract
     *
     *
     *
     *
     *
     *
     *
     *
     * @return int
     */
    abstract public function get_submitter_type();

    /**
     * Returns whether or not the current user is the submitter or part of the submitter entity
     *
     * @abstract
     *
     *
     *
     *
     *
     *
     *
     *
     * @param int $submitter_id - the id of the submitter entity
     * @param int $user_id - the id of the logged in user
     * @return bool
     */
    abstract public function is_submitter($submitter_id, $user_id);
}
