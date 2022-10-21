<?php
namespace Chamilo\Libraries\Format\Table\Column;

use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Libraries\Format\Table\Column
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SortableStaticTableColumn extends AbstractSortableTableColumn
{

    /**
     * @param string[] $headerCssClasses
     * @param string[] $contentCssClasses
     */
    public function __construct(
        string $name, string $title, ?array $headerCssClasses = null, ?array $contentCssClasses = null
    )
    {
        parent::__construct($name, $title, true, $headerCssClasses, $contentCssClasses);
    }

    public function getConditionVariable(): ConditionVariable
    {
        return new StaticConditionVariable($this->get_name(), false);
    }
}
