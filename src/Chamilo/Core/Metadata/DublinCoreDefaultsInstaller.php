<?php
namespace Chamilo\Core\Metadata;

use Chamilo\Configuration\Package\Action;
use Chamilo\Core\Metadata\Attribute\Storage\DataClass\Attribute;
use Chamilo\Core\Metadata\Element\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Element\Storage\DataClass\ElementRelAttribute;
use Chamilo\Core\Metadata\Schema\Storage\DataClass\Schema;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Cache\DataClassCache;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Installs the default values for the dublin core schema Class DublinCoreDefaultsInstaller
 * 
 * @package core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DublinCoreDefaultsInstaller
{

    /**
     * The Action that installs the dublin core defaults
     * 
     * @var \common\libraries\package\Action
     */
    private $action;

    /**
     * List of created schemas by namespace
     * 
     * @var Schema[string]
     */
    private $created_schemas;

    /**
     * List of created elements by element name
     * 
     * @var Element[string]
     */
    private $created_elements;

    /**
     * List of created attributes by attribute name
     * 
     * @var Attribute[string]
     */
    private $created_attributes;

    /**
     * Constructor
     * 
     * @param \common\libraries\package\Action $action
     */
    public function __construct(Action $action = null)
    {
        $this->action = $action;
    }

    /**
     * Installs the dublin core in the system
     */
    public function install_dublin_core()
    {
        return $this->install_schemas() && $this->install_dublin_core_elements() &&
             $this->install_dublin_core_attributes() && $this->install_dublin_core_element_rel_attributes();
    }

    /**
     * Installs the fixed schemas
     * 
     * @return bool
     */
    protected function install_schemas()
    {
        $schemas = array();
        
        $schemas[] = array('name' => 'Dublin Core', 'namespace' => 'dc', 'url' => 'http://purl.org/dc/elements/1.1/');
        
        $schemas[] = array('name' => 'XML', 'namespace' => 'xml', 'url' => 'http://www.w3.org/2001/XMLSchema');
        
        DataClassCache :: reset();
        
        foreach ($schemas as $schema_array)
        {
            try
            {
                $schema = \Chamilo\Core\Metadata\Schema\Storage\DataManager :: retrieve_schema_by_namespace(
                    $schema_array['namespace']);
            }
            catch (\Exception $ex)
            {
                $schema = null;
            }
            
            if (! $schema)
            {
                $schema = new Schema();
                $schema->set_default_properties($schema_array);
            }
            
            $schema->set_fixed(true);
            
            $succes = $schema->is_identified() ? $schema->update() : $schema->create();
            
            if (! $succes)
            {
                return $this->failed(Translation :: get('SchemaNotCreated', array('SCHEMA' => $schema_array['name'])));
            }
            
            $this->add_message(
                Action :: TYPE_NORMAL, 
                Translation :: get('SchemaCreated', array('SCHEMA' => $schema_array['name'])));
            
            $this->created_schemas[$schema->get_namespace()] = $schema;
        }
        
        return true;
    }

    /**
     * Installs the dublin core elements
     * 
     * @return bool
     */
    protected function install_dublin_core_elements()
    {
        $elements = array(
            'contributor', 
            'coverage', 
            'creator', 
            'date', 
            'description', 
            'format', 
            'identifier', 
            'language', 
            'publisher', 
            'relation', 
            'rights', 
            'source', 
            'subject', 
            'title', 
            'type');
        
        $schema_id = $this->created_schemas['dc']->get_id();
        
        foreach ($elements as $element_name)
        {
            $element = \Chamilo\Core\Metadata\Storage\DataManager :: retrieve_element_by_schema_id_and_name(
                $schema_id, 
                $element_name);
            
            if (! $element)
            {
                $element = new Element();
                $element->set_name($element_name);
                $element->set_schema_id($schema_id);
            }
            
            $element->set_fixed(1);
            $element->set_display_name(
                Translation :: get(
                    (string) StringUtilities :: getInstance()->createString($element_name)->upperCamelize()));
            
            $succes = $element->is_identified() ? $element->update() : $element->create();
            DataClassCache :: reset();
            
            if ($succes)
            {
                $this->add_message(
                    Action :: TYPE_NORMAL, 
                    Translation :: get('ElementCreated', array('ELEMENT' => $element_name)));
                
                $this->created_elements[$element_name] = $element;
            }
            else
            {
                return $this->failed(Translation :: get('ElementNotCreated', array('ELEMENT' => $element_name)));
            }
        }
        
        return true;
    }

    /**
     * Installs the dublin core attributes
     * 
     * @return bool
     */
    protected function install_dublin_core_attributes()
    {
        $schema_id = $this->created_schemas['xml']->get_id();
        
        $attributes = array('lang');
        
        foreach ($attributes as $attribute_name)
        {
            $attribute = \Chamilo\Core\Metadata\Storage\DataManager :: retrieve_attribute_by_schema_id_and_name(
                $schema_id, 
                $attribute_name);
            
            if (! $attribute)
            {
                $attribute = new Attribute();
                $attribute->set_schema_id($schema_id);
                $attribute->set_name($attribute_name);
            }
            
            $attribute->set_fixed(1);
            $attribute->set_display_name(
                Translation :: get(
                    (string) StringUtilities :: getInstance()->createString($attribute_name)->upperCamelize()));
            
            $succes = $attribute->is_identified() ? $attribute->update() : $attribute->create();
            DataClassCache :: reset();
            
            if ($succes)
            {
                $this->add_message(
                    Action :: TYPE_NORMAL, 
                    Translation :: get('AttributeCreated', array('ATTRIBUTE' => $attribute_name)));
                
                $this->created_attributes[$attribute_name] = $attribute;
            }
            else
            {
                return $this->failed(Translation :: get('AttributeNotCreated', array('ATTRIBUTE' => $attribute_name)));
            }
        }
        
        return true;
    }

    /**
     * Installs the dublin core element attribute relations
     * 
     * @return bool
     */
    protected function install_dublin_core_element_rel_attributes()
    {
        foreach ($this->created_attributes as $attribute)
        {
            foreach ($this->created_elements as $element)
            {
                $element_rel_attribute = \Chamilo\Core\Metadata\Element\Storage\DataManager :: retrieve_element_rel_attribute_by_element_and_attribute(
                    $element->get_id(), 
                    $attribute->get_id());
                
                if (! $element_rel_attribute)
                {
                    $element_rel_attribute = new ElementRelAttribute();
                    
                    $element_rel_attribute->set_attribute_id($attribute->get_id());
                    $element_rel_attribute->set_element_id($element->get_id());
                }
                
                $succes = $element_rel_attribute->is_identified() ? true : $element_rel_attribute->create();
                DataClassCache :: reset();
                
                if (! $succes)
                {
                    return $this->failed(
                        Translation :: get(
                            'ElementRelAttributeNotCreated', 
                            array('ATTRIBUTE' => $attribute->get_name(), 'ELEMENT' => $element->get_name())));
                }
            }
        }
        
        return true;
    }

    /**
     * Delegation method to the action (if any)
     * 
     * @param string $error_message
     *
     * @return bool
     */
    protected function failed($error_message)
    {
        if ($this->action)
        {
            return $this->action->failed($error_message);
        }
        
        return false;
    }

    /**
     * Delegation method to the action (if any)
     * 
     * @param string $type
     * @param string $message
     */
    protected function add_message($type = Action::TYPE_NORMAL, $message)
    {
        if ($this->action)
        {
            $this->action->add_message($type, $message);
        }
    }
}