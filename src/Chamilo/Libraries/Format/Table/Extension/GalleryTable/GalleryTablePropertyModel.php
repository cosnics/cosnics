<?php
namespace Chamilo\Libraries\Format\Table\Extension\GalleryTable;

use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Property\DataClassGalleryTableProperty;
use Chamilo\Libraries\Format\Table\TableComponent;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * This class represents a property model for a gallery table Refactoring from ObjectTable to split between a table
 * based on a record and based on an object
 *
 * @package Chamilo\Libraries\Format\Table\Extension\GalleryTable
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class GalleryTablePropertyModel extends TableComponent
{
    const DEFAULT_ORDER_PROPERTY_DIRECTION = SORT_ASC;
    const DEFAULT_ORDER_PROPERTY_INDEX = 0;

    /**
     * The direction in which the table is currently sorted.
     *
     * @var integer
     */
    private $orderDirection;

    /**
     * The property by which the table is currently sorted.
     *
     * @var integer
     */
    private $orderProperty;

    /**
     * The properties in the table.
     *
     * @var \Chamilo\Libraries\Format\Table\Extension\GalleryTable\Property\DataClassGalleryTableProperty[]
     */
    private $properties;

    /**
     *
     * @param \Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTable $table
     */
    public function __construct($table)
    {
        parent::__construct($table);

        $this->initialize_properties();

        $this->set_default_order_property(static::DEFAULT_ORDER_PROPERTY_INDEX);
        $this->set_default_order_direction(static::DEFAULT_ORDER_PROPERTY_DIRECTION);
    }

    /**
     * Adds the given property at the end of the table.
     *
     * @param \Chamilo\Libraries\Format\Table\Extension\GalleryTable\Property\DataClassGalleryTableProperty $property
     */
    public function add_property($property)
    {
        $this->properties[] = $property;
    }

    /**
     * Delete a property at a given index
     *
     * @param integer $propertyIndex
     */
    public function delete_property($propertyIndex)
    {
        unset($this->properties[$propertyIndex]);

        $this->properties = array_values($this->properties);
    }

    /**
     * Returns the index of the default property to order objects by
     *
     * @return integer
     */
    public function getDefaultOrderBy()
    {
        return $this->orderProperty;
    }

    /**
     * Gets the default order direction.
     *
     * @return integer
     */
    public function get_default_order_direction()
    {
        return $this->orderDirection;
    }

    /**
     * Returns an \Chamilo\Libraries\Storage\Query\OrderBy with the given order column index.
     * If the selected index is not sortable, the default
     * column index will be used. If the default column is not sortable then no order will be given
     *
     * @param integer $propertyNumber
     * @param integer $orderDirection
     *
     * @return \Chamilo\Libraries\Storage\Query\OrderProperty
     */
    public function get_order_property($propertyNumber, $orderDirection)
    {
        $property = $this->get_property($propertyNumber);

        if ($property instanceof DataClassGalleryTableProperty)
        {
            return
                new OrderProperty(
                    new PropertyConditionVariable($property->get_class_name(), $property->get_property()),
                    $orderDirection

            );
        }
        else
        {
            $defaultProperty = $this->get_property($this->getDefaultOrderBy());

            if ($propertyNumber != $defaultProperty)
            {
                return $this->get_order_property($defaultProperty, $orderDirection);
            }
        }

        return null;
    }

    /**
     * Returns the properties
     *
     * @return \Chamilo\Libraries\Format\Table\Extension\GalleryTable\Property\DataClassGalleryTableProperty[]
     */
    public function get_properties()
    {
        return $this->properties;
    }

    /**
     * Sets the properties
     *
     * @param \Chamilo\Libraries\Format\Table\Extension\GalleryTable\Property\DataClassGalleryTableProperty[] $properties
     */
    public function set_properties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * Gets the property at the given index in the model.
     *
     * @param integer $index
     *
     * @return \Chamilo\Libraries\Format\Table\Extension\GalleryTable\Property\DataClassGalleryTableProperty
     */
    public function get_property($index)
    {
        return $this->properties[$index];
    }

    /**
     * Returns the number of properties in the model.
     *
     * @return integer
     */
    public function get_property_count()
    {
        return count($this->properties);
    }

    /**
     * Initializes the properties for the table
     */
    abstract public function initialize_properties();

    /**
     * Sets the default order direction.
     *
     * @param integer $direction
     */
    public function set_default_order_direction($direction)
    {
        $this->orderDirection = $direction;
    }

    /**
     * Sets the index of the default property to order objects by
     *
     * @param integer $propertyIndex
     */
    public function set_default_order_property($propertyIndex)
    {
        $this->orderProperty = $propertyIndex;
    }
}
