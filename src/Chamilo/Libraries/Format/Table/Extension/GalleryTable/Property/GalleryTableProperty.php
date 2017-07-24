<?php
namespace Chamilo\Libraries\Format\Table\Extension\GalleryTable\Property;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Describes a property for the gallery table
 *
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring from old gallery table property
 */
class GalleryTableProperty
{

    /**
     * The property of the object which will be displayed in this column.
     *
     * @var string
     */
    private $property;

    /**
     * The title of the column.
     *
     * @var string
     */
    private $title;

    /**
     * Constructor
     *
     * @param string $property
     * @param string $title
     * @param string $storage_unit_alias
     */
    public function __construct($property, $title = null)
    {
        $this->property = $property;

        if (is_null($title))
        {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $called_class = $backtrace[1]['class'];
            $context = ClassnameUtilities::getInstance()->getNamespaceFromClassname($called_class);

            $title = Translation::get(
                (string) StringUtilities::getInstance()->createString($property)->upperCamelize(),
                array(),
                $context);
        }

        $this->set_title($title);
    }

    /**
     * Returns the property
     *
     * @return string
     */
    public function get_property()
    {
        return $this->property;
    }

    /**
     * Sets the property
     *
     * @param string $property
     */
    public function set_property($property)
    {
        $this->property = $property;
    }

    /**
     * Gets the title of this column.
     *
     * @return string
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * Sets the title of this column.
     *
     * @param string $title
     */
    public function set_title($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the name of this property
     *
     * @return string
     */
    public function get_name()
    {
        return $this->property;
    }
}
