<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataManager as AssignmentDataManager;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block displaying information about the assigment and
 *          access details
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Anthony Hurst (Hogeschool Gent)
 */
class AssignmentInformationBlock extends AssignmentReportingManager
{
    const STATISTICS_COUNT_SUBMITTED = 0;
    const STATISTICS_COUNT_LATE = 1;
    const STATISTICS_AVG_SCORE = 0;
    const STATISTICS_MIN_SCORE = 1;
    const STATISTICS_MAX_SCORE = 2;

    private static $column_details;

    private static $row_description;

    private static $row_number_of_submitters_submitted;

    private static $row_number_of_submitters_late;

    private static $row_score_average;

    private static $row_score_maximum;

    private static $row_score_minimum;

    private static $row_title;

    private $assignment;

    public function __construct($parent, $vertical = false)
    {
        $this->assignment = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(),
            $this->get_publication_id())->get_content_object();

        self :: $column_details = Translation :: get('Details');
        self :: $row_title = Translation :: get('Title');
        self :: $row_description = Translation :: get('Description');
        if (! $this->assignment->get_allow_group_submissions())
        {
            self :: $row_number_of_submitters_submitted = Translation :: get('NumberOfUsersSubmitted');
            self :: $row_number_of_submitters_late = Translation :: get('NumberOfUsersLate');
        }
        else
        {
            self :: $row_number_of_submitters_submitted = Translation :: get('NumberOfGroupsSubmitted');
            self :: $row_number_of_submitters_late = Translation :: get('NumberOfGroupsLate');
        }
        self :: $row_score_average = Translation :: get('AverageScore');
        self :: $row_score_minimum = Translation :: get('MinimumScore');
        self :: $row_score_maximum = Translation :: get('MaximumScore');
        parent :: __construct($parent, $vertical);
    }

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_categories(
            array(
                self :: $row_title,
                self :: $row_description,
                self :: $row_number_of_submitters_submitted,
                self :: $row_number_of_submitters_late,
                self :: $row_score_average,
                self :: $row_score_minimum,
                self :: $row_score_maximum));

        $params = array();
        $params[Application :: PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_COURSE;
        $params[Application :: PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager :: context();
        $params[\Chamilo\Application\Weblcms\Manager :: PARAM_COURSE] = $this->get_course_id();
        $params[\Chamilo\Application\Weblcms\Manager :: PARAM_TOOL] = ClassnameUtilities :: getInstance()->getClassNameFromNamespace(
            Assignment :: class_name(),
            true);
        $params[\Chamilo\Application\Weblcms\Manager :: PARAM_PUBLICATION] = $this->get_publication_id();
        $params[\Chamilo\Application\Weblcms\Manager :: PARAM_TOOL_ACTION] = \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager :: ACTION_BROWSE_SUBMITTERS;

        $redirect = new Redirect($params);
        $url_title = $redirect->getUrl();

        $number_of_submitters = $this->count_submitters($this->assignment->get_allow_group_submissions());
        $submitter_statistics = $this->compile_submitter_statistics($this->assignment);
        $score_statistics = $this->compile_score_statistics();

        $reporting_data->set_rows(array(self :: $column_details));
        $reporting_data->add_data_category_row(
            self :: $row_title,
            self :: $column_details,
            '<a href="' . $url_title . '">' . $this->assignment->get_title() . '</a>');
        $reporting_data->add_data_category_row(
            self :: $row_description,
            self :: $column_details,
            $this->assignment->get_description());
        $reporting_data->add_data_category_row(
            self :: $row_number_of_submitters_submitted,
            self :: $column_details,
            $submitter_statistics[self :: STATISTICS_COUNT_SUBMITTED] . '/' . $number_of_submitters);
        $reporting_data->add_data_category_row(
            self :: $row_number_of_submitters_late,
            self :: $column_details,
            $submitter_statistics[self :: STATISTICS_COUNT_LATE] . '/' . $number_of_submitters);
        $reporting_data->add_data_category_row(
            self :: $row_score_average,
            self :: $column_details,
            $this->format_score_html($score_statistics[self :: STATISTICS_AVG_SCORE]));
        $reporting_data->add_data_category_row(
            self :: $row_score_minimum,
            self :: $column_details,
            $this->format_score_html($score_statistics[self :: STATISTICS_MIN_SCORE]));
        $reporting_data->add_data_category_row(
            self :: $row_score_maximum,
            self :: $row_description,
            $this->format_score_html($score_statistics[self :: STATISTICS_MAX_SCORE]));

        return $reporting_data;
    }

    /**
     * Counts the number of submitters registered for the assignment.
     *
     * @param $is_group_assignment boolean Whether the assignment is a group assignment.
     * @return int the number of submitters registered for the assignment.
     */
    private function count_submitters($is_group_assignment)
    {
        if (! $is_group_assignment)
        {
            return count(
                \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_publication_target_users(
                    Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID),
                    null)->as_array());
        }
        else
        {
            $number_of_submitters = count(
                \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_publication_target_course_groups(
                    Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID),
                    null)->as_array());
            $number_of_submitters += count(
                \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_publication_target_platform_groups(
                    Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID),
                    null)->as_array());
            return $number_of_submitters;
        }
    }

    /**
     * Compiles and returns general submitter statistics about the current publication of the assignment.
     *
     * @param $assignment Assignment The assignment for which the statistics are needed.
     * @return array() The general statistics in the format {$number_submitters_submitted, $number_submitters_late}.
     */
    private function compile_submitter_statistics($assignment)
    {
        $submission_trackers_array = array();

        if (! $assignment->get_allow_group_submissions())
        {
            $submission_trackers_array[] = AssignmentDataManager :: retrieve_submissions_by_submitter_type(
                Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_USER)->as_array();
        }
        else
        {
            $submission_trackers_array[] = AssignmentDataManager :: retrieve_submissions_by_submitter_type(
                Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_COURSE_GROUP)->as_array();
            $submission_trackers_array[] = AssignmentDataManager :: retrieve_submissions_by_submitter_type(
                Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_PLATFORM_GROUP)->as_array();
        }

        $count_late = 0;
        $count_submitted = 0;

        foreach ($submission_trackers_array as $submission_trackers)
        {
            foreach ($submission_trackers as $submission_tracker)
            {
                $count_submitted ++;

                if ($submission_tracker['last_date'] > $assignment->get_end_time())
                {
                    $count_late ++;
                }
            }
        }

        return array(
            self :: STATISTICS_COUNT_SUBMITTED => $count_submitted,
            self :: STATISTICS_COUNT_LATE => $count_late);
    }

    /**
     * Compiles and returns the general score statistics for the current publication.
     *
     * @return array() The general score statistics in the format {$minimum_score, $average_score, $maximum_score}.
     */
    private function compile_score_statistics()
    {
        $submissions_condition = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: class_name(),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable(
                Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID)));
        $submission_trackers = \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: get_data(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: class_name(),
            null,
            $submissions_condition)->as_array();

        $submission_ids = array();
        foreach ($submission_trackers as $submission_tracker)
        {
            $submission_ids[] = $submission_tracker->get_id();
        }

        $score_condition = new InCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: class_name(),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: PROPERTY_SUBMISSION_ID),
            $submission_ids);
        $score_trackers = \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: get_data(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: class_name(),
            null,
            $score_condition)->as_array();

        $avg_score = $this->get_avg_score($score_trackers);
        $min_score = $this->get_min_score($score_trackers);
        $max_score = $this->get_max_score($score_trackers);

        return array(
            self :: STATISTICS_AVG_SCORE => $avg_score,
            self :: STATISTICS_MIN_SCORE => $min_score,
            self :: STATISTICS_MAX_SCORE => $max_score);
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_TABLE);
    }
}
