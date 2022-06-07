<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CourseUserEntity;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager as WeblcmsDataManager;
use Chamilo\Core\Repository\ContentObject\Announcement\Storage\DataClass\Announcement;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Table\Column\SortableStaticTableColumn;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 *
 * @package Chamilo\Application\Weblcms\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AnnouncementComponent extends Manager
{
    const TOOL_ANNOUNCEMENT = 'announcement';

    private $courses;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ViewPersonalCourses');

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->renderAnnouncements();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_content($tool)
    {
        // All user courses
        $user_courses = CourseDataManager::retrieve_all_courses_from_user($this->get_user());

        $this->courses = [];

        $course_settings_controller = CourseSettingsController::getInstance();
        $unique_publications = [];
        foreach ($user_courses as $course)
        {
            $this->courses[$course->get_id()] = $course;

            if ($course_settings_controller->get_course_setting(
                    $course, CourseSettingsConnector::VISIBILITY
                ) == 1)
            {
                $condition = $this->get_publication_conditions($course, $tool);
                $course_module_id = WeblcmsDataManager::retrieve_course_tool_by_name($tool)->get_id();
                $location = WeblcmsRights::getInstance()->get_weblcms_location_by_identifier_from_courses_subtree(
                        WeblcmsRights::TYPE_COURSE_MODULE, $course_module_id, $course->get_id()
                    );

                $entities = [];
                $entities[CourseGroupEntity::ENTITY_TYPE] = CourseGroupEntity::getInstance(
                    $course->get_id()
                );
                $entities[CourseUserEntity::ENTITY_TYPE] = CourseUserEntity::getInstance();
                $entities[CoursePlatformGroupEntity::ENTITY_TYPE] = CoursePlatformGroupEntity::getInstance();

                $publications =
                    WeblcmsDataManager::retrieve_content_object_publications_with_view_right_granted_in_category_location(
                        $location, $entities, $condition, array(
                            new OrderProperty(
                                new PropertyConditionVariable(
                                    ContentObjectPublication::class,
                                    ContentObjectPublication::PROPERTY_DISPLAY_ORDER_INDEX
                                )
                            )
                        )
                    );

                if ($publications == 0)
                {
                    continue;
                }
                foreach ($publications as $publication)
                {
                    $unique_publications[$course->get_id() . '.' .
                    $publication[ContentObjectPublication::PROPERTY_ID]] = $publication;
                }
            }
        }

        return $unique_publications;
    }

    protected function get_course_by_id($course_id)
    {
        return $this->courses[$course_id];
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param string[] $publication
     *
     * @return string
     */
    private function get_course_viewer_link($course, $publication)
    {
        $id = $publication[ContentObjectPublication::PROPERTY_ID];

        $parameters[Manager::PARAM_CONTEXT] = Manager::context();
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW_COURSE;
        $parameters[Manager::PARAM_COURSE] = $course->get_id();
        $parameters[Manager::PARAM_TOOL] = self::TOOL_ANNOUNCEMENT;
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] =
            \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW;
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID] = $id;

        $redirect = new Redirect($parameters);

        return $redirect->getUrl();
    }

    private function get_publication_conditions($course, $tool)
    {
        $type = Announcement::class;
        $last_visit_date = WeblcmsDataManager::get_last_visit_date(
            $course->get_id(), $this->get_user_id(), $tool
        );

        $conditions = [];

        $conditions[] = WeblcmsDataManager::get_publications_condition($course, $this->get_user(), $tool, $type);
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_PUBLICATION_DATE
            ), ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable($last_visit_date)
        );

        return new AndCondition($conditions);
    }

    /**
     *
     * @return string
     */
    public function renderAnnouncements()
    {
        $publications = $this->get_content(self::TOOL_ANNOUNCEMENT);

        ksort($publications);

        $data = [];

        foreach ($publications as $publication)
        {
            $course_id = $publication[ContentObjectPublication::PROPERTY_COURSE_ID];
            $title = $publication[ContentObject::PROPERTY_TITLE];

            $title = htmlspecialchars($title);
            $link = $this->get_course_viewer_link($this->get_course_by_id($course_id), $publication);

            $row = [];

            $row[] = DatetimeUtilities::getInstance()->formatLocaleDate(
                null, $publication[ContentObjectPublication::PROPERTY_MODIFIED_DATE]
            );
            $row[] = $this->get_course_by_id($course_id)->get_title();
            $row[] = '<a href="' . $link . '" >' . $title . '</a></li>';

            $data[] = $row;
        }

        $headers = [];
        $headers[] = new SortableStaticTableColumn(Translation::get('Date'));
        $headers[] = new SortableStaticTableColumn(Translation::get('Course'));
        $headers[] = new SortableStaticTableColumn(Translation::get('Announcement'));

        $table = new SortableTableFromArray($data, $headers, $this->get_parameters());

        $html[] = $table->toHtml();

        return implode(PHP_EOL, $html);
    }
}
