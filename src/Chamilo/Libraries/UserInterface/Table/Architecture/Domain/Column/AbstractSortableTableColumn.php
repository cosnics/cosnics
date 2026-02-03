<?php
namespace Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;

/**
 * @package Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractSortableTableColumn extends TableColumn
{
    private bool $sortable;

    /**
     * @param string[] $headerCssClasses
     * @param string[] $contentCssClasses
     */
    public function __construct(
        string $name, string $title, bool $sortable = true, ?array $headerCssClasses = null,
        ?array $contentCssClasses = null
    )
    {
        parent::__construct($name, $title, $headerCssClasses, $contentCssClasses);

        $this->sortable = $sortable;
    }

    abstract public function getConditionVariable(): StaticConditionVariable|PropertyConditionVariable;

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function setSortable(bool $sortable): static
    {
        $this->sortable = $sortable;

        return $this;
    }
}