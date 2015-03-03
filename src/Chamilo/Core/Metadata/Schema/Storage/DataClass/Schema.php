<?php
namespace Chamilo\Core\Metadata\Schema\Storage\DataClass;

use Chamilo\Core\Metadata\Attribute\Storage\DataClass\Attribute;
use Chamilo\Core\Metadata\Element\Storage\DataClass\Element;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class describes a metadata schema
 * 
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class Schema extends DataClass
{
    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_NAMESPACE = 'namespace';
    const PROPERTY_NAME = 'name';
    const PROPERTY_URL = 'url';
    const PROPERTY_FIXED = 'fixed';

    /**
     * ***************************************************************************************************************
     * Variables *
     * **************************************************************************************************************
     */
    
    /**
     * An array of elements that belong to this schema
     * 
     * @var Element[]
     */
    private $elements;

    /**
     * An array of attributes that belong to this schema
     * 
     * @var Attribute[]
     */
    private $attributes;

    /**
     * **************************************************************************************************************
     * Extended functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Get the default properties
     * 
     * @param array $extended_property_names
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_NAMESPACE;
        $extended_property_names[] = self :: PROPERTY_NAME;
        $extended_property_names[] = self :: PROPERTY_URL;
        $extended_property_names[] = self :: PROPERTY_FIXED;
        
        return parent :: get_default_property_names($extended_property_names);
    }
    
    /*
     * Creates this object in the database
     */
    public function create()
    {
        if (! parent :: create())
        {
            return false;
        }
        
        return true;
    }

    /**
     * Returns the dependencies for this dataclass
     * 
     * @return string[string]
     */
    protected function get_dependencies()
    {
        return array(
            Element :: class_name() => new EqualityCondition(
                new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_SCHEMA_ID), 
                new StaticConditionVariable($this->get_id())), 
            Attribute :: class_name() => new EqualityCondition(
                new PropertyConditionVariable(Attribute :: class_name(), Attribute :: PROPERTY_SCHEMA_ID), 
                new StaticConditionVariable($this->get_id())));
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the namespace
     * 
     * @return string
     */
    public function get_namespace()
    {
        return $this->get_default_property(self :: PROPERTY_NAMESPACE);
    }

    /**
     * Sets the namespace
     * 
     * @param string $namespace
     */
    public function set_namespace($namespace)
    {
        $this->set_default_property(self :: PROPERTY_NAMESPACE, $namespace);
    }

    /**
     * Returns the name
     * 
     * @return string
     */
    public function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name
     * 
     * @param string $name
     */
    public function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the url
     * 
     * @return string
     */
    public function get_url()
    {
        return $this->get_default_property(self :: PROPERTY_URL);
    }

    /**
     * Sets the url
     * 
     * @param string $url
     */
    public function set_url($url)
    {
        $this->set_default_property(self :: PROPERTY_URL, $url);
    }

    /**
     * Returns whether or not this element is fixed
     * 
     * @return string
     */
    public function is_fixed()
    {
        return $this->get_default_property(self :: PROPERTY_FIXED);
    }

    /**
     * Sets whether or not the element is fixed
     * 
     * @param string $fixed
     */
    public function set_fixed($fixed)
    {
        $this->set_default_property(self :: PROPERTY_FIXED, $fixed);
    }

    /**
     * Sets the elements that belong to this schema
     * 
     * @param \Chamilo\Core\Metadata\element\storage\data_class\Element[] $elements
     */
    public function set_elements($elements)
    {
        $this->elements = $elements;
    }

    /**
     * Returns the elements that belong to this schema
     * 
     * @return \Chamilo\Core\Metadata\element\storage\data_class\Element[]
     */
    public function get_elements()
    {
        return $this->elements;
    }

    /**
     * Sets the attributes that belong to this schema
     * 
     * @param \Chamilo\Core\Metadata\attribute\storage\data_class\Attribute[] $attributes
     */
    public function set_attributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Returns the attributes that belong to this schema
     * 
     * @return \Chamilo\Core\Metadata\attribute\storage\data_class\Attribute[]
     */
    public function get_attributes()
    {
        return $this->attributes;
    }
}