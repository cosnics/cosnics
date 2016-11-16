<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager as WeblcmsDataManager;
use Chamilo\Core\Repository\ContentObject\Announcement\Storage\DataClass\Announcement;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Table\Column\SortableStaticTableColumn;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
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
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->renderAnnouncements();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderAnnouncements()
    {
        $publications = $this->get_content(self::TOOL_ANNOUNCEMENT);
        
        ksort($publications);
        $icon = $this->get_new_announcements_icon();
        
        $data = array();
        
        foreach ($publications as $publication)
        {
            $course_id = $publication[ContentObjectPublication::PROPERTY_COURSE_ID];
            $title = $publication[ContentObject::PROPERTY_TITLE];
            
            $title = htmlspecialchars($title);
            $link = $this->get_course_viewer_link($this->get_course_by_id($course_id), $publication);
            
            $row = array();
            
            $row[] = DatetimeUtilities::format_locale_date(
                null, 
                $publication[ContentObjectPublication::PROPERTY_MODIFIED_DATE]);
            $row[] = $this->get_course_by_id($course_id)->get_title();
            $row[] = '<a href="' . $link . '" >' . $title . '</a></li>';
            
            $data[] = $row;
        }
        
        $headers = array();
        $headers[] = new SortableStaticTableColumn(Translation::get('Date'));
        $headers[] = new SortableStaticTableColumn(Translation::get('Course'));
        $headers[] = new SortableStaticTableColumn(Translation::get('Announcement'));
        
        $table = new SortableTableFromArray($data, $headers, $this->get_parameters());
        
        $html[] = $table->toHtml();
        
        return implode(PHP_EOL, $html);
    }

    public function get_content($tool)
    {
        // All user courses
        $user_courses = CourseDataManager::retrieve_all_courses_from_user($this->get_user());
        
        $this->courses = array();
        
        $course_settings_controller = \Chamilo\Application\Weblcms\CourseSettingsController::getInstance();
        $unique_publications = array();
        while ($course = $user_courses->next_result())
        {
            $this->courses[$course->get_id()] = $course;
            
            if ($course_settings_controller->get_course_setting(
                $course, 
                \Chamilo\Application\Weblcms\CourseSettingsConnector::VISIBILITY) == 1)
            {
                $condition = $this->get_publication_conditions($course, $tool);
                $course_module_id = WeblcmsDataManager::retrieve_course_tool_by_name($tool)->get_id();
                $location = \Chamilo\Application\Weblcms\Rights\WeblcmsRights::getInstance()->get_weblcms_location_by_identifier_from_courses_subtree(
                    \Chamilo\Application\Weblcms\Rights\WeblcmsRights::TYPE_COURSE_MODULE, 
                    $course_module_id, 
                    $course->get_id());
                
                $entities = array();
                $entities[\Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity::ENTITY_TYPE] = \Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity::getInstance(
                    $course->get_id());
                $entities[\Chamilo\Application\Weblcms\Rights\Entities\CourseUserEntity::ENTITY_TYPE] = \Chamilo\Application\Weblcms\Rights\Entities\CourseUserEntity::getInstance();
                $entities[\Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity::ENTITY_TYPE] = \Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity::getInstance();
                
                $publications = WeblcmsDataManager::retrieve_content_object_publications_with_view_right_granted_in_category_location(
                    $location, 
                    $entities, 
                    $condition, 
                    new OrderBy(
                        new PropertyConditionVariable(
                            \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication::class_name(), 
                            \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication::PROPERTY_DISPLAY_ORDER_INDEX)));
                
                if ($publications == 0)
                {
                    continue;
                }
                foreach ($publications->as_array() as $publication)
                {
                    $unique_publications[$course->get_id() . '.' . $publication[ContentObjectPublication::PROPERTY_ID]] = $publication;
                }
            }
        }
        return $unique_publications;
    }

    /**
     *
     * @return string
     */
    private function get_new_announcements_icon()
    {
        return Theme::getInstance()->getImagePath(
            \Chamilo\Application\Weblcms\Tool\Manager::get_tool_type_namespace(self::TOOL_ANNOUNCEMENT), 
            'Logo/' . Theme::ICON_MINI . 'New');
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param string[] $publication
     * @return string
     */
    private function get_course_viewer_link($course, $publication)
    {
        $id = $publication[ContentObjectPublication::PROPERTY_ID];
        
        $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::context();
        $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_ACTION] = \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE;
        $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $course->get_id();
        $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_TOOL] = self::TOOL_ANNOUNCEMENT;
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW;
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID] = $id;
        
        $redirect = new Redirect($parameters);
        return $redirect->getUrl();
    }

    private function get_publication_conditions($course, $tool)
    {
        $type = Announcement::class_name();
        $last_visit_date = \Chamilo\Application\Weblcms\Storage\DataManager::get_last_visit_date(
            $course->get_id(), 
            $this->get_user_id(), 
            $tool, 
            0);
        
        $conditions = array();
        
        $conditions[] = WeblcmsDataManager::get_publications_condition($course, $this->get_user(), $tool, $type);
        $conditions[] = new InequalityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication::class_name(), 
                \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication::PROPERTY_PUBLICATION_DATE), 
            InequalityCondition::GREATER_THAN_OR_EQUAL, 
            new StaticConditionVariable($last_visit_date));
        
        return new AndCondition($conditions);
    }

    protected function get_course_by_id($course_id)
    {
        return $this->courses[$course_id];
    }
}
