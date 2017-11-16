<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Core\Rights\Entity\RightsEntity;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;

/**
 * Class that describes the courses for the rights editor
 *
 * @author Sven Vanpoucke
 */
class CourseEntity implements RightsEntity
{
    const ENTITY_NAME = 'course';
    const ENTITY_TYPE = 4;

    private $course_cache;

    private static $instance;

    public static function getInstance()
    {
        if (! isset(self::$instance))
        {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * Retrieves the items for this entity
     *
     * @param $condition Condition
     * @param $offset int
     * @param $count int
     * @param $order_property Array
     * @return ObjectResultSet
     */
    public function retrieve_entity_items($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $condition = $this->get_condition($condition);
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieves(Course::class_name(), $parameters);
    }

    /**
     * Retrieves the entity item ids relevant for a given course
     *
     * @param $course_id integer
     * @return array
     */
    public function retrieve_entity_item_ids_linked_to_user($user_id)
    {
        if (is_null($this->course_cache[$user_id]))
        {

            $this->course_cache[$user_id] = array($user_id);
        }
        return $this->course_cache[$user_id];
    }

    /**
     * Counts the items for this entity
     *
     * @param $condition Condition
     * @return int
     */
    public function count_entity_items($condition = null)
    {
        $condition = $this->get_condition($condition);
        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::count(Course::class_name(), new DataClassCountParameters($condition));
    }

    /**
     * Returns the name of this entity
     *
     * @return String
     */
    public function get_entity_name()
    {
        return self::ENTITY_NAME;
    }

    /**
     * Returns the translated name of this entiry for displaying purposes only!
     *
     * @return String Translated name of the entity
     */
    public function get_entity_translated_name()
    {
        return Translation::get(
            StringUtilities::getInstance()->createString(self::ENTITY_NAME)->upperCamelize()->__toString());
    }

    /**
     * Returns the type of this entity
     *
     * @return int
     */
    public function get_entity_type()
    {
        return self::ENTITY_TYPE;
    }

    /**
     * Returns the path to the icon of the entity
     *
     * @return String
     */
    public function get_entity_icon()
    {
        return Theme::getInstance()->getImagePath(__NAMESPACE__, 'Place/Course');
    }

    /**
     * Returns the cell renderer of this entity
     *
     * @param $browser Application
     * @return null
     */
    public function get_entity_cell_renderer($browser)
    {
        // return null;
    }

    /**
     * Returns the column model of this entity
     *
     * @param $browser Application
     * @return LocationUserBrowserTableColumnModel
     */
    public function get_entity_column_model($browser)
    {
        // return new LocationUserEntityBrowserTableColumnModel($browser);
    }

    /**
     * Returns the properties on which can be searched
     *
     * @return Array
     */
    public function get_search_properties()
    {
        return array(Course::PROPERTY_TITLE, Course::PROPERTY_VISUAL_CODE);
    }

    /**
     * Function that can be filled in extensions of this class to limit the courses
     *
     * @return Condition
     */
    public function get_condition($condition)
    {
        return $condition;
    }

    /**
     * Retrieves the type for the advanced element finder for the simple rights editor
     */
    public function get_element_finder_type()
    {
        return new AdvancedElementFinderElementType(
            'courses',
            Translation::get('Courses'),
            __NAMESPACE__,
            'course_feed',
            array());
    }

    /**
     * Retrieves the type for the advanced element finder for the simple rights editor
     */
    public function get_element_finder_element($id)
    {
        $course = \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_by_id(
            Course::class_name(),
            (int) $id);

        if (! $course)
        {
            return null;
        }

        return new AdvancedElementFinderElement(
            self::ENTITY_TYPE . '_' . $id,
            'type type_course',
            $course->get_title(),
            strip_tags($course->get_fully_qualified_name()));
    }

    /**
     * Returns the class name of the data class that is used for this entity
     *
     * @return string
     */
    public static function data_class_class_name()
    {
        return Course::class_name();
    }

    /**
     * Get the fully qualified class name of the object
     *
     * @return string
     */
    public static function class_name()
    {
        return get_called_class();
    }
}
