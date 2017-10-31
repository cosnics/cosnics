<?php
namespace Chamilo\Libraries\Format\DataTable\Column;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Format\DataTable\Column
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DataClassPropertyDataTableColumn extends DataTableColumn
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable
     */
    private $propertyConditionVariable;

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable $propertyConditionVariable
     */
    public function __construct(PropertyConditionVariable $propertyConditionVariable)
    {
        $this->propertyConditionVariable = $propertyConditionVariable;

        parent::__construct(
            $this->determineColumnName(
                $propertyConditionVariable->get_property(),
                $propertyConditionVariable->get_class()),
            $this->determineColumnTitle($propertyConditionVariable));
    }

    /**
     *
     * @param string $property
     * @param string $className
     * @return string
     */
    public function determineColumnName($property, $className = null)
    {
        if (is_null($className))
        {
            return $property;
        }

        $classNameSlug = StringUtilities::getInstance()->createString($className)->replace('\\', '_')->__toString();
        return $classNameSlug . ':' . $property;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable $propertyConditionVariable
     * @return string
     */
    public function determineColumnTitle(PropertyConditionVariable $propertyConditionVariable)
    {
        $className = $propertyConditionVariable->get_class();

        return Translation::getInstance()->getTranslation(
            StringUtilities::getInstance()->createString($propertyConditionVariable->get_property())->upperCamelize()->__toString(),
            null,
            $className::context());
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable
     */
    public function getPropertyConditionVariable()
    {
        return $this->propertyConditionVariable;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable $propertyConditionVariable
     */
    public function setPropertyConditionVariable(PropertyConditionVariable $propertyConditionVariable)
    {
        $this->propertyConditionVariable = $propertyConditionVariable;
    }
}
