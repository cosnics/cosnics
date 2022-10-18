<?php
namespace Chamilo\Libraries\Format\Table\Column;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * This class represents a column for a table that is based on a property from the data class
 *
 * @package Chamilo\Libraries\Format\Table\Column
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class DataClassPropertyTableColumn extends AbstractSortableTableColumn
{

    /**
     * The class name of the dataclass
     *
     * @var string
     */
    private $className;

    /**
     * @param string $className - The class name
     * @param string $property  - The property or the property alias
     * @param string $title     - [OPTIONAL] default translated title from property
     * @param bool $sortable    - Whether or not the column is sortable
     * @param string $headerCssClasses
     * @param string $contentCssClasses
     */
    public function __construct(
        $className, $property, $title = null, $sortable = true, $headerCssClasses = null, $contentCssClasses = null
    )
    {
        $this->className = $className;

        $context = $className::context();

        if (!$title)
        {
            $title = Translation::get(
                (string) StringUtilities::getInstance()->createString($property)->upperCamelize(), null, $context
            );
        }

        parent::__construct($property, $title, $sortable, $headerCssClasses, $contentCssClasses);
    }

    public function getConditionVariable(): ConditionVariable
    {
        return new PropertyConditionVariable($this->get_class_name(), $this->get_name());
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
