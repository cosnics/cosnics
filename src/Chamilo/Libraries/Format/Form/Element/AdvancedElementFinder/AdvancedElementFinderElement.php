<?php
namespace Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder;

/**
 * Defines an element for an advanced element finder
 * When the element has children it becomes a category
 * 
 * @author Sven Vanpoucke
 */
class AdvancedElementFinderElement
{
    const PROPERTY_ID = 'id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_CLASS = 'classes';
    const PROPERTY_CHILDREN = 'children';
    const PROPERTY_TYPE = 'type';
    const TYPE_SELECTABLE = 1;
    const TYPE_SELECTABLE_AND_FILTER = 2;
    const TYPE_FILTER = 3;

    /**
     * Associative array for the properties
     * 
     * @var Array
     */
    private $properties;

    public function __construct($id, $class, $title, $description, $type = self :: TYPE_SELECTABLE)
    {
        $this->set_id($id);
        $this->set_class($class);
        $this->set_title($title);
        $this->set_description($description);
        $this->set_type($type);
    }

    /**
     * Sets a property in the associative array of properties
     * 
     * @param String $property_name
     * @param Object $value
     */
    public function set_property($property_name, $value)
    {
        $this->properties[$property_name] = $value;
    }

    /**
     * Retrieves a property from the associative array of properties
     * 
     * @param String $property_name
     */
    public function get_property($property_name)
    {
        return $this->properties[$property_name];
    }

    /**
     * Sets the id of this element
     * 
     * @param int $id
     */
    public function set_id($id)
    {
        $this->set_property(self :: PROPERTY_ID, $id);
    }

    /**
     * Returns the id of this element
     * 
     * @return int
     */
    public function get_id()
    {
        return $this->get_property(self :: PROPERTY_ID);
    }

    /**
     * Sets the title of this element
     * 
     * @param String title
     */
    public function set_title($title)
    {
        $this->set_property(self :: PROPERTY_TITLE, $title);
    }

    /**
     * Sets the description of this element
     * 
     * @param String description
     */
    public function set_description($description)
    {
        $this->set_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Sets the class of this element
     * 
     * @param String class
     */
    public function set_class($class)
    {
        $this->set_property(self :: PROPERTY_CLASS, $class);
    }

    /**
     * Sets the type of this element
     * 
     * @param String type
     */
    public function set_type($type)
    {
        $this->set_property(self :: PROPERTY_TYPE, $type);
    }

    /**
     * Sets the children of this element
     * 
     * @param Array $children
     */
    public function set_children($children)
    {
        $this->set_property(self :: PROPERTY_CHILDREN, $children);
    }

    /**
     * Retrieves the children of this element
     * 
     * @return Array
     */
    public function get_children()
    {
        return $this->get_property(self :: PROPERTY_CHILDREN);
    }

    /**
     * Adds a child to this element
     * 
     * @param AdvancedElementFinderElement $child
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
     * @return Array
     */
    public function as_array()
    {
        $array = $this->properties;
        $array[self :: PROPERTY_CHILDREN] = array();
        
        $children = $this->get_children();
        
        foreach ($children as $child)
        {
            $array[self :: PROPERTY_CHILDREN][] = $child->as_array();
        }
        
        return $array;
    }
}
