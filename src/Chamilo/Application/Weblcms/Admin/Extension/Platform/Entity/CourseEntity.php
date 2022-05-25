<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Core\Rights\Entity\RightsEntity;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Class that describes the courses for the rights editor
 *
 * @author Sven Vanpoucke
 */
class CourseEntity implements RightsEntity
{
    const ENTITY_NAME = 'course';
    const ENTITY_TYPE = 4;

    private static $instance;

    private $course_cache;

    /**
     * Get the fully qualified class name of the object
     *
     * @return string
     */
    public static function class_name()
    {
        return get_called_class();
    }

    /**
     * Counts the items for this entity
     *
     * @param $condition Condition
     *
     * @return int
     */
    public function count_entity_items($condition = null)
    {
        $condition = $this->get_condition($condition);

        return DataManager::count(Course::class, new DataClassCountParameters($condition));
    }

    /**
     * Returns the class name of the data class that is used for this entity
     *
     * @return string
     */
    public static function data_class_class_name()
    {
        return Course::class;
    }

    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new static();
        }

        return self::$instance;
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
    public function get_element_finder_element($id)
    {
        $course = DataManager::retrieve_by_id(
            Course::class, (int) $id
        );

        if (!$course)
        {
            return null;
        }

        $glyph = new FontAwesomeGlyph('chalkboard', [], null, 'fas');

        return new AdvancedElementFinderElement(
            self::ENTITY_TYPE . '_' . $id, $glyph->getClassNamesString(), $course->get_title(),
            strip_tags($course->get_fully_qualified_name())
        );
    }

    /**
     * Retrieves the type for the advanced element finder for the simple rights editor
     */
    public function get_element_finder_type()
    {
        return new AdvancedElementFinderElementType(
            'courses', Translation::get('Courses'), __NAMESPACE__, 'course_feed', []
        );
    }

    /**
     * Returns the cell renderer of this entity
     *
     * @param $browser Application
     *
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
     *
     * @return LocationUserBrowserTableColumnModel
     */
    public function get_entity_column_model($browser)
    {
        // return new LocationUserEntityBrowserTableColumnModel($browser);
    }

    /**
     * Returns the path to the icon of the entity
     *
     * @return String
     */
    public function get_entity_icon()
    {
        return new FontAwesomeGlyph('chalkboard', [], null, 'fas');
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
            StringUtilities::getInstance()->createString(self::ENTITY_NAME)->upperCamelize()->__toString()
        );
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
     * Returns the properties on which can be searched
     *
     * @return Array
     */
    public function get_search_properties()
    {
        return array(Course::PROPERTY_TITLE, Course::PROPERTY_VISUAL_CODE);
    }

    /**
     * Retrieves the entity item ids relevant for a given course
     *
     * @param $course_id integer
     *
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
     * Retrieves the items for this entity
     *
     * @param $condition Condition
     * @param $offset int
     * @param $count int
     * @param $order_property Array
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function retrieve_entity_items($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $condition = $this->get_condition($condition);
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);

        return DataManager::retrieves(Course::class, $parameters);
    }
}
