<?php
namespace Chamilo\Application\Weblcms\Rights\Entities;

use Chamilo\Application\Weblcms\Ajax\Manager;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager as CourseGroupDataManager;
use Chamilo\Core\Rights\Entity\NestedRightsEntity;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Class that describes the course groups for the rights editor
 *
 * @author Sven Vanpoucke
 */
class CourseGroupEntity implements NestedRightsEntity
{
    const ENTITY_NAME = 'course_group';
    const ENTITY_TYPE = 4;

    private static $instance;

    private $course_group_cache;

    private $course_id;

    public function __construct($course_id)
    {
        $this->course_id = $course_id;
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

        return DataManager::count(CourseGroup::class, new DataClassCountParameters($condition));
    }

    /**
     * Returns the class name of the data class that is used for this entity
     *
     * @return string
     */
    public static function data_class_class_name()
    {
        return CourseGroup::class;
    }

    public static function getInstance($course_id)
    {
        if (!isset(self::$instance))
        {
            self::$instance = new self($course_id);
        }

        return self::$instance;
    }

    /**
     * Adds the course condition to the given condition
     *
     * @return Condition
     */
    private function get_condition($condition)
    {
        $course_condition = new EqualityCondition(
            new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_COURSE_CODE),
            new StaticConditionVariable($this->course_id)
        );

        if ($condition)
        {
            $conditions = array();

            $conditions[] = $condition;
            $conditions[] = $course_condition;

            return new AndCondition($conditions);
        }

        return $course_condition;
    }

    public function get_course_id()
    {
        return $this->course_id;
    }

    /**
     * Retrieves an element for the advanced element finder for the simple rights editor with the given id
     */
    public function get_element_finder_element($id)
    {
        $group = CourseGroupDataManager::retrieve_by_id(CourseGroup::class, $id);
        if (!$group)
        {
            return null;
        }

        $glyph = new FontAwesomeGlyph('users', array(), null, 'fas');

        return new AdvancedElementFinderElement(
            self::ENTITY_TYPE . '_' . $id, $glyph->getClassNamesString(), $group->get_name(), $group->get_description()
        );
    }

    /**
     * Retrieves the type for the advanced element finder for the simple rights editor
     */
    public function get_element_finder_type()
    {
        return new AdvancedElementFinderElementType(
            'course_groups', Translation::get('CourseGroups'), Manager::package(), 'course_groups_feed',
            array('course_id' => $this->course_id)
        );
    }

    /**
     * Returns the path to the icon of the entity
     *
     * @return String
     */
    public function get_entity_icon()
    {
        return new FontAwesomeGlyph('users', array(), null, 'fas');
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
            (string) StringUtilities::getInstance()->createString(self::ENTITY_NAME)->upperCamelize()
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
     * Returns the property for the ID column of this entity
     *
     * @return String
     */
    public function get_id_property()
    {
        return CourseGroup::PROPERTY_ID;
    }

    /**
     * Returns the property for the PARENT column of this entity
     *
     * @return String
     */
    public function get_parent_property()
    {
        return CourseGroup::PROPERTY_PARENT_ID;
    }

    /**
     * Returns the root ids for this entity
     *
     * @return Array<int>
     */
    public function get_root_ids()
    {
        return array(CourseGroupDataManager::retrieve_course_group_root($this->course_id)->get_id());
    }

    /**
     * Returns the properties on which can be searched
     *
     * @return Array
     */
    public function get_search_properties()
    {
        return array(CourseGroup::PROPERTY_NAME, CourseGroup::PROPERTY_DESCRIPTION);
    }

    // Helper functionality

    /**
     * Returns the property for the TITLE column of this entity
     *
     * @return String
     */
    public function get_title_property()
    {
        return CourseGroup::PROPERTY_NAME;
    }

    /**
     * Returns the xml feed which will be used for the element finder and for the ajax tree menu
     */
    public function get_xml_feed()
    {
        return '';
    }

    /**
     * Retrieves the entity item ids relevant for a given user
     *
     * @param $user_id integer
     *
     * @return array
     */
    public function retrieve_entity_item_ids_linked_to_user($user_id)
    {
        if (is_null($this->course_group_cache[$user_id]))
        {
            $course_groups = CourseGroupDataManager::get_user_course_groups($user_id, $this->course_id); // todo:
            foreach ($course_groups as $course_group)
            {
                $this->course_group_cache[$user_id][] = $course_group->get_id();
            }
        }

        return $this->course_group_cache[$user_id];
    }

    /**
     * Retrieves the items for this entity
     *
     * @param $condition Condition
     * @param $offset int
     * @param $count int
     * @param $order_property Array
     *
     * @return ObjectResultSet
     */
    public function retrieve_entity_items($condition = null, $offset = null, $count = null, $order_property = null)
    {
        $condition = $this->get_condition($condition);

        return CourseGroupDataManager::retrieves(
            CourseGroup::class, new DataClassRetrievesParameters($condition, $count, $offset, $order_property)
        );
    }
}
