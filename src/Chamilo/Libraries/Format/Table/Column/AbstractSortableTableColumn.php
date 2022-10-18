<?php
namespace Chamilo\Libraries\Format\Table\Column;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

abstract class AbstractSortableTableColumn extends TableColumn
{
    private bool $sortable;

    /**
     * @param string $name
     * @param string $title - [OPTIONAL] default null - translation of the column name
     * @param string $headerCssClasses
     * @param string $contentCssClasses
     */
    public function __construct(
        $name = '', $title = null, bool $sortable = true, $headerCssClasses = null, $contentCssClasses = null
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