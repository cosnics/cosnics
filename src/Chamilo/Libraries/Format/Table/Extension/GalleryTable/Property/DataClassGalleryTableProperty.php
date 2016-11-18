<?php
namespace Chamilo\Libraries\Format\Table\Extension\GalleryTable\Property;

use Chamilo\Libraries\Platform\Translation;

/**
 * This class represents a data class property for the gallery table
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DataClassGalleryTableProperty extends GalleryTableProperty
{

    /**
     * The class name of the dataclass
     * 
     * @var string
     */
    private $class_name;

    /**
     * **************************************************************************************************************
     * Constructor *
     * **************************************************************************************************************
     */
    
    /**
     * Constructor
     * 
     * @param string $class_name - The class name
     * @param string $property - The property or the property alias
     * @param string $title - [OPTIONAL] default translated title from property
     */
    public function __construct($class_name, $property, $title = null)
    {
        $this->class_name = $class_name;
        
        $context = $class_name::context();
        
        if (! $title)
        {
            $title = Translation::get($property, null, $context);
        }
        
        parent::__construct($property, $title);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the class name
     * 
     * @return string
     */
    public function get_class_name()
    {
        return $this->class_name;
    }

    /**
     * Sets the class name
     * 
     * @param string $class_name
     */
    public function set_class_name($class_name)
    {
        $this->class_name = $class_name;
    }
}
