<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\AssignmentEntitiesTemplate;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\Repository\AssignmentRepository;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block displaying all assigments within a course and their
 *          submission stats
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class AssignmentBlock extends AssignmentReportingManager
{

    public function count_data()
    {
        $reporting_data = new ReportingData();

        $reporting_data->set_rows(
            array(
                Translation::get('Title'),
                Translation::get('NumberOfSubmissions'),
                Translation::get('LastSubmission'),
                Translation::get('AverageScore'),
                Translation::get('AssignmentDetails')
            )
        );

        $course_id = $this->getCourseId();

        $count = 1;
        $glyph = new FontAwesomeGlyph('chart-pie');

        $publicationsById = [];

        $publications = $this->retrieveAssignmentPublicationsForCourse($course_id);
        foreach ($publications as $publication)
        {
            $publicationsById[$publication[DataClass::PROPERTY_ID]] = $publication;
        }

        $publicationsStatistics =
            $this->getAssignmentService()->findEntryStatisticsByContentObjectPublicationIdentifiers(
                    array_keys($publicationsById)
                );

        foreach ($publicationsStatistics as $publicationStatistics)
        {
            $publicationId = $publicationStatistics[Entry::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID];

            $publication = $publicationsById[$publicationId];
            $last_submission = $publicationStatistics[AssignmentRepository::LAST_ENTRY_SUBMITTED_DATE];
            $score = $publicationStatistics[AssignmentRepository::AVERAGE_SCORE];
            $entriesCount = $publicationStatistics[AssignmentRepository::ENTRIES_COUNT];

            if ($last_submission > 0)
            {
                $last_submission = DatetimeUtilities::getInstance()->formatLocaleDate(
                    Translation::get('DateFormatShort', null, StringUtilities::LIBRARIES) . ', ' .
                    Translation::get('TimeNoSecFormat', null, StringUtilities::LIBRARIES), $last_submission
                );
            }

            if ($score > 0)
            {
                $score = $this->get_score_bar($score);
            }

            $params = $this->get_parent()->get_parameters();
            $params[Manager::PARAM_TEMPLATE_ID] = AssignmentEntitiesTemplate::class;
            $params[Manager::PARAM_PUBLICATION] = $publicationId;

            $link = $this->createLink($this->get_parent()->get_url($params), $glyph->render());

            $url = $this->getAssignmentUrl($course_id, $publicationId);

            $reporting_data->add_category($count);
            $reporting_data->add_data_category_row(
                $count, Translation::get('Title'),
                $this->createLink($url, $publication[ContentObject::PROPERTY_TITLE], '_blank')
            );

            $reporting_data->add_data_category_row(
                $count, Translation::get('NumberOfSubmissions'), $entriesCount
            );

            $reporting_data->add_data_category_row($count, Translation::get('LastSubmission'), $last_submission);
            $reporting_data->add_data_category_row($count, Translation::get('AverageScore'), $score);
            $reporting_data->add_data_category_row($count, Translation::get('AssignmentDetails'), $link);

            $count ++;
        }

        $reporting_data->hide_categories();

        return $reporting_data;
    }

    public function get_views()
    {
        return array(Html::VIEW_TABLE);
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }
}
