<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Score;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block with an overview of all submissions of an
 *          assignment from a user/group
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 * @author Anthony Hurst (Hogeschool Gent)
 */
class AssignmentEntriesBlock extends AssignmentReportingManager
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
     *        $vertical Pass-through variable. Please refer to parent class(es) for more details.
     */
    public function __construct($parent)
    {
        self::$COLUMN_TITLE = Translation::get('Title');
        self::$COLUMN_INDIVIDUAL_SUBMITTER = Translation::get('SubmittedBy');
        self::$COLUMN_DATE_SUBMITTED = Translation::get('DateSubmitted');
        self::$COLUMN_SCORE = Translation::get('Score');
        self::$COLUMN_NUMBER_OF_FEEDBACKS = Translation::get('NumberOfFeedbacks');
        self::$COLUMN_IP_ADDRESS = Translation::get('IpAddress');
        parent::__construct($parent);
    }

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data_headers = array(
            self::$COLUMN_TITLE,
            self::$COLUMN_DATE_SUBMITTED,
            self::$COLUMN_SCORE,
            self::$COLUMN_NUMBER_OF_FEEDBACKS,
            self::$COLUMN_IP_ADDRESS
        );

        $isGroupSubmission = $this->getEntityType() != Entry::ENTITY_TYPE_USER;

        if ($isGroupSubmission)
        {
            array_splice($reporting_data_headers, 1, 0, array(self::$COLUMN_INDIVIDUAL_SUBMITTER));
        }

        $reporting_data->set_rows($reporting_data_headers);

        $contentObjectPublication = $this->getContentObjectPublication();

        /** @var \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment */
        $assignment = $contentObjectPublication->getContentObject();
        $entries = $this->getAssignmentService()->findEntriesForContentObjectPublicationEntityTypeAndId(
            $contentObjectPublication, $this->getEntityType(), $this->getEntityId()
        );

        foreach ($entries as $key => $entry)
        {
            $score = null;

            $date_submitted = $this->format_date_html(
                $entry[Entry::PROPERTY_SUBMITTED],
                $assignment->get_end_time()
            );

            $score = $this->format_score_html($entry[Score::PROPERTY_SCORE]);

            $entryUrl = $this->getEntryUrl(
                $contentObjectPublication->get_course_id(), $contentObjectPublication->getId(),
                $this->getEntityType(), $this->getEntityId(), $entry[DataClass::PROPERTY_ID]
            );

            $title = $this->createLink($entryUrl, $entry[ContentObject::PROPERTY_TITLE], '_blank');

            $numberOfFeedbacks =
                $this->getAssignmentService()->countFeedbackForContentObjectPublicationByEntityTypeAndEntityId(
                    $contentObjectPublication, $this->getEntityType(), $this->getEntityId()
                );

            $reporting_data->add_category($key);
            $reporting_data->add_data_category_row($key, self::$COLUMN_TITLE, $title);
            $reporting_data->add_data_category_row($key, self::$COLUMN_DATE_SUBMITTED, $date_submitted);
            $reporting_data->add_data_category_row($key, self::$COLUMN_SCORE, $score);
            $reporting_data->add_data_category_row($key, self::$COLUMN_NUMBER_OF_FEEDBACKS, $numberOfFeedbacks);

            $reporting_data->add_data_category_row(
                $key,
                self::$COLUMN_IP_ADDRESS,
                $entry[Entry::PROPERTY_IP_ADDRESS]
            );

            if ($isGroupSubmission)
            {
                $individual_submitter = $this->getUserService()->getUserFullNameByIdentifier($entry[Entry::PROPERTY_USER_ID]);
                $reporting_data->add_data_category_row($key, self::$COLUMN_INDIVIDUAL_SUBMITTER, $individual_submitter);
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
        return array(Html::VIEW_TABLE);
    }

    /**
     * @return \Chamilo\Core\User\Service\UserService
     */
    public function getUserService()
    {
        return $this->getService(UserService::class);
    }
}
