<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\Repository\AssignmentRepository;
use Chamilo\Libraries\Translation\Translation;
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
     *        $vertical Pass-through variable. Please refer to parent class(es) for more details.
     */
    public function __construct($parent)
    {
        self::$COLUMN_DETAILS = Translation::get('Details');

        $this->ROW_SUBMITTER = $this->define_row_submitter_title();
        self::$ROW_ASSIGNMENT = Translation::get('AssignmentTitle');
        self::$ROW_NUMBER_SUBMISSIONS_LATE = Translation::get('NumberOfSubmissionsLate');
        self::$ROW_NUMBER_FEEDBACK = Translation::get('NumberOfSubmissionsFeedbacks');
        self::$ROW_SCORE_AVERAGE = Translation::get('AverageScore');
        self::$ROW_SCORE_MINIMUM = Translation::get('MinimumScore');
        self::$ROW_SCORE_MAXIMUM = Translation::get('MaximumScore');

        parent::__construct($parent);
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
                self::$ROW_ASSIGNMENT,
                self::$ROW_NUMBER_SUBMISSIONS_LATE,
                self::$ROW_NUMBER_FEEDBACK,
                self::$ROW_SCORE_MINIMUM,
                self::$ROW_SCORE_AVERAGE,
                self::$ROW_SCORE_MAXIMUM
            )
        );
        $reporting_data->set_rows(array(self::$COLUMN_DETAILS));

        /** @var ContentObjectPublication $contentObjectPublication */
        $contentObjectPublication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(),
            $this->get_publication_id()
        );

        $assignment = $contentObjectPublication->getContentObject();
        $assignmentUrl = $this->getAssignmentUrlForContentObjectPublication($contentObjectPublication);
        $title = '<a href="' . $assignmentUrl . '" target="_blank">' . $assignment->get_title() . '</a>';

        $submitter = $this->getEntityUrl(
            $contentObjectPublication->get_course_id(), $contentObjectPublication->getId(), $this->get_submitter_type(),
            $this->get_target_id()
        );

        $entryStatistics = $this->getAssignmentService()->findEntryStatisticsForEntityByContentObjectPublication(
            $contentObjectPublication, $this->get_submitter_type(), $this->get_target_id()
        );

        $minimum_score = $this->format_score_html($entryStatistics[AssignmentRepository::MINIMUM_SCORE]);
        $average_score = $this->format_score_html($entryStatistics[AssignmentRepository::AVERAGE_SCORE]);
        $maximum_score = $this->format_score_html($entryStatistics[AssignmentRepository::MAXIMUM_SCORE]);

        $reporting_data->add_data_category_row($this->ROW_SUBMITTER, self::$COLUMN_DETAILS, $submitter);
        $reporting_data->add_data_category_row(self::$ROW_ASSIGNMENT, self::$COLUMN_DETAILS, $title);

//        $lateSubmissions = $this->getAssignmentService()->countLateEntriesByContentObjectPublicationAndEntity(
//            $contentObjectPublication, $assignment, $this->get_submitter_type(), $this->get_target_id()
//        );

        $entriesWithFeedback =
            $this->getAssignmentService()->countDistinctFeedbackForContentObjectPublicationEntityTypeAndId(
                $contentObjectPublication, $this->get_submitter_type(), $this->get_target_id()
            );

        $reporting_data->add_data_category_row(
            self::$ROW_NUMBER_SUBMISSIONS_LATE,
            self::$COLUMN_DETAILS,
            $lateSubmissions . '/' . $entryStatistics[AssignmentRepository::ENTRIES_COUNT]
        );

        $reporting_data->add_data_category_row(
            self::$ROW_NUMBER_FEEDBACK,
            self::$COLUMN_DETAILS,
            $entriesWithFeedback . '/' . $entryStatistics[AssignmentRepository::ENTRIES_COUNT]
        );

        $reporting_data->add_data_category_row(self::$ROW_SCORE_AVERAGE, self::$COLUMN_DETAILS, $average_score);
        $reporting_data->add_data_category_row(self::$ROW_SCORE_MINIMUM, self::$COLUMN_DETAILS, $minimum_score);
        $reporting_data->add_data_category_row(self::$ROW_SCORE_MAXIMUM, self::$COLUMN_DETAILS, $maximum_score);

        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_TABLE);
    }
}
