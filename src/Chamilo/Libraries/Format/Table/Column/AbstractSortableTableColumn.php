<?php
namespace Chamilo\Libraries\Format\Table\Column;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 * @package Chamilo\Libraries\Format\Table\Column
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
        string $name = '', string $title, bool $sortable = true, ?array $headerCssClasses = null,
        ?array $contentCssClasses = null
    )
    {
        parent::__construct($name, $title, $headerCssClasses, $contentCssClasses);

        $this->sortable = $sortable;
    }

    abstract public function getConditionVariable(): ConditionVariable;

    public function is_sortable(): bool
    {
        return $this->sortable;
    }

    public function set_sortable(bool $sortable)
    {
        $this->sortable = $sortable;
    }
}