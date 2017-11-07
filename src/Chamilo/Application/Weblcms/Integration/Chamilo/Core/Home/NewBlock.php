<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager as WeblcmsDataManager;
use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class NewBlock extends Block
{
    const TOOL_ANNOUNCEMENT = 'Announcement';
    const TOOL_ASSIGNMENT = 'Assignment';
    const TOOL_DOCUMENT = 'Document';

    /**
     *
     * @var \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course[]
     */
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

    /**
     *
     * @param string $tool
     * @return string[][]
     */
    public function getContent($tool)
    {
        // All user courses for active course types
        $excludedCourseTypes = explode(
            ',', 
            Configuration::getInstance()->get_setting(array('Chamilo\Application\Weblcms', 'excluded_course_types')));
        $archiveCondition = new NotCondition(
            new InCondition(
                new PropertyConditionVariable(Course::class_name(), Course::PROPERTY_COURSE_TYPE_ID), 
                $excludedCourseTypes));
        
        // All user courses
        $user_courses = CourseDataManager::retrieve_all_courses_from_user($this->getUser(), $archiveCondition);
        
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
                $condition = $this->getPublicationConditions($course, $tool);
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
        
        usort($unique_publications, array($this, 'sortPublications'));
        
        return $unique_publications;
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param string $tool
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    private function getPublicationConditions($course, $tool)
    {
        $type = null;
        
        $last_visit_date = \Chamilo\Application\Weblcms\Storage\DataManager::get_last_visit_date(
            $course->get_id(), 
            $this->getUserId(), 
            $tool, 
            0);
        
        $conditions = array();
        $conditions[] = WeblcmsDataManager::get_publications_condition(
            $course, 
            $this->getUser(), 
            $tool, 
            $this->getContentObjectTypes());
        $conditions[] = new InequalityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication::class_name(), 
                \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication::PROPERTY_PUBLICATION_DATE), 
            InequalityCondition::GREATER_THAN_OR_EQUAL, 
            new StaticConditionVariable($last_visit_date));
        return new AndCondition($conditions);
    }

    /**
     *
     * @param integer $course_id
     * @return \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course
     */
    protected function getCourseById($course_id)
    {
        return $this->courses[$course_id];
    }

    /**
     *
     * @return string[]
     */
    abstract public function getContentObjectTypes();

    /**
     *
     * @return string
     */
    abstract public function getToolName();

    /**
     *
     * @param string[] $publicationLeft
     * @param string[] $publicationRight
     * @return integer
     */
    public function sortPublications($publicationLeft, $publicationRight)
    {
        if ($publicationLeft[ContentObjectPublication::PROPERTY_MODIFIED_DATE] ==
             $publicationRight[ContentObjectPublication::PROPERTY_MODIFIED_DATE])
        {
            return 0;
        }
        elseif ($publicationLeft[ContentObjectPublication::PROPERTY_MODIFIED_DATE] >
             $publicationRight[ContentObjectPublication::PROPERTY_MODIFIED_DATE])
        {
            return - 1;
        }
        else
        {
            return 1;
        }
    }

    /**
     *
     * @see \Chamilo\Core\Home\Renderer\Type\Basic\BlockRenderer::displayContent()
     */
    public function displayContent()
    {
        $publications = $this->getContent($this->getToolName());
        
        if (count($publications) == 0)
        {
            $html = array();
            
            $html[] = '<div class="panel-body portal-block-content' . ($this->getBlock()->isVisible() ? '' : ' hidden') .
                 '">';
            $html[] = Translation::get('NoNewPublicationsSinceLastVisit');
            $html[] = '</div>';
            
            return implode(PHP_EOL, $html);
        }
        
        return $this->displayNewItems($publications);
    }

    /**
     *
     * @param string[][] $publications
     * @return string
     */
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

    /**
     *
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param string[] $publication
     * @return string
     */
    abstract public function getCourseViewerLink(Course $course, $publication);

    /**
     *
     * @param string[] $publication
     * @return string
     */
    public function getBadgeContent($publication)
    {
        return '<span class="badge badge-date">' .
             date('j M', $publication[ContentObjectPublication::PROPERTY_MODIFIED_DATE]) . '</span>';
    }

    /**
     *
     * @param string[] $publication
     * @return string
     */
    public function displayNewItem($publication)
    {
        $html = array();
        
        $course_id = $publication[ContentObjectPublication::PROPERTY_COURSE_ID];
        $title = $publication[ContentObject::PROPERTY_TITLE];
        $link = $this->getCourseViewerLink($this->getCourseById($course_id), $publication);
        
        $html[] = '<a href="' . $link . '" class="list-group-item">';
        $html[] = $this->getBadgeContent($publication);
        $html[] = '<p class="list-group-item-text">' . $title . '</p>';
        $html[] = '<h5 class="list-group-item-heading">' . $this->getCourseById($course_id)->get_title() . '</h5>';
        
        $html[] = '</a>';
        
        return implode(PHP_EOL, $html);
    }
}
