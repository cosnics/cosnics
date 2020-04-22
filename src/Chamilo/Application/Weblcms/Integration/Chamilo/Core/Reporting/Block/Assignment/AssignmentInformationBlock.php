<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository\AssignmentRepository;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Libraries\Translation\Translation;

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

    /**
     * @var ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    protected $assignmentPublication;

    /**
     * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject | Assignment
     */
    protected $assignment;

    public function __construct($parent, $vertical = false)
    {
        parent::__construct($parent, $vertical);

        $this->contentObjectPublication = $this->getContentObjectPublication();

        $this->assignment = $this->contentObjectPublication->get_content_object();

        $this->assignmentPublication =
            $this->getPublicationRepository()->findPublicationByContentObjectPublication(
                $this->contentObjectPublication
            );

        self::$column_details = Translation::get('Details');
        self::$row_title = Translation::get('Title');
        self::$row_description = Translation::get('Description');

        if ($this->assignmentPublication->getEntityType() == Entry::ENTITY_TYPE_USER)
        {
            self::$row_number_of_submitters_submitted = Translation::get('NumberOfUsersSubmitted');
            self::$row_number_of_submitters_late = Translation::get('NumberOfUsersLate');
        }
        else
        {
            self::$row_number_of_submitters_submitted = Translation::get('NumberOfGroupsSubmitted');
            self::$row_number_of_submitters_late = Translation::get('NumberOfGroupsLate');
        }

        self::$row_score_average = Translation::get('AverageScore');
        self::$row_score_minimum = Translation::get('MinimumScore');
        self::$row_score_maximum = Translation::get('MaximumScore');
    }

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_categories(
            array(
                self::$row_title,
                self::$row_description,
                self::$row_number_of_submitters_submitted,
                self::$row_number_of_submitters_late,
                self::$row_score_average,
                self::$row_score_minimum,
                self::$row_score_maximum
            )
        );

        $url = $this->getAssignmentUrlForContentObjectPublication($this->contentObjectPublication);

        $number_of_submitters = $this->count_submitters();
        $assignmentStatistics = $this->getAssignmentService()->findEntryStatisticsByContentObjectPublicationIdentifiers(
            [$this->contentObjectPublication->getId()]
        )[0];

        $lateEntries = $this->getAssignmentService()->countDistinctLateEntriesByContentObjectPublicationAndEntityType(
            $this->assignment, $this->contentObjectPublication, $this->assignmentPublication->getEntityType()
        );

        $entitiesWithEntriesCount =
            $this->getAssignmentService()->countDistinctEntriesByContentObjectPublicationAndEntityType(
                $this->contentObjectPublication, $this->assignmentPublication->getEntityType()
            );

        $reporting_data->set_rows(array(self::$column_details));
        $reporting_data->add_data_category_row(
            self::$row_title,
            self::$column_details,
            $this->createLink($url, $this->assignment->get_title(), '_blank')
        );
        $reporting_data->add_data_category_row(
            self::$row_description,
            self::$column_details,
            $this->assignment->get_description()
        );
        $reporting_data->add_data_category_row(
            self::$row_number_of_submitters_submitted,
            self::$column_details,
            $entitiesWithEntriesCount . '/' . $number_of_submitters
        );
        $reporting_data->add_data_category_row(
            self::$row_number_of_submitters_late,
            self::$column_details,
            $lateEntries . '/' . $number_of_submitters
        );
        $reporting_data->add_data_category_row(
            self::$row_score_average,
            self::$column_details,
            $this->format_score_html($assignmentStatistics[AssignmentRepository::AVERAGE_SCORE])
        );
        $reporting_data->add_data_category_row(
            self::$row_score_minimum,
            self::$column_details,
            $this->format_score_html($assignmentStatistics[AssignmentRepository::MINIMUM_SCORE])
        );
        $reporting_data->add_data_category_row(
            self::$row_score_maximum,
            self::$row_description,
            $this->format_score_html($assignmentStatistics[AssignmentRepository::MAXIMUM_SCORE])
        );

        return $reporting_data;
    }

    /**
     * Counts the number of submitters registered for the assignment.
     *
     * @param $is_group_assignment boolean Whether the assignment is a group assignment.
     *
     * @return int the number of submitters registered for the assignment.
     */
    private function count_submitters()
    {
        $entityService = $this->getEntityServiceForEntityType($this->assignmentPublication->getEntityType());
        return $entityService->countEntities($this->contentObjectPublication);
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(Html::VIEW_TABLE);
    }
}
