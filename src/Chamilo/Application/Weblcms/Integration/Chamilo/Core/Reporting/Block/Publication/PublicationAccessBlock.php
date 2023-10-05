<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Publication;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\CourseVisit;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager as WeblcmsTrackingDataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class PublicationAccessBlock extends ToolBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();

        $reporting_data->set_rows(
            array(
                Translation::get('User', null, Manager::CONTEXT),
                Translation::get('OfficialCode', null, Manager::CONTEXT)
            )
        );

        $this->add_reporting_data_rows_for_course_visit_data($reporting_data);

        $course_visits = $this->retrieve_course_visits();

        for ($counter = 0; $counter < $course_visits->count(); $counter ++)
        {
            $reporting_data->add_category($counter);
        }

        $counter = 0;

        foreach ($course_visits as $course_visit)
        {
            $user = DataManager::retrieve_by_id(
                User::class, (string) $course_visit->get_user_id()
            );

            if ($user)
            {
                $name = $user->get_fullname();
                $officialCode = $user->get_official_code();
            }

            $reporting_data->add_data_category_row(
                $counter, Translation::get('User', null, Manager::CONTEXT), $name
            );

            $reporting_data->add_data_category_row(
                $counter, Translation::get('OfficialCode', null, Manager::CONTEXT), $officialCode
            );

            $this->add_reporting_data_from_course_visit_as_row($counter, $reporting_data, $course_visit);

            $counter ++;
        }

        $reporting_data->hide_categories();

        return $reporting_data;
    }

    public function get_views()
    {
        return array(Html::VIEW_TABLE);
    }

    /**
     * Retrieves the course visit records for the given publication
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\CourseVisit>
     */
    public function retrieve_course_visits()
    {
        $content_object_publication_id = $this->getPublicationId();
        $content_object_publication = DataManager::retrieve_by_id(
            ContentObjectPublication::class, $content_object_publication_id
        );

        if (!$content_object_publication instanceof ContentObjectPublication)
        {
            throw new ObjectNotExistException(
                Translation::getInstance()->getTranslation(
                    'ContentObjectPublication', null, 'Chamilo\Application\Weblcms'
                ), $content_object_publication_id
            );
        }

        $category_id = $content_object_publication->get_category_id();
        $category_id = $category_id ?: null;

        $tool_id = $this->get_tool_registration($content_object_publication->get_tool())->get_id();

        $condition = WeblcmsTrackingDataManager::get_course_visit_conditions_by_course_data(
            $content_object_publication->get_course_id(), $tool_id, $category_id, $content_object_publication_id, false
        );

        return WeblcmsTrackingDataManager::retrieves(
            CourseVisit::class, new DataClassRetrievesParameters($condition)
        );
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }
}
