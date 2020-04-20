<?php
namespace Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder;

/**
 * Defines an element for an advanced element finder
 * When the element has children it becomes a category
 *
 * @package Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder
 * @author Sven Vanpoucke
 */
class AdvancedElementFinderElement
{
    const PROPERTY_CHILDREN = 'children';
    const PROPERTY_CLASS = 'classes';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_ID = 'id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_TYPE = 'type';

    const TYPE_FILTER = 3;
    const TYPE_SELECTABLE = 1;
    const TYPE_SELECTABLE_AND_FILTER = 2;
    const TYPE_VISUAL = 4;

    /**
     * Associative array for the properties
     *
     * @var string[]
     */
    private $properties;

    /**
     *
     * @param string $id
     * @param string $class
     * @param string $title
     * @param string $description
     * @param integer $type
     */
    public function __construct($id, $class, $title, $description, $type = self::TYPE_SELECTABLE)
    {
        $this->set_id($id);
        $this->set_class($class);
        $this->set_title($title);
        $this->set_description($description);
        $this->set_type($type);
    }

    /**
     * Adds a child to this element
     *
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement $child
     */
    public function add_child(AdvancedElementFinderElement $child)
    {
        $children = $this->get_children();

        $children[] = $child;

        $this->set_children($children);
    }

    /**
     * Returns this element as an array
     *
     * @return string[]
     */
    public function as_array()
    {
        $array = $this->properties;
        $array[self::PROPERTY_CHILDREN] = array();

        $children = $this->get_children();

        foreach ($children as $child)
        {
            $array[self::PROPERTY_CHILDREN][] = $child->as_array();
        }

        return $array;
    }

    /**
     * Retrieves the children of this element
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement[]
     */
    public function get_children()
    {
        return $this->get_property(self::PROPERTY_CHILDREN);
    }

    /**
     * Returns the id of this element
     *
     * @return string
     */
    public function get_id()
    {
        return $this->get_property(self::PROPERTY_ID);
    }

    /**
     * Retrieves a property from the associative array of properties
     *
     * @param string $propertyName
     *
     * @return mixed
     */
    public function get_property($propertyName)
    {
        return $this->properties[$propertyName];
    }

    public function hasChildren()
    {
        return count($this->get_children()) > 0;
    }

    /**
     * Sets the children of this element
     *
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement[]
     */
    public function set_children($children)
    {
        $this->set_property(self::PROPERTY_CHILDREN, $children);
    }

    /**
     * Sets the class of this element
     *
     * @param string $class
     */
    public function set_class($class)
    {
        $this->set_property(self::PROPERTY_CLASS, $class);
    }

    /**
     * Sets the description of this element
     *
     * @param string $description
     */
    public function set_description($description)
    {
        $this->set_property(self::PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Sets the id of this element
     *
     * @param string $id
     */
    public function set_id($id)
    {
        $this->set_property(self::PROPERTY_ID, $id);
    }

    /**
     * Sets a property in the associative array of properties
     *
     * @param string $propertyName
     * @param mixed $value
     */
    public function set_property($propertyName, $value)
    {
        $this->properties[$propertyName] = $value;
    }

    /**
     * Sets the title of this element
     *
     * @param string $title
     */
    public function set_title($title)
    {
        $this->set_property(self::PROPERTY_TITLE, $title);
    }

    /**
     * Sets the type of this element
     *
     * @param string $type
     */
    public function set_type($type)
    {
        $this->set_property(self::PROPERTY_TYPE, $type);
    }
}
