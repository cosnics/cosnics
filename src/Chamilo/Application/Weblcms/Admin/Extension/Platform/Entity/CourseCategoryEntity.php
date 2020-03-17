<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity;

use Chamilo\Application\Weblcms\Admin\Extension\Platform\Ajax\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Rights\Entity\NestedRightsEntity;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Class that describes the platform groups for the rights editor
 *
 * @author Sven Vanpoucke
 */
class CourseCategoryEntity implements NestedRightsEntity
{
    const ENTITY_NAME = 'course_category';
    const ENTITY_TYPE = 3;

    private static $instance;

    private $course_category_cache;

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

        return DataManager::count(
            CourseCategory::class_name(), new DataClassCountParameters($condition)
        );
    }

    /**
     * Retrieves the entity item ids relevant for a given user (direct subscribed platformgroups and their parents)
     *
     * @param $user_id integer
     *
     * @return array
     */

    /**
     * Returns the class name of the data class that is used for this entity
     *
     * @return string
     */
    public static function data_class_class_name()
    {
        return CourseCategory::class_name();
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
     * Function that can be filled in extensions of this class to limit the platform groups
     *
     * @return Condition
     */
    public function get_condition($condition)
    {
        return $condition;
    }

    /**
     * Retrieves an element for the advanced element finder for the simple rights editor with the given id
     */
    public function get_element_finder_element($id)
    {
        $course_category = DataManager::retrieve_by_id(
            CourseCategory::class_name(), $id
        );
        if (!$course_category)
        {
            return null;
        }

        return new AdvancedElementFinderElement(
            self::ENTITY_TYPE . '_' . $id, 'type type_course_category', $course_category->get_name(),
            strip_tags($course_category->get_fully_qualified_name())
        );
    }

    /**
     * Retrieves the type for the advanced element finder for the simple rights editor
     */
    public function get_element_finder_type()
    {
        return new AdvancedElementFinderElementType(
            'course_categories', Translation::get('CourseCategories'), __NAMESPACE__, 'course_category_feed', array()
        );
    }

    /**
     * Returns the cell renderer of this entity
     *
     * @param $browser Application
     *
     * @return LocationPlatformGroupBrowserTableCellRenderer
     */
    public function get_entity_cell_renderer($browser)
    {
        // return new LocationPlatformGroupBrowserTableCellRenderer($browser);
    }

    /**
     * Returns the column model of this entity
     *
     * @param $browser Application
     *
     * @return LocationPlatformGroupBrowserTableColumnModel
     */
    public function get_entity_column_model($browser)
    {
        // return new LocationPlatformGroupBrowserTableColumnModel($browser);
    }

    /**
     * Returns the path to the icon of the entity
     *
     * @return String
     */
    public function get_entity_icon()
    {
        return new FontAwesomeGlyph('folder', array(), null, 'fas');
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
     * Returns the property for the ID column of this entity
     *
     * @return String
     */
    public function get_id_property()
    {
        return CourseCategory::PROPERTY_ID;
    }

    /**
     * Returns the property for the PARENT column of this entity
     *
     * @return String
     */
    public function get_parent_property()
    {
        return CourseCategory::PROPERTY_PARENT;
    }

    /**
     * Returns the root ids for this entity
     *
     * @return Array<int>
     */
    public function get_root_ids()
    {
        return array(DataManager::get_root_group()->get_id());
    }

    /**
     * Returns the properties on which can be searched
     *
     * @return Array
     */
    public function get_search_properties()
    {
        return array(CourseCategory::PROPERTY_NAME, CourseCategory::PROPERTY_CODE);
    }

    /**
     * Returns the property for the TITLE column of this entity
     *
     * @return String
     */
    public function get_title_property()
    {
        return CourseCategory::PROPERTY_NAME;
    }

    /**
     * Returns the xml feed which will be used for the element finder and for the ajax tree menu
     */
    public function get_xml_feed()
    {
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => Manager::package(), Manager::PARAM_ACTION => 'course_category'
            )
        );

        return $redirect->getUrl();
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
        if (is_null($this->course_category_cache[$user_id]))
        {

            $this->course_category_cache[$user_id] = array($user_id);
        }

        return $this->course_category_cache[$user_id];
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

        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieves(
            CourseCategory::class_name(), new DataClassRetrievesParameters($condition, $count, $offset, $order_property)
        );
    }
}
