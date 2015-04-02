<?php
namespace Chamilo\Core\Metadata\Element\Entity;

use Chamilo\Core\Metadata\Element\Manager;
use Chamilo\Core\Metadata\Element\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Element\Storage\DataManager;
use Chamilo\Core\Rights\Entity\RightsEntity;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Class that describes the users for the rights editor
 *
 * @author Sven Vanpoucke
 */
class ElementEntity implements RightsEntity
{
    const ENTITY_NAME = 'element';
    const ENTITY_TYPE = 1;

    private $element_cache;

    private static $instance;

    public static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new static();
        }

        return self :: $instance;
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
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);

        return DataManager :: retrieves(Element :: class_name(), $parameters);
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
        if (is_null($this->element_cache[$user_id]))
        {

            $this->element_cache[$user_id] = array($user_id);
        }

        return $this->element_cache[$user_id];
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

        return DataManager :: count(Element :: class_name(), $condition);
    }

    /**
     * Returns the name of this entity
     *
     * @return String
     */
    public function get_entity_name()
    {
        return self :: ENTITY_NAME;
    }

    /**
     * Returns the translated name of this entiry for displaying purposes only!
     *
     * @return String Translated name of the entity
     */
    public function get_entity_translated_name()
    {
        return Translation :: get(
            (string) StringUtilities :: getInstance()->createString(self :: ENTITY_NAME)->upperCamelize());
    }

    /**
     * Returns the type of this entity
     *
     * @return int
     */
    public function get_entity_type()
    {
        return self :: ENTITY_TYPE;
    }

    /**
     * Returns the path to the icon of the entity
     *
     * @return String
     */
    public function get_entity_icon()
    {
        return Theme :: getInstance()->getImagePath('Chamilo\Core\Metadata\Element', 'Logo/16');
    }

    /**
     * Returns the properties on which can be searched
     *
     * @return Array
     */
    public function get_search_properties()
    {
        return array(Element :: PROPERTY_NAME);
    }

    /**
     * Function that can be filled in extensions of this class to limit the users
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
            'elements',
            Translation :: get('Elements'),
            Manager :: context(),
            'element_entity_feed',
            array());
    }

    /**
     * Retrieves the type for the advanced element finder for the simple rights editor
     */
    public function get_element_finder_element($id)
    {
        $element = DataManager :: retrieve_by_id(Element :: class_name(), (int) $id);

        if (! $element)
        {
            return null;
        }

        return new AdvancedElementFinderElement(
            self :: ENTITY_TYPE . '_' . $id,
            'type type_element',
            $element->get_namespace() . ':' . $element->get_name(),
            $element->get_namespace() . ':' . $element->get_name());
    }

    /**
     * Returns the class name of the data class that is used for this entity
     *
     * @return string
     */
    public static function data_class_class_name()
    {
        return Element :: class_name();
    }
}
