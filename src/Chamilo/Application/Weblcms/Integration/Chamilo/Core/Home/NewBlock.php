<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager as WeblcmsDataManager;
use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Description of weblcms_new_block
 *
 * @author Anthony Hurst (Hogeschool Gent)
 */
abstract class NewBlock extends Block
{
    const TOOL_ANNOUNCEMENT = 'Announcement';
    const TOOL_ASSIGNMENT = 'Assignment';
    const TOOL_DOCUMENT = 'Document';
    const OVERSIZED_SETTING = 'oversized_new_list_threshold';
    const FORCE_OVERSIZED = 'force_oversized_newblocks';
    const DO_FORCE_OVERSIZED = '1';
    const OVERSIZED_WARNING = 'oversized';

    private $courses;

    /**
     *
     * @see \Chamilo\Core\Home\Renderer\Type\Basic\BlockRenderer::renderContentHeader()
     */
    public function renderContentHeader()
    {
    }

    /**
     *
     * @see \Chamilo\Core\Home\Renderer\Type\Basic\BlockRenderer::renderContentFooter()
     */
    public function renderContentFooter()
    {
    }

    public function getContent($tool)
    {
        // All user courses for active course types
        $excludedCourseTypes = explode(
            ',',
            Configuration :: get_instance()->get_setting(array('Chamilo\Application\Weblcms', 'excluded_course_types')));
        $archiveCondition = new NotCondition(
            new InCondition(
                new PropertyConditionVariable(Course :: class_name(), Course :: PROPERTY_COURSE_TYPE_ID),
                $excludedCourseTypes));

        // All user courses
        $user_courses = CourseDataManager :: retrieve_all_courses_from_user($this->getUser(), $archiveCondition);

        $threshold = intval(PlatformSetting :: get(self :: OVERSIZED_SETTING, 'Chamilo\Application\Weblcms'));

        if ($threshold !== 0 && Request :: get(self :: FORCE_OVERSIZED) != self :: DO_FORCE_OVERSIZED &&
             $user_courses->size() > $threshold)
        {
            $this->courses = array();
            return self :: OVERSIZED_WARNING;
        }

        $this->courses = array();

        $course_settings_controller = \Chamilo\Application\Weblcms\CourseSettingsController :: get_instance();
        $unique_publications = array();

        while ($course = $user_courses->next_result())
        {
            $this->courses[$course->get_id()] = $course;

            if ($course_settings_controller->get_course_setting(
                $course,
                \Chamilo\Application\Weblcms\CourseSettingsConnector :: VISIBILITY) == 1)
            {
                $condition = $this->getPublicationConditions($course, $tool);
                $course_module_id = WeblcmsDataManager :: retrieve_course_tool_by_name($tool)->get_id();
                $location = \Chamilo\Application\Weblcms\Rights\WeblcmsRights :: get_instance()->get_weblcms_location_by_identifier_from_courses_subtree(
                    \Chamilo\Application\Weblcms\Rights\WeblcmsRights :: TYPE_COURSE_MODULE,
                    $course_module_id,
                    $course->get_id());

                $entities = array();
                $entities[\Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity :: ENTITY_TYPE] = \Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity :: get_instance(
                    $course->get_id());
                $entities[\Chamilo\Application\Weblcms\Rights\Entities\CourseUserEntity :: ENTITY_TYPE] = \Chamilo\Application\Weblcms\Rights\Entities\CourseUserEntity :: get_instance();
                $entities[\Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity :: ENTITY_TYPE] = \Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity :: get_instance();

                $publications = WeblcmsDataManager :: retrieve_content_object_publications_with_view_right_granted_in_category_location(
                    $location,
                    $entities,
                    $condition,
                    new OrderBy(
                        new PropertyConditionVariable(
                            \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication :: class_name(),
                            \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX)));

                if ($publications == 0)
                {
                    continue;
                }
                foreach ($publications->as_array() as $publication)
                {
                    $unique_publications[$course->get_id() . '.' . $publication[ContentObjectPublication :: PROPERTY_ID]] = $publication;
                }
            }
        }

        usort($unique_publications, array($this, 'sortPublications'));

        return $unique_publications;
    }

    private function getPublicationConditions($course, $tool)
    {
        $type = null;

        $last_visit_date = \Chamilo\Application\Weblcms\Storage\DataManager :: get_last_visit_date(
            $course->get_id(),
            $this->getUserId(),
            $tool,
            0);

        $conditions = array();
        $conditions[] = WeblcmsDataManager :: get_publications_condition(
            $course,
            $this->getUser(),
            $tool,
            $this->getContentObjectTypes());
        $conditions[] = new InequalityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication :: class_name(),
                \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication :: PROPERTY_PUBLICATION_DATE),
            InequalityCondition :: GREATER_THAN_OR_EQUAL,
            new StaticConditionVariable($last_visit_date));
        return new AndCondition($conditions);
    }

    protected function getCourseById($course_id)
    {
        return $this->courses[$course_id];
    }

    public function getOversizedWarning()
    {
        return '<div class="warning-message" style="width: auto; margin: 0 0 1em 0; position: static;">' .
             Translation :: get('OversizedWarning', null, Utilities :: COMMON_LIBRARIES) . ' <a href="?' .
             Utilities :: get_current_query_string(array(self :: FORCE_OVERSIZED => self :: DO_FORCE_OVERSIZED)) . '">' .
             Translation :: get('ForceOversized', null, Utilities :: COMMON_LIBRARIES) . '</a></div>';
    }

    /**
     *
     * @return string[]
     */
    abstract public function getContentObjectTypes();

    abstract public function getToolName();

    /**
     *
     * @param string[] $publicationLeft
     * @param string[] $publicationRight
     * @return integer
     */
    public function sortPublications($publicationLeft, $publicationRight)
    {
        if ($publicationLeft[ContentObjectPublication :: PROPERTY_MODIFIED_DATE] ==
             $publicationRight[ContentObjectPublication :: PROPERTY_MODIFIED_DATE])
        {
            return 0;
        }
        elseif ($publicationLeft[ContentObjectPublication :: PROPERTY_MODIFIED_DATE] >
             $publicationRight[ContentObjectPublication :: PROPERTY_MODIFIED_DATE])
        {
            return - 1;
        }
        else
        {
            return 1;
        }
    }

    public function displayContent()
    {
        $publications = $this->getContent($this->getToolName());

        if (count($publications) == 0)
        {
            $html = array();

            $html[] = '<div class="panel-body portal-block-content' . ($this->getBlock()->isVisible() ? '' : ' hidden') .
                 '">';
            $html[] = Translation :: get('NoNewPublicationsSinceLastVisit');
            $html[] = '</div>';

            return implode(PHP_EOL, $html);
        }

        return $this->displayNewItems($publications);
    }

    public function displayNewItems($publications)
    {
        $html = array();

        $html[] = '<div class="list-group portal-block-content portal-block-new-list' .
             ($this->getBlock()->isVisible() ? '' : ' hidden') . '">';

        foreach ($publications as $publication)
        {
            $html[] = $this->displayNewItem($publication);
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    abstract public function getCourseViewerLink($course, $publication);

    public function getBadgeContent($publication)
    {
        return '<span class="badge badge-date">' .
             date('j M', $publication[ContentObjectPublication :: PROPERTY_MODIFIED_DATE]) . '</span>';
    }
}
