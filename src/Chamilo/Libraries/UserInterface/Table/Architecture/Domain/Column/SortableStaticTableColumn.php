<?php
namespace Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\ConditionVariable;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\StaticConditionVariable;

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
        return new StaticConditionVariable($this->getName(), false);
    }
}
