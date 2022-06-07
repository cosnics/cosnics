<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Assignment;

use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\AssignmentStudentEntriesTemplate;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\Repository\AssignmentRepository;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block with an overiew of the assignments the user has
 *          sent a submission for
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class CourseUserAssignmentInformationBlock extends AssignmentReportingManager
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(
            array(
                Translation::get('Title'),
                Translation::get('NumberOfSubmissions'),
                Translation::get('LastSubmission'),
                Translation::get('NumberOfFeedbacks'),
                Translation::get('LastScore'),
                Translation::get('Submissions')
            )
        );

        $userId = $this->get_user_id();
        $glyph = new FontAwesomeGlyph('chart-pie');

        $courseId = $this->get_parent()->get_parent()->get_parent()->get_parameter(
            Manager::PARAM_COURSE
        );

        $publications = $this->retrieveAssignmentPublicationsForCourse($courseId, Entry::ENTITY_TYPE_USER);

        $params_detail = $this->get_parent()->get_parameters();
        $params_detail[Manager::PARAM_TEMPLATE_ID] =
            AssignmentStudentEntriesTemplate::class;
        $params_detail[\Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTITY_ID] = $userId;
        $params_detail[\Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTITY_TYPE] =
            Entry::ENTITY_TYPE_USER;

        $key = 0;
        foreach ($publications as $publication)
        {
            $publicationId = $publication[ContentObjectPublication::PROPERTY_ID];

            $publicationObject = new ContentObjectPublication();
            $publicationObject->setId($publicationId);

            if (!DataManager::is_publication_target_user($userId, $publicationId))
            {
                continue;
            }

            ++ $key;

            $url_title = $this->getAssignmentUrl($courseId, $publicationId);

            $entryStatistics = $this->getAssignmentService()->findEntryStatisticsForEntityByContentObjectPublication(
                $publicationObject, Entry::ENTITY_TYPE_USER, $userId
            );

            $last = DatetimeUtilities::getInstance()->formatLocaleDate(
                Translation::get('DateFormatShort', null, StringUtilities::LIBRARIES) . ', ' .
                Translation::get('TimeNoSecFormat', null, StringUtilities::LIBRARIES),
                $entryStatistics[AssignmentRepository::LAST_ENTRY_SUBMITTED_DATE]
            );

            $params_detail[Manager::PARAM_PUBLICATION] =
                $publication[ContentObjectPublication::PROPERTY_ID];
            $link = $this->createLink($this->get_parent()->get_url($params_detail), $glyph->render());

            $reporting_data->add_category($key);
            $reporting_data->add_data_category_row(
                $key, Translation::get('Title'),
                $this->createLink($url_title, $publication[ContentObject::PROPERTY_TITLE], '_blank')
            );

            $reporting_data->add_data_category_row(
                $key, Translation::get('NumberOfSubmissions'), $entryStatistics[AssignmentRepository::ENTRIES_COUNT]
            );

            $lastScore = $this->get_score_bar(
                $this->getAssignmentService()->getLastScoreForContentObjectPublicationEntityTypeAndId(
                    $publicationObject, Entry::ENTITY_TYPE_USER, $userId
                )
            );

            $feedbackCount =
                $this->getAssignmentService()->countFeedbackForContentObjectPublicationByEntityTypeAndEntityId(
                    $publicationObject, Entry::ENTITY_TYPE_USER, $userId
                );

            $reporting_data->add_data_category_row($key, Translation::get('LastSubmission'), $last);
            $reporting_data->add_data_category_row($key, Translation::get('NumberOfFeedbacks'), $feedbackCount);
            $reporting_data->add_data_category_row($key, Translation::get('LastScore'), $lastScore);
            $reporting_data->add_data_category_row($key, Translation::get('Submissions'), $link);
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
