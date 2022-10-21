<?php
namespace Chamilo\Libraries\Format\Table\Column;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Format\Table\Column
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DataClassPropertyTableColumn extends AbstractSortableTableColumn
{

    private string $className;

    /**
     * @param string[] $headerCssClasses
     * @param string[] $contentCssClasses
     */
    public function __construct(
        string $className, string $property, ?string $title = null, bool $sortable = true,
        ?array $headerCssClasses = null, ?array $contentCssClasses = null
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

    public function get_class_name(): string
    {
        return $this->className;
    }

    public function set_class_name(string $className)
    {
        $this->className = $className;
    }
}
