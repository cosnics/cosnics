<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\EntityAssignmentEntriesTemplate;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableColumnModel;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

/**
 * Umbrella class for WeblcmsAssignmentCourseGroupsReportingBlock and WeblcmsAssignmentPlatformGroupsReportingBlock
 * containing all common code.
 * Implementation specific methods are declared abstract.
 *
 * @author Anthony Hurst (Hogeschool Gent)
 */
class AssignmentEntitiesBlock extends AssignmentReportingManager
{

    private static $COLUMN_ACTIONS;

    private static $COLUMN_LAST_SCORE;

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
     * @var ReportingData
     */
    protected $reportingData;

    /**
     * AssignmentEntitiesBlock constructor.
     *
     * @param $parent
     */
    public function __construct($parent)
    {
        self::$COLUMN_NAME = Translation::get('Name');
        self::$COLUMN_FIRST_SUBMISSION = Translation::get('FirstSubmission');
        self::$COLUMN_LAST_SUBMISSION = Translation::get('LastSubmission');
        self::$COLUMN_NUMBER_OF_SUBMISSIONS = Translation::get('NumberOfSubmissions');
        self::$COLUMN_NUMBER_OF_FEEDBACKS = Translation::get('NumberOfFeedbacks');
        self::$COLUMN_LAST_SCORE = Translation::get('LastScore');
        self::$COLUMN_ACTIONS = Translation::get('SubmitterDetails');

        parent::__construct($parent);
    }

    public function get_title()
    {
        $contentObjectPublication = $this->getContentObjectPublication();
        $assignmentPublication = $this->getPublicationRepository()->findPublicationByContentObjectPublication($contentObjectPublication);
        $entityType = ($assignmentPublication instanceof Publication) ?
            $assignmentPublication->getEntityType() : Entry::ENTITY_TYPE_USER;

        return $this->getEntityServiceManager()->getEntityServiceByType($entityType)->getPluralEntityName();
    }

    /**
     * @return \Chamilo\Core\Reporting\ReportingData
     */
    public function count_data()
    {
        if ($this->reportingData)
        {
            return $this->reportingData;
        }

        $this->reportingData = new ReportingData();

        $this->reportingData->set_rows(
            array(
                self::$COLUMN_NAME,
                self::$COLUMN_FIRST_SUBMISSION,
                self::$COLUMN_LAST_SUBMISSION,
                self::$COLUMN_NUMBER_OF_SUBMISSIONS,
                self::$COLUMN_NUMBER_OF_FEEDBACKS,
                self::$COLUMN_LAST_SCORE,
                self::$COLUMN_ACTIONS
            )
        );

        $contentObjectPublication = $this->getContentObjectPublication();
        /** @var Assignment $assignment */
        $assignment = $contentObjectPublication->getContentObject();

        $assignmentPublication = $this->getPublicationRepository()->findPublicationByContentObjectPublication($contentObjectPublication);
        $entityType = ($assignmentPublication instanceof Publication) ?
            $assignmentPublication->getEntityType() : Entry::ENTITY_TYPE_USER;

        $entityService = $this->getEntityServiceForEntityType($entityType);
        $entities = $entityService->retrieveEntities($contentObjectPublication);

        $count = 0;

        $img = '<img src="' . Theme::getInstance()->getCommonImagePath('Action/Statistics') . '" title="' .
            Translation::get('Details') . '" />';

        $detailParams = $this->get_parent()->get_parameters();
        $detailParams[\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID] =
            EntityAssignmentEntriesTemplate::class_name();
        $detailParams[\Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTITY_TYPE] =
            $entityType;
        $detailParams[\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION] = $this->getPublicationId();

        foreach ($entities as $entity)
        {
            $entityId = $entity[Entry::PROPERTY_ENTITY_ID];

            $url = $this->getEntityUrl(
                $contentObjectPublication->get_course_id(), $contentObjectPublication->getId(), $entityType,
                $entityId
            );

            $name = $this->createLink($url, $entityService->renderEntityNameByArray($entity));

            $this->reportingData->add_category($count);

            $this->reportingData->add_data_category_row($count, self::$COLUMN_NAME, $name);
            $this->reportingData->add_data_category_row(
                $count,
                self::$COLUMN_FIRST_SUBMISSION,
                $this->format_date_html($entity[EntityTableColumnModel::PROPERTY_FIRST_ENTRY_DATE], $assignment->get_end_time())
            );
            $this->reportingData->add_data_category_row(
                $count,
                self::$COLUMN_LAST_SUBMISSION,
                $this->format_date_html($entity[EntityTableColumnModel::PROPERTY_LAST_ENTRY_DATE], $assignment->get_end_time())
            );
            $this->reportingData->add_data_category_row(
                $count,
                self::$COLUMN_NUMBER_OF_SUBMISSIONS,
                $entity[EntityTableColumnModel::PROPERTY_ENTRY_COUNT]
            );
            $this->reportingData->add_data_category_row(
                $count,
                self::$COLUMN_NUMBER_OF_FEEDBACKS,
                $this->getFeedbackService()->countFeedbackForContentObjectPublicationByEntityTypeAndEntityId(
                    $contentObjectPublication, $entityType, $entityId
                )
            );
            $this->reportingData->add_data_category_row(
                $count,
                self::$COLUMN_LAST_SCORE,
                $this->format_score_html($this->getAssignmentService()->getLastScoreForContentObjectPublicationEntityTypeAndId(
                    $contentObjectPublication, $entityType, $entityId
                ))
        );

            $detailParams[\Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTITY_ID] =
                $entityId;

            $link = $this->createLink($this->get_parent()->get_url($detailParams), $img);
            $this->reportingData->add_data_category_row($count, self::$COLUMN_ACTIONS, $link);

            $count ++;
        }

        $this->reportingData->hide_categories();

        return $this->reportingData;
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
