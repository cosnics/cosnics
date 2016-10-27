<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block with an overview of all submissions of an
 *          assignment from a user/group
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Anthony Hurst (Hogeschool Gent)
 */
class AssignmentSubmitterSubmissionsBlock extends AssignmentReportingManager
{

    private static $COLUMN_DATE_SUBMITTED;

    private static $COLUMN_INDIVIDUAL_SUBMITTER;

    private static $COLUMN_IP_ADDRESS;

    private static $COLUMN_NUMBER_OF_FEEDBACKS;

    private static $COLUMN_SCORE;

    private static $COLUMN_TITLE;

    /**
     * Instatiates the column headers.
     *
     * @param $parent type Pass-through variable. Please refer to parent class(es) for more details. &param type
     *            $vertical Pass-through variable. Please refer to parent class(es) for more details.
     */
    public function __construct($parent, $vertical)
    {
        self :: $COLUMN_TITLE = Translation :: get('Title');
        self :: $COLUMN_INDIVIDUAL_SUBMITTER = Translation :: get('SubmittedBy');
        self :: $COLUMN_DATE_SUBMITTED = Translation :: get('DateSubmitted');
        self :: $COLUMN_SCORE = Translation :: get('Score');
        self :: $COLUMN_NUMBER_OF_FEEDBACKS = Translation :: get('NumberOfFeedbacks');
        self :: $COLUMN_IP_ADDRESS = Translation :: get('IpAddress');
        parent :: __construct($parent, $vertical);
    }

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data_headers = array(
            self :: $COLUMN_TITLE,
            self :: $COLUMN_DATE_SUBMITTED,
            self :: $COLUMN_SCORE,
            self :: $COLUMN_NUMBER_OF_FEEDBACKS,
            self :: $COLUMN_IP_ADDRESS);
        if ($this->get_submitter_type() !=
             \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_USER)
        {
            array_splice($reporting_data_headers, 1, 0, array(self :: $COLUMN_INDIVIDUAL_SUBMITTER));
        }
        $reporting_data->set_rows($reporting_data_headers);

        $assignment = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(),
            $this->get_publication_id())->get_content_object();

        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: class_name(),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: PROPERTY_SUBMITTER_ID),
            new StaticConditionVariable($this->get_target_id()));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: class_name(),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: PROPERTY_SUBMITTER_TYPE),
            new StaticConditionVariable($this->get_submitter_type()));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: class_name(),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($this->get_publication_id()));

        $condition = new AndCondition($conditions);

        $submission_trackers = \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: get_data(
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: class_name(),
            null,
            $condition)->as_array();

        foreach ($submission_trackers as $key => $submission_tracker)
        {
            $score = null;

            $date_submitted = $this->format_date_html(
                $submission_tracker->get_date_submitted(),
                $assignment->get_end_time());

            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: class_name(),
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: PROPERTY_SUBMISSION_ID),
                new StaticConditionVariable($submission_tracker->get_id()));

            $score_trackers = \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: get_data(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore :: class_name(),
                null,
                $condition)->as_array();

            if ($score_trackers[0])
            {
                $score = $this->format_score_html($score_trackers[0]->get_score());
            }

            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback :: class_name(),
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback :: PROPERTY_SUBMISSION_ID),
                new StaticConditionVariable($submission_tracker->get_id()));

            $number_feedbacks = \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback :: count_data(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback :: class_name(),
                null,
                $condition);

            if ($this->get_submitter_type() !=
                 \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_USER)
            {
                // Link to the User
                $individual_submitter = $this->generate_user_name_link($submission_tracker->get_user_id());
            }
            // Link to submission details.
            $title = $this->generate_submission_title_link($submission_tracker);

            $reporting_data->add_category($key);
            $reporting_data->add_data_category_row($key, self :: $COLUMN_TITLE, $title);
            $reporting_data->add_data_category_row($key, self :: $COLUMN_DATE_SUBMITTED, $date_submitted);
            $reporting_data->add_data_category_row($key, self :: $COLUMN_SCORE, $score);
            $reporting_data->add_data_category_row($key, self :: $COLUMN_NUMBER_OF_FEEDBACKS, $number_feedbacks);
            $reporting_data->add_data_category_row(
                $key,
                self :: $COLUMN_IP_ADDRESS,
                $submission_tracker->get_ip_address());

            if ($this->get_submitter_type() !=
                 \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission :: SUBMITTER_TYPE_USER)
            {
                $reporting_data->add_data_category_row(
                    $key,
                    self :: $COLUMN_INDIVIDUAL_SUBMITTER,
                    $individual_submitter);
            }
        }
        $reporting_data->hide_categories();
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
