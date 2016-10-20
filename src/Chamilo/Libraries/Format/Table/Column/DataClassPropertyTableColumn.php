<?php
namespace Chamilo\Libraries\Format\Table\Column;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * This class represents a column for a table that is based on a property from the data class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DataClassPropertyTableColumn extends TableColumn
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
     * @param bool $sortable - Whether or not the column is sortable
     * @param string $headerCssClasses
     * @param string $contentCssClasses
     */
    public function __construct(
        $class_name, $property, $title = null, $sortable = true, $headerCssClasses = null, $contentCssClasses = null
    )
    {
        $this->class_name = $class_name;

        $context = $class_name:: context();

        if (!$title)
        {
            $title = Translation:: get(
                (string) StringUtilities:: getInstance()->createString($property)->upperCamelize(),
                null,
                $context
            );
        }

        parent:: __construct($property, $title, $sortable, $headerCssClasses, $contentCssClasses);
    }

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

    public function getConditionVariable()
    {
        return new PropertyConditionVariable($this->get_class_name(), $this->get_name());
    }
}
