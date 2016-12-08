<?php
namespace Chamilo\Core\Rights\Entity;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Rights\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Class that describes the platform groups for the rights editor
 * 
 * @author Sven Vanpoucke
 */
class PlatformGroupEntity implements NestedRightsEntity
{
    const ENTITY_NAME = 'group';
    const ENTITY_TYPE = 2;

    private $platform_group_cache;

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
        
        return \Chamilo\Core\Group\Storage\DataManager::retrieves(
            Group::class_name(), 
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property));
    }

    /**
     * Retrieves the entity item ids relevant for a given user (direct subscribed platformgroups and their parents)
     * 
     * @param $user_id integer
     * @return array
     */
    public function retrieve_entity_item_ids_linked_to_user($user_id)
    {
        if (is_null($this->platform_group_cache[$user_id]))
        {
            $this->platform_group_cache[$user_id] = \Chamilo\Core\Group\Storage\DataManager::retrieve_all_subscribed_groups_array(
                $user_id, 
                true);
        }
        return $this->platform_group_cache[$user_id];
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
        return \Chamilo\Core\Group\Storage\DataManager::count(
            Group::class_name(), 
            new DataClassCountParameters($condition));
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
            (string) StringUtilities::getInstance()->createString(self::ENTITY_NAME)->upperCamelize());
    }

    /**
     * Returns the type of this entity
     * 
     * @return int
     */
    public function get_entity_type()
    {
        return static::ENTITY_TYPE;
    }

    /**
     * Returns the path to the icon of the entity
     * 
     * @return String
     */
    public function get_entity_icon()
    {
        return Theme::getInstance()->getImagePath('Chamilo\Core\Rights\Editor', 'Place/Group');
    }

    /**
     * Returns the property for the ID column of this entity
     * 
     * @return String
     */
    public function get_id_property()
    {
        return Group::PROPERTY_ID;
    }

    /**
     * Returns the property for the PARENT column of this entity
     * 
     * @return String
     */
    public function get_parent_property()
    {
        return Group::PROPERTY_PARENT_ID;
    }

    /**
     * Returns the property for the TITLE column of this entity
     * 
     * @return String
     */
    public function get_title_property()
    {
        return Group::PROPERTY_NAME;
    }

    /**
     * Returns the root ids for this entity
     * 
     * @return Array<int>
     */
    public function get_root_ids()
    {
        return array(\Chamilo\Core\Group\Storage\DataManager::get_root_group()->get_id());
    }

    /**
     * Returns the properties on which can be searched
     * 
     * @return Array
     */
    public function get_search_properties()
    {
        return array(Group::PROPERTY_NAME, Group::PROPERTY_CODE);
    }

    /**
     * Returns the xml feed which will be used for the element finder and for the ajax tree menu
     */
    public function get_xml_feed()
    {
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Group\Ajax\Manager::package(), 
                \Chamilo\Core\Group\Ajax\Manager::PARAM_ACTION => 'xml_group_menu_feed'));
        
        return $redirect->getUrl();
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
     * Retrieves the type for the advanced element finder for the simple rights editor
     */
    public function get_element_finder_type()
    {
        return new AdvancedElementFinderElementType(
            'platform_groups', 
            Translation::get('PlatformGroups'), 
            Manager::context() . '\Ajax', 
            'platform_group_entity_feed', 
            array());
    }

    /**
     * Retrieves an element for the advanced element finder for the simple rights editor with the given id
     */
    public function get_element_finder_element($id)
    {
        $group = \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(Group::class_name(), $id);
        if (! $group)
        {
            return null;
        }
        
        $description = strip_tags($group->get_fully_qualified_name() . ' [' . $group->get_code() . ']');
        
        return new AdvancedElementFinderElement(
            static::ENTITY_TYPE . '_' . $id, 
            'type type_group', 
            $group->get_name(), 
            $description);
    }

    /**
     * Returns the class name of the data class that is used for this entity
     * 
     * @return string
     */
    public static function data_class_class_name()
    {
        return Group::class_name();
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
