<?php
namespace Chamilo\Core\Metadata;

use Chamilo\Core\Metadata\Element\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Element\Storage\DataClass\ElementRelAttribute;
use Chamilo\Core\Metadata\Storage\DataManager;
use Chamilo\Core\Metadata\Value\Element\Storage\DataClass\DefaultElementValue;
use Chamilo\Core\Metadata\Value\Storage\DataClass\ElementValue;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

/**
 * A metadata manager
 *
 * @author Jens Vanderheyden
 */
abstract class Manager extends Application
{
    const APPLICATION_NAME = 'metadata';
    const SETTING_DEFAULT_ELEMENT = 'default_element';
    const SETTING_DEFAULT_NAMESPACE = 'default_namespace';
    const SETTING_DEFAULT_VALUE = 'default_value';
    const PARAM_CONTENT_OBJECT = 'content_object';
    const PARAM_USER = 'user';
    const PARAM_GROUP = 'group';
    const PARAM_METADATA_ATTRIBUTE_ASSOCIATION = 'metadata_attribute_association';
    const PARAM_METADATA_DEFAULT_VALUE = 'metadata_default_value';
    const PARAM_METADATA_ELEMENT_NESTING = 'metadata_element_nesting';
    const PARAM_METADATA_NAMESPACE = 'metadata_namespace';
    const PARAM_CONTENT_OBJECT_PROPERTY_METADATA = 'content_object_property_metadata';
    const PARAM_METADATA_ELEMENT = 'metadata_element';
    const PARAM_METADATA_PROPERTY_VALUE = 'metadata_element_value';
    const PARAM_METADATA_ATTRIBUTE = 'metadata_attribute';
    const PARAM_METADATA_ATTRIBUTE_VALUE = 'metadata_attribute_value';
    const PARAM_MOVE = 'move';
    const PARAM_CONTROLLED_VOCABULARY_ID = 'controlled_vocabulary_id';
    const ACTION_EXPORT_METADATA = 'metadata_exporter';
    const ACTION_IMPORT_METADATA = 'metadata_importer';
    const DEFAULT_ACTION = self :: ACTION_ATTRIBUTE;
    const ACTION_SCHEMA = 'schema';
    const ACTION_ELEMENT = 'element';
    const ACTION_ATTRIBUTE = 'attribute';
    const ACTION_VALUE = 'value';
    const ACTION_CONTROLLED_VOCABULARY = 'controlled_vocabulary';

    private $display_order_total = array();

    /**
     * Returns an array with all the metadata for a content-object array key =
     * metadata_property-namespace:metadata_property-name array value = metadata_property-value
     *
     * @param <int> $co_id id of the content-object
     * @return array
     */
    public static function retrieve_metadata_for_content_object($co_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ElementValue :: class_name(), ElementValue :: PROPERTY_CONTENT_OBJECT_ID),
            new StaticConditionVariable($co_id));

        $metadata_element_values = DataManager :: retrieves(ElementValue :: class_name(), $condition);

        $metadata = array();

        while ($mpv = $metadata_element_values->next_result())
        {
            $type = DataManager :: retrieve_by_id(Element :: class_name(), $mpv->get_element_id());

            $metadata[$type->get_namespace() . ':' . $type->get_name()] = $mpv->get_value();
        }

        return $metadata;
    }

    /*
     * Retrieves the allowed metadata attributes @param array conditions - equality conditons with ElementRelAttribute
     * :: PROPERTY_ELEMENT_ID @return array allowed_metadata_attributes
     */
    public function retrieve_allowed_metadata_attributes(array $conditions_allowed)
    {
        if (count($conditions_allowed))
        {
            $condition = (count($conditions_allowed) === 1) ? $conditions_allowed[0] : new OrCondition(
                $conditions_allowed);

            $allowed_metadata_attributes = DataManager :: retrieves(ElementRelAttribute :: class_name(), $condition);

            return $this->format_allowed_metadata_attributes($allowed_metadata_attributes);
        }

        return array();
    }

    /*
     * @param ArrayResultSet $allowed_metadata_attributes @return allowed_metadata_attribute_arr [parent_id][child_id]
     */
    public function format_allowed_metadata_attributes(ArrayResultSet $allowed_metadata_attributes)
    {
        $allowed_metadata_attribute_arr = array();

        while ($allowed_metadata_attribute = $allowed_metadata_attributes->next_result())
        {
            $allowed_metadata_attribute_arr[$allowed_metadata_attribute->get_parent_id()][$allowed_metadata_attribute->get_child_id()] = $allowed_metadata_attribute->get_child_id();
        }

        return $allowed_metadata_attribute_arr;
    }

    // Url Creation
    public function get_edit_associations_url($metadata_element)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_EDIT_ASSOCIATIONS,
                self :: PARAM_METADATA_ELEMENT => $metadata_element->get_id()));
    }

    public function get_update_metadata_namespace_url($metadata_namespace)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_EDIT_METADATA_NAMESPACE,
                self :: PARAM_METADATA_NAMESPACE => $metadata_namespace->get_id()));
    }

    public function get_delete_metadata_namespace_url($metadata_namespace)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_DELETE_METADATA_NAMESPACE,
                self :: PARAM_METADATA_NAMESPACE => $metadata_namespace->get_id()));
    }

    public function get_update_content_object_property_metadata_url($content_object_property_metadata)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_EDIT_CONTENT_OBJECT_PROPERTY_METADATA,
                self :: PARAM_CONTENT_OBJECT_PROPERTY_METADATA => $content_object_property_metadata->get_id()));
    }

    public function get_delete_content_object_property_metadata_url($content_object_property_metadata)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_DELETE_CONTENT_OBJECT_PROPERTY_METADATA,
                self :: PARAM_CONTENT_OBJECT_PROPERTY_METADATA => $content_object_property_metadata->get_id()));
    }

    public function get_update_metadata_element_url($metadata_element)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_EDIT_METADATA_ELEMENT,
                self :: PARAM_METADATA_ELEMENT => $metadata_element->get_id()));
    }

    public function get_delete_metadata_element_url($metadata_element)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_DELETE_METADATA_ELEMENT,
                self :: PARAM_METADATA_ELEMENT => $metadata_element->get_id()));
    }

    public function get_update_metadata_default_value_url($metadata_default_value)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_EDIT_METADATA_DEFAULT_VALUE,
                self :: PARAM_METADATA_DEFAULT_VALUE => $metadata_default_value->get_id()));
    }

    public function get_delete_metadata_default_value_url(DefaultElementValue $metadata_default_value)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_DELETE_METADATA_DEFAULT_VALUE,
                self :: PARAM_METADATA_DEFAULT_VALUE => $metadata_default_value->get_id()));
    }

    public function get_browse_metadata_default_values_url(Element $metadata_element)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_BROWSE_METADATA_DEFAULT_VALUES,
                Manager :: PARAM_METADATA_ELEMENT => $metadata_element->get_id()));
    }

    public function get_edit_content_object_metadata_element_values_url($content_object)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_EDIT_CONTENT_OBJECT_METADATA,
                self :: PARAM_CONTENT_OBJECT => $content_object->get_id()));
    }

    public function get_edit_user_metadata_element_values_url($user)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_EDIT_USER_METADATA, self :: PARAM_USER => $user->get_id()));
    }

    public function get_edit_group_metadata_element_values_url($group)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_EDIT_GROUP_METADATA, self :: PARAM_GROUP => $group->get_id()));
    }

    public function get_update_metadata_attribute_url($metadata_attribute)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_EDIT_METADATA_ATTRIBUTE,
                self :: PARAM_METADATA_ATTRIBUTE => $metadata_attribute->get_id()));
    }

    public function get_delete_metadata_attribute_url($metadata_attribute)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_DELETE_METADATA_ATTRIBUTE,
                self :: PARAM_METADATA_ATTRIBUTE => $metadata_attribute->get_id()));
    }

    public function get_move_metadata_element_url($metadata_element, $move)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_MOVE_METADATA_ELEMENT,
                self :: PARAM_MOVE => $move,
                self :: PARAM_METADATA_ELEMENT => $metadata_element->get_id()));
    }

    public function get_display_order_total($namespace)
    {
        if (! isset($this->display_order_total[$namespace]))
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_SCHEMA_ID),
                new StaticConditionVariable($namespace));

            $this->display_order_total[$namespace] = DataManager :: count(Element :: class_name(), $condition);
        }

        return $this->display_order_total[$namespace];
    }

    /**
     * Returns the url for the controlled vocabulary updater
     *
     * @param int $controlled_vocabulary_id
     *
     * @return string
     */
    public function get_update_controlled_vocabulary_url($controlled_vocabulary_id)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_UPDATE_CONTROLLED_VOCABULARY,
                self :: PARAM_CONTROLLED_VOCABULARY_ID => $controlled_vocabulary_id));
    }

    /**
     * Returns the url for the controlled vocabulary deleter
     *
     * @param int $controlled_vocabulary_id
     *
     * @return string
     */
    public function get_delete_controlled_vocabulary_url($controlled_vocabulary_id)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_DELETE_CONTROLLED_VOCABULARY,
                self :: PARAM_CONTROLLED_VOCABULARY_ID => $controlled_vocabulary_id));
    }

    /**
     * Returns the url to manage the controlled vocabulary for a given element
     *
     * @param int $element_id
     *
     * @return string
     */
    public function get_manage_element_controlled_vocabulary_url($element_id)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_MANAGE_ELEMENT_CONTROLLED_VOCABULARY,
                self :: PARAM_METADATA_ELEMENT => $element_id));
    }

    /**
     * Returns the url to manage the controlled vocabulary for a given attribute
     *
     * @param int $attribute_id
     *
     * @return string
     */
    public function get_manage_attribute_controlled_vocabulary_url($attribute_id)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_MANAGE_ATTRIBUTE_CONTROLLED_VOCABULARY,
                self :: PARAM_METADATA_ATTRIBUTE => $attribute_id));
    }

    /**
     * Returns the admin breadcrumb generator
     *
     * @return \libraries\format\BreadcrumbGeneratorInterface
     */
    public function get_breadcrumb_generator()
    {
        return new \Chamilo\Core\Admin\Core\BreadcrumbGenerator($this, BreadcrumbTrail :: get_instance());
    }
}
