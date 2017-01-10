<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Description of weblcms_assignment_submissions_reporting_block
 *
 * @author Anthony Hurst (Hogeschool Gent)
 */
abstract class AssignmentSubmissionsBlock extends AssignmentReportingManager
{

    private static $COLUMN_DATE_SUBMITTED;

    private static $COLUMN_INDIVIDUAL_SUBMITTER_ID;

    private static $COLUMN_INDIVIDUAL_SUBMITTER_NAME;

    private static $COLUMN_IP_ADDRESS;

    private static $COLUMN_NUMBER_OF_FEEDBACKS;

    private static $COLUMN_SCORE;

    private static $COLUMN_SUBMITTER_ID;

    private $COLUMN_SUBMITTER_NAME;

    private static $COLUMN_TITLE;
    const SUBMITTER_INFORMATION_NAME = 0;
    const SUBMITTER_INFORMATION_CODE = 1;

    /**
     * Creates a submissions overview block depending on the current assignmnet publication's allow group submissions
     * setting.
     *
     * @param $parent type
     * @param $vertical type
     *
     * @return \application\weblcms\WeblcmsAssignmentSubmissionsGroupReportingBlock |
     *         \application\weblcms\WeblcmsAssignmentSubmissionsUserReportingBlock
     */
    public static function getInstance($parent, $vertical)
    {
        if (\Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(),
            self::get_publication_id()
        )->get_content_object()->get_allow_group_submissions()
        )
        {
            return new AssignmentSubmissionsGroupBlock($parent, $vertical);
        }

        return new AssignmentSubmissionsUserBlock($parent, $vertical);
    }

    public function __construct($parent, $vertical)
    {
        self::$COLUMN_TITLE = Translation::get('Title');
        self::$COLUMN_SUBMITTER_ID = Translation::get('UserCode'); // Only
        // visible
        // in
        // individual
        // assignments.
        $this->COLUMN_SUBMITTER_NAME = $this->define_column_submitter_name_title();
        self::$COLUMN_INDIVIDUAL_SUBMITTER_ID = Translation::get('UserCode'); // Only
        // visible
        // in
        // group
        // assignments.
        self::$COLUMN_INDIVIDUAL_SUBMITTER_NAME = Translation::get('SubmittedBy'); // Only
        // visible
        // in
        // group
        // assignments.
        self::$COLUMN_DATE_SUBMITTED = Translation::get('DateSubmitted');
        self::$COLUMN_SCORE = Translation::get('Score');
        self::$COLUMN_NUMBER_OF_FEEDBACKS = Translation::get('NumberOfFeedbacks');
        self::$COLUMN_IP_ADDRESS = Translation::get('IpAddress');
        parent::__construct($parent, $vertical);
    }

    /**
     * Defines the title of the submitter name column.
     *
     * @return string The title of the submitter name column.
     */
    abstract protected function define_column_submitter_name_title();

    public function count_data()
    {
        $assignment = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(),
            $this->get_publication_id()
        )->get_content_object();
        $reporting_data = new ReportingData();

        $reporting_data_headers = array(
            self::$COLUMN_TITLE,
            $this->COLUMN_SUBMITTER_NAME,
            self::$COLUMN_DATE_SUBMITTED,
            self::$COLUMN_SCORE,
            self::$COLUMN_NUMBER_OF_FEEDBACKS,
            self::$COLUMN_IP_ADDRESS
        );

        if ($assignment->get_allow_group_submissions())
        {
            array_splice(
                $reporting_data_headers,
                2,
                0,
                array(self::$COLUMN_INDIVIDUAL_SUBMITTER_ID, self::$COLUMN_INDIVIDUAL_SUBMITTER_NAME)
            );
        }
        else
        {
            array_splice($reporting_data_headers, 1, 0, array(self::$COLUMN_SUBMITTER_ID));
        }
        $reporting_data->set_rows($reporting_data_headers);

        $assignment = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(),
            $this->get_publication_id()
        )->get_content_object();

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(
                ),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_PUBLICATION_ID
            ),
            new StaticConditionVariable($this->get_publication_id())
        );

        $submission_trackers =
            \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::get_data(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(
                ),
                null,
                $condition
            )->as_array();

        foreach ($submission_trackers as $key => $submission_tracker)
        {
            // Title column.
            $submission_title_link = $this->generate_submission_title_link($submission_tracker);

            // Submitter code column
            $submitter_code = $this->get_submitter_code(
                $submission_tracker->get_submitter_type(),
                $submission_tracker->get_submitter_id()
            );

            // Submitter name column.
            $submitter_name_link = $this->generate_submitter_name_link(
                $submission_tracker->get_submitter_type(),
                $submission_tracker->get_submitter_id()
            );

            // Individual submitter column.
            if ($assignment->get_allow_group_submissions())
            {
                $individual_submitter_id = $this->get_submitter_code(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER,
                    $submission_tracker->get_user_id()
                );
                $individual_submitter_name_link = $this->generate_user_name_link($submission_tracker->get_user_id());
            }

            // Date submitted column.
            $date_submitted = $this->format_date_html(
                $submission_tracker->get_date_submitted(),
                $assignment->get_end_time()
            );

            // Score column.
            $score = null;
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::class_name(
                    ),
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::PROPERTY_SUBMISSION_ID
                ),
                new StaticConditionVariable($submission_tracker->get_id())
            );

            $score_trackers =
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::get_data(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::class_name(
                    ),
                    null,
                    $condition
                )->as_array();

            if ($score_trackers[0])
            {
                $score = $this->format_score_html($score_trackers[0]->get_score());
            }

            // Number of feedbacks column.
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::class_name(
                    ),
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::PROPERTY_SUBMISSION_ID
                ),
                new StaticConditionVariable($submission_tracker->get_id())
            );

            $number_feedbacks =
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::count_data(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionFeedback::class_name(
                    ),
                    null,
                    $condition
                );

            // IP address column.
            $ip_address = $submission_tracker->get_ip_address();

            $reporting_data->add_category($key);
            $reporting_data->add_data_category_row($key, self::$COLUMN_TITLE, $submission_title_link);

            $reporting_data->add_data_category_row($key, $this->COLUMN_SUBMITTER_NAME, $submitter_name_link);
            if ($assignment->get_allow_group_submissions())
            {
                $reporting_data->add_data_category_row(
                    $key,
                    self::$COLUMN_INDIVIDUAL_SUBMITTER_ID,
                    $individual_submitter_id
                );
                $reporting_data->add_data_category_row(
                    $key,
                    self::$COLUMN_INDIVIDUAL_SUBMITTER_NAME,
                    $individual_submitter_name_link
                );
            }
            else
            {
                $reporting_data->add_data_category_row($key, self::$COLUMN_SUBMITTER_ID, $submitter_code);
            }
            $reporting_data->add_data_category_row($key, self::$COLUMN_DATE_SUBMITTED, $date_submitted);
            $reporting_data->add_data_category_row($key, self::$COLUMN_SCORE, $score);
            $reporting_data->add_data_category_row($key, self::$COLUMN_NUMBER_OF_FEEDBACKS, $number_feedbacks);
            $reporting_data->add_data_category_row($key, self::$COLUMN_IP_ADDRESS, $ip_address);
        }
        $reporting_data->hide_categories();

        return $reporting_data;
    }

    /**
     * Obtains the official code of the submitter given (only users).
     *
     * @param $submitter_type int The type of the submitter
     *        (\application\weblcms\integration\core\tracking\tracker\AssignmentSubmission::SUBMITTER_TYPE_...).
     * @param $submitter_id int The id of the submitter.
     *
     * @return string The official code of the submitter (null for groups).
     */
    protected function get_submitter_code($submitter_type, $submitter_id)
    {
        switch ($submitter_type)
        {
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_COURSE_GROUP :
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_PLATFORM_GROUP :
                return null;
            case \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::SUBMITTER_TYPE_USER :
                $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                    \Chamilo\Core\User\Storage\DataClass\User::class_name(),
                    (int) $submitter_id
                );

                if ($user)
                {
                    return $user->get_official_code();
                }

                return null;
        }
    }

    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_TABLE);
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }
}
