<?php
namespace Chamilo\Libraries\Format\Table\Extension\GalleryTable;

use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Property\DataClassGalleryTableProperty;
use Chamilo\Libraries\Format\Table\TableComponent;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * This class represents a property model for a gallery table Refactoring from ObjectTable to split between a table
 * based on a record and based on an object
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class GalleryTablePropertyModel extends TableComponent
{
    /**
     * **************************************************************************************************************
     * Default constants *
     * **************************************************************************************************************
     */
    const DEFAULT_ORDER_PROPERTY_INDEX = 0;
    const DEFAULT_ORDER_PROPERTY_DIRECTION = SORT_ASC;

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */

    /**
     * The object table
     *
     * @var NewObjectTable
     */
    private $object_table;

    /**
     * The properties in the table.
     *
     * @var GalleryTableProperty[]
     */
    private $properties;

    /**
     * The property by which the table is currently sorted.
     *
     * @var int
     */
    private $order_property;

    /**
     * The direction in which the table is currently sorted.
     *
     * @var int
     */
    private $order_direction;

    /**
     * **************************************************************************************************************
     * Main Functionality *
     * **************************************************************************************************************
     */

    /**
     * Constructor.
     *
     * @param GalleryTable $table
     */
    public function __construct($table)
    {
        parent::__construct($table);

        $this->initialize_properties();

        $this->set_default_order_property(static::DEFAULT_ORDER_PROPERTY_INDEX);
        $this->set_default_order_direction(static::DEFAULT_ORDER_PROPERTY_DIRECTION);
    }

    /**
     * Returns an ObjectTableOrder with the given order column index.
     * If the selected index is not sortable, the default
     * column index will be used. If the default column is not sortable then no order will be given
     *
     * @param $property_number int
     * @param $order_direction int
     *
     * @return \libraries\ObjectTableOrder null
     */
    public function get_order_property($property_number, $order_direction)
    {
        $property = $this->get_property($property_number);

        if ($property instanceof DataClassGalleryTableProperty)
        {
            return new OrderBy(
                new PropertyConditionVariable($property->get_class_name(), $property->get_property()),
                $order_direction);
        }
        else
        {
            $default_property = $this->get_property($this->get_default_order_property());

            if ($property_number != $default_property)
            {
                return $this->get_order_property($default_property, $order_direction);
            }
        }

        return null;
    }

    /**
     * **************************************************************************************************************
     * Getters and Setters *
     * **************************************************************************************************************
     */

    /**
     * Returns the properties
     *
     * @return \libraries\GalleryObjectTableProperty[]
     */
    public function get_properties()
    {
        return $this->properties;
    }

    /**
     * Sets the properties
     *
     * @param \libraries\GalleryObjectTableProperty[] $properties
     */
    public function set_properties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * Returns the index of the default property to order objects by
     *
     * @return int
     */
    public function get_default_order_property()
    {
        return $this->order_property;
    }

    /**
     * Sets the index of the default property to order objects by
     *
     * @param int $property_index
     */
    public function set_default_order_property($property_index)
    {
        $this->order_property = $property_index;
    }

    /**
     * Gets the default order direction.
     *
     * @return int - The direction. Either the PHP constant SORT_ASC or SORT_DESC.
     */
    public function get_default_order_direction()
    {
        return $this->order_direction;
    }

    /**
     * Sets the default order direction.
     *
     * @param $direction int - The direction. Either the PHP constant SORT_ASC or SORT_DESC.
     */
    public function set_default_order_direction($direction)
    {
        $this->order_direction = $direction;
    }

    /**
     * **************************************************************************************************************
     * List Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the number of properties in the model.
     *
     * @return int
     */
    public function get_property_count()
    {
        return count($this->properties);
    }

    /**
     * Gets the property at the given index in the model.
     *
     * @param $index int
     *
     * @return \libraries\format\GalleryTableProperty
     */
    public function get_property($index)
    {
        return $this->properties[$index];
    }

    /**
     * Adds the given property at the end of the table.
     *
     * @param $property \libraries\format\GalleryTableProperty
     */
    public function add_property($property)
    {
        $this->properties[] = $property;
    }

    /**
     * Delete a property at a given index
     *
     * @param $property_index int
     */
    public function delete_property($property_index)
    {
        unset($this->properties[$property_index]);

        $this->properties = array_values($this->properties);
    }

    /**
     * **************************************************************************************************************
     * Abstract Functionality *
     * **************************************************************************************************************
     */

    /**
     * Initializes the properties for the table
     */
    abstract public function initialize_properties();
}
