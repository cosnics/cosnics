<?php
namespace Chamilo\Libraries\Format\Table\Extension\GalleryTable\Property;

use Chamilo\Libraries\Translation\Translation;

/**
 * This class represents a data class property for the gallery table
 *
 * @package Chamilo\Libraries\Format\Table\Extension\GalleryTable\Property
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DataClassGalleryTableProperty extends GalleryTableProperty
{

    /**
     * The class name of the dataclass
     *
     * @var string
     */
    private $className;

    /**
     *
     * @param string $className - The class name
     * @param string $property - The property or the property alias
     * @param string $title - [OPTIONAL] default translated title from property
     */
    public function __construct($className, $property, $title = null)
    {
        $this->className = $className;

        $context = $className::context();

        if (!$title)
        {
            $title = Translation::get($property, null, $context);
        }

        parent::__construct($property, $title);
    }

    /**
     * Returns the class name
     *
     * @return string
     */
    public function get_class_name()
    {
        return $this->className;
    }

    /**
     * Sets the class name
     *
     * @param string $className
     */
    public function set_class_name($className)
    {
        $this->className = $className;
    }
}
