<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block displaying information about the assignment and the
 *          user
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Anthony Hurst (Hogeschool Gent)
 */
abstract class AssignmentSubmitterInformationBlock extends AssignmentReportingManager
{

    private static $COLUMN_DETAILS;

    private static $ROW_ASSIGNMENT;

    private static $ROW_NUMBER_FEEDBACK;

    private static $ROW_NUMBER_SUBMISSIONS_LATE;

    private static $ROW_SCORE_AVERAGE;

    private static $ROW_SCORE_MAXIMUM;

    private static $ROW_SCORE_MINIMUM;

    private $ROW_SUBMITTER;

    /**
     * Instatiates the column headers.
     *
     * @param $parent type Pass-through variable. Please refer to parent class(es) for more details. &param type
     *            $vertical Pass-through variable. Please refer to parent class(es) for more details.
     */
    public function __construct($parent, $vertical)
    {
        self :: $COLUMN_DETAILS = Translation :: get('Details');

        $this->ROW_SUBMITTER = $this->define_row_submitter_title();
        self :: $ROW_ASSIGNMENT = Translation :: get('AssignmentTitle');
        self :: $ROW_NUMBER_SUBMISSIONS_LATE = Translation :: get('NumberOfSubmissionsLate');
        self :: $ROW_NUMBER_FEEDBACK = Translation :: get('NumberOfSubmissionsFeedbacks');
        self :: $ROW_SCORE_AVERAGE = Translation :: get('AverageScore');
        self :: $ROW_SCORE_MINIMUM = Translation :: get('MinimumScore');
        self :: $ROW_SCORE_MAXIMUM = Translation :: get('MaximumScore');
        parent :: __construct($parent, $vertical);
    }

    /**
     * Defines the title of the submitter row.
     *
     * @return string The title of the row.
     */
    abstract protected function define_row_submitter_title();

    public function count_data()
    {
        $reporting_data = new ReportingData();

        $reporting_data->set_categories(
            array(
                $this->ROW_SUBMITTER,
                self :: $ROW_ASSIGNMENT,
                self :: $ROW_NUMBER_SUBMISSIONS_LATE,
                self :: $ROW_NUMBER_FEEDBACK,
                self :: $ROW_SCORE_MINIMUM,
                self :: $ROW_SCORE_AVERAGE,
                self :: $ROW_SCORE_MAXIMUM));
        $reporting_data->set_rows(array(self :: $COLUMN_DETAILS));

        $assignment = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(),
            $this->get_publication_id())->get_content_object();

        $title = $this->generate_assignment_name_link($this->get_publication_id());

        $submitter = $this->generate_submitter_name_link($this->get_submitter_type(), $this->get_target_id());

        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: class_name(),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($this->get_publication_id()));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: class_name(),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: PROPERTY_SUBMITTER_TYPE),
            new StaticConditionVariable($this->get_submitter_type()));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: class_name(),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: PROPERTY_SUBMITTER_ID),
            new StaticConditionVariable($this->get_target_id()));

        $condition = new AndCondition($conditions);
        $submission_trackers = \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: get_data(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: class_name(),
            null,
            $condition)->as_array();

        $count_submissions_late = 0;
        $count_submissions_feedback = 0;
        $submission_ids = array();
        foreach ($submission_trackers as $submission_tracker)
        {
            $submission_ids[] = $submission_tracker->get_id();
            if ($submission_tracker->get_date_submitted() > $assignment->get_end_time())
            {
                $count_submissions_late ++;
            }

            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback :: class_name(),
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback :: PROPERTY_SUBMISSION_ID),
                new StaticConditionVariable($submission_tracker->get_id()));

            if (\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback :: count_data(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback :: class_name(),
                null,
                $condition) > 0)
            {
                $count_submissions_feedback ++;
            }
        }

        $condition = new InCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: class_name(),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: PROPERTY_SUBMISSION_ID),
            $submission_ids);
        $score_trackers = \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: get_data(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: class_name(),
            null,
            $condition)->as_array();

        $minimum_score = $this->format_score_html($this->get_min_score($score_trackers));
        $average_score = $this->format_score_html($this->get_avg_score($score_trackers));
        $maximum_score = $this->format_score_html($this->get_max_score($score_trackers));

        $reporting_data->add_data_category_row($this->ROW_SUBMITTER, self :: $COLUMN_DETAILS, $submitter);
        $reporting_data->add_data_category_row(self :: $ROW_ASSIGNMENT, self :: $COLUMN_DETAILS, $title);
        $reporting_data->add_data_category_row(
            self :: $ROW_NUMBER_SUBMISSIONS_LATE,
            self :: $COLUMN_DETAILS,
            $count_submissions_late . '/' . count($submission_trackers));
        $reporting_data->add_data_category_row(
            self :: $ROW_NUMBER_FEEDBACK,
            self :: $COLUMN_DETAILS,
            $count_submissions_feedback . '/' . count($submission_trackers));
        $reporting_data->add_data_category_row(self :: $ROW_SCORE_AVERAGE, self :: $COLUMN_DETAILS, $average_score);
        $reporting_data->add_data_category_row(self :: $ROW_SCORE_MINIMUM, self :: $COLUMN_DETAILS, $minimum_score);
        $reporting_data->add_data_category_row(self :: $ROW_SCORE_MAXIMUM, self :: $COLUMN_DETAILS, $maximum_score);

        return $reporting_data;
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
