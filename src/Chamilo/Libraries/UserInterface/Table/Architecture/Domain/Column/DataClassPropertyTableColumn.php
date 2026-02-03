<?php
namespace Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;

/**
 * @package Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column
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
        string $className, string $property, string $title, bool $sortable = true, ?array $headerCssClasses = null,
        ?array $contentCssClasses = null
    )
    {
        $this->className = $className;

        parent::__construct($property, $title, $sortable, $headerCssClasses, $contentCssClasses);
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function setClassName(string $className): static
    {
        $this->className = $className;

        return $this;
    }

    public function getConditionVariable(): PropertyConditionVariable
    {
        return new PropertyConditionVariable($this->getClassName(), $this->getName());
    }
}
