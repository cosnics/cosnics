<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\Repository\AssignmentRepository;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block displaying information about the assignment and the
 *          user
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Anthony Hurst (Hogeschool Gent)
 */
class AssignmentEntityInformationBlock extends AssignmentReportingManager
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
        parent::__construct($parent);

        self::$COLUMN_DETAILS = Translation::get('Details');

        $entityService = $this->getEntityServiceForEntityType($this->getEntityType());
        $this->ROW_SUBMITTER = $entityService->getEntityName();
        self::$ROW_ASSIGNMENT = Translation::get('AssignmentTitle');
        self::$ROW_NUMBER_SUBMISSIONS_LATE = Translation::get('NumberOfSubmissionsLate');
        self::$ROW_NUMBER_FEEDBACK = Translation::get('NumberOfSubmissionsFeedbacks');
        self::$ROW_SCORE_AVERAGE = Translation::get('AverageScore');
        self::$ROW_SCORE_MINIMUM = Translation::get('MinimumScore');
        self::$ROW_SCORE_MAXIMUM = Translation::get('MaximumScore');
    }

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

        $contentObjectPublication = $this->getContentObjectPublication();

        /** @var \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment */
        $assignment = $contentObjectPublication->getContentObject();
        $assignmentUrl = $this->getAssignmentUrlForContentObjectPublication($contentObjectPublication);

        $title = $this->createLink($assignmentUrl, $assignment->get_title(), '_blank');

        $submitterUrl = $this->getEntityUrl(
            $contentObjectPublication->get_course_id(), $contentObjectPublication->getId(), $this->getEntityType(),
            $this->getEntityId()
        );

        $entityService = $this->getEntityServiceForEntityType($this->getEntityType());

        $submitter = $this->createLink(
            $submitterUrl,
            $entityService->renderEntityNameById(
                $this->getEntityId()
            ), '_blank'
        );

        $entryStatistics = $this->getAssignmentService()->findEntryStatisticsForEntityByContentObjectPublication(
            $contentObjectPublication, $this->getEntityType(), $this->getEntityId()
        );

        $minimum_score = $this->format_score_html($entryStatistics[AssignmentRepository::MINIMUM_SCORE]);
        $average_score = $this->format_score_html($entryStatistics[AssignmentRepository::AVERAGE_SCORE]);
        $maximum_score = $this->format_score_html($entryStatistics[AssignmentRepository::MAXIMUM_SCORE]);

        $reporting_data->add_data_category_row($this->ROW_SUBMITTER, self::$COLUMN_DETAILS, $submitter);
        $reporting_data->add_data_category_row(self::$ROW_ASSIGNMENT, self::$COLUMN_DETAILS, $title);

        $lateSubmissions = $this->getAssignmentService()->countLateEntriesByContentObjectPublicationEntityTypeAndId(
            $assignment, $contentObjectPublication, $this->getEntityType(), $this->getEntityId()
        );

        $entriesWithFeedback =
            $this->getFeedbackService()->countDistinctFeedbackForContentObjectPublicationEntityTypeAndId(
                $contentObjectPublication, $this->getEntityType(), $this->getEntityId()
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
