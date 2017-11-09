<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\CourseSubmitterSubmissionsTemplate;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Umbrella class for WeblcmsAssignmentCourseGroupsReportingBlock and WeblcmsAssignmentPlatformGroupsReportingBlock
 * containing all common code.
 * Implementation specific methods are declared abstract.
 * 
 * @author Anthony Hurst (Hogeschool Gent)
 */
abstract class AssignmentSubmittersBlock extends AssignmentReportingManager
{

    private static $COLUMN_ACTIONS;

    private static $COLUMN_AVERAGE_SCORE;

    private static $COLUMN_FIRST_SUBMISSION;

    private static $COLUMN_LAST_SUBMISSION;

    private static $COLUMN_NAME;

    private static $COLUMN_NUMBER_OF_FEEDBACKS;

    private static $COLUMN_NUMBER_OF_SUBMISSIONS;
    const STATISTICS_FIRST_SUBMISSION = 0;
    const STATISTICS_LAST_SUBMISSION = 1;
    const STATISTICS_AVERAGE_SCORE = 2;
    const STATISTICS_NUMBER_OF_SUBMISSIONS = 3;
    const STATISTICS_NUMBER_OF_FEEDBACKS = 4;

    protected $submissions;

    protected $feedbacks;

    /**
     * Instantiates the column headers needed.
     * 
     * @param $parent type Pass-through variable. See parent class(es) for more details.
     * @param $vertical type Pass-through variable. See parent class(es) for more details.
     */
    public function __construct($parent, $vertical)
    {
        self::$COLUMN_NAME = Translation::get('Name');
        self::$COLUMN_FIRST_SUBMISSION = Translation::get('FirstSubmission');
        self::$COLUMN_LAST_SUBMISSION = Translation::get('LastSubmission');
        self::$COLUMN_NUMBER_OF_SUBMISSIONS = Translation::get('NumberOfSubmissions');
        self::$COLUMN_NUMBER_OF_FEEDBACKS = Translation::get('NumberOfFeedbacks');
        self::$COLUMN_AVERAGE_SCORE = Translation::get('AverageScore');
        self::$COLUMN_ACTIONS = Translation::get('SubmitterDetails');
        parent::__construct($parent, $vertical);
    }

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(
            array(
                self::$COLUMN_NAME, 
                self::$COLUMN_FIRST_SUBMISSION, 
                self::$COLUMN_LAST_SUBMISSION, 
                self::$COLUMN_NUMBER_OF_SUBMISSIONS, 
                self::$COLUMN_NUMBER_OF_FEEDBACKS, 
                self::$COLUMN_AVERAGE_SCORE, 
                self::$COLUMN_ACTIONS));
        
        $assignment = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), 
            $this->get_publication_id())->get_content_object();
        
        $submitters = $this->get_submitters();
        
        $this->get_submitters_data();
        $img = '<img src="' . Theme::getInstance()->getCommonImagePath('Action/Statistics') . '" title="' .
             Translation::get('Details') . '" />';
        $count = 0;
        
        foreach ($submitters as $submitter)
        {
            $url_title = null;
            $submissions_count = 0;
            $feedbacks_count = null;
            
            $reporting_data->add_category($count);
            $submitter_statistics = array();
            
            if ($this->submissions[$submitter->get_id()])
            {
                $submitter_statistics = $this->compile_submitter_statistics($submitter, $assignment);
                $first_submission = reset($this->submissions);
                $url_title = $this->generate_submitter_name_link(
                    $first_submission[AssignmentSubmission::PROPERTY_SUBMITTER_TYPE], 
                    $submitter->get_id());
                
                $params = $this->get_parent()->get_parameters();
                $params[\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID] = CourseSubmitterSubmissionsTemplate::class_name();
                $params[\Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::PARAM_SUBMITTER_TYPE] = $first_submission[AssignmentSubmission::PROPERTY_SUBMITTER_TYPE];
                $params[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] = $this->get_publication_id();
                $params[\Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager::PARAM_TARGET_ID] = $submitter->get_id();
                $link = '<a href="' . $this->get_parent()->get_url($params) . '">' . $img . '</a>';
                $reporting_data->add_data_category_row($count, self::$COLUMN_ACTIONS, $link);
            }
            
            if ($url_title)
            {
                $name = $url_title;
            }
            else
            {
                $name = $this->get_submitter_name($submitter);
            }
            
            $reporting_data->add_data_category_row($count, self::$COLUMN_NAME, $name);
            $reporting_data->add_data_category_row(
                $count, 
                self::$COLUMN_FIRST_SUBMISSION, 
                $submitter_statistics[self::STATISTICS_FIRST_SUBMISSION]);
            $reporting_data->add_data_category_row(
                $count, 
                self::$COLUMN_LAST_SUBMISSION, 
                $submitter_statistics[self::STATISTICS_LAST_SUBMISSION]);
            $reporting_data->add_data_category_row(
                $count, 
                self::$COLUMN_NUMBER_OF_SUBMISSIONS, 
                count($submitter_statistics) > 0 ? $submitter_statistics[self::STATISTICS_NUMBER_OF_SUBMISSIONS] : $submissions_count);
            $reporting_data->add_data_category_row(
                $count, 
                self::$COLUMN_NUMBER_OF_FEEDBACKS, 
                count($submitter_statistics) > 0 ? $submitter_statistics[self::STATISTICS_NUMBER_OF_FEEDBACKS] : $feedbacks_count);
            $reporting_data->add_data_category_row(
                $count, 
                self::$COLUMN_AVERAGE_SCORE, 
                $submitter_statistics[self::STATISTICS_AVERAGE_SCORE]);
            
            $count ++;
        }
        
        $reporting_data->hide_categories();
        return $reporting_data;
    }

    /**
     * Obtains the course groups or platform groups registered with the assignment.
     * 
     * @return array() The course groups or platform groups registered with the assignment.
     */
    abstract public function get_submitters();

    /**
     * Obtains the submission and feedback information needed to compile the group statistics.
     */
    abstract public function get_submitters_data();

    /**
     * Obtains the name of the submitter.
     */
    abstract public function get_submitter_name($submitter);

    /**
     * Compiles the statistics for a group and assignment.
     * 
     * @param $submitter type The group for which the statistics are wanted.
     * @param $assignment type The assignment for which the statistics are wanted.
     * @return type array() The statistics needed to fill the reporting table. Use the class STATISTICS_* constants for
     *         access.
     */
    private function compile_submitter_statistics($submitter, $assignment)
    {
        $submissions_count = $this->submissions[$submitter->get_id()]['count'];
        $first_submission = $this->format_date_html(
            $this->submissions[$submitter->get_id()]['first_date'], 
            $assignment->get_end_time());
        $last_submission = $this->format_date_html(
            $this->submissions[$submitter->get_id()]['last_date'], 
            $assignment->get_end_time());
        
        if ($submissions_count > 0)
        {
            $conditions = array();
            
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    AssignmentSubmission::class_name(), 
                    AssignmentSubmission::PROPERTY_SUBMITTER_ID), 
                new StaticConditionVariable(
                    $this->submissions[$submitter->get_id()][AssignmentSubmission::PROPERTY_SUBMITTER_ID]));
            
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    AssignmentSubmission::class_name(), 
                    AssignmentSubmission::PROPERTY_SUBMITTER_TYPE), 
                new StaticConditionVariable(
                    $this->submissions[$submitter->get_id()][AssignmentSubmission::PROPERTY_SUBMITTER_TYPE]));
            
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    AssignmentSubmission::class_name(), 
                    AssignmentSubmission::PROPERTY_PUBLICATION_ID), 
                new StaticConditionVariable($this->get_publication_id()));
            
            $condition = new AndCondition($conditions);
            
            $submission_trackers = AssignmentSubmission::get_data(AssignmentSubmission::class_name(), null, $condition)->as_array();
            
            $submission_tracker_ids = array();
            foreach ($submission_trackers as $submission_tracker)
            {
                $submission_tracker_ids[] = $submission_tracker->get_id();
            }
            
            $condition = new InCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::class_name(), 
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::PROPERTY_SUBMISSION_ID), 
                $submission_tracker_ids);
            $score_trackers = \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::get_data(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\SubmissionScore::class_name(), 
                null, 
                $condition)->as_array();
            
            $average_score = $this->format_score_html($this->get_avg_score($score_trackers));
            
            if ($this->feedbacks[$submitter->get_id()])
            {
                $feedbacks_count = $this->feedbacks[$submitter->get_id()]['count'];
            }
            else
            {
                $feedbacks_count = 0;
            }
            
            $statistics = array();
            $statistics[self::STATISTICS_FIRST_SUBMISSION] = $first_submission;
            $statistics[self::STATISTICS_LAST_SUBMISSION] = $last_submission;
            $statistics[self::STATISTICS_NUMBER_OF_SUBMISSIONS] = $submissions_count;
            $statistics[self::STATISTICS_NUMBER_OF_FEEDBACKS] = $feedbacks_count;
            $statistics[self::STATISTICS_AVERAGE_SCORE] = $average_score;
            return $statistics;
        }
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_TABLE);
    }

    /**
     * Converts the resultset to an array with the submitter_id as index
     * 
     * @param $resultset ResultSet
     * @param $array array target array, passed by reference
     */
    protected function get_array_from_resultset($resultset, &$array)
    {
        while ($row = $resultset->next_result())
        {
            $array[$row[AssignmentSubmission::PROPERTY_SUBMITTER_ID]] = $row;
        }
    }
}
