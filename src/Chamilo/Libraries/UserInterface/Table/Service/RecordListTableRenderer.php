<?php
namespace Chamilo\Libraries\UserInterface\Table\Service;

use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableResultPosition;

/**
 * This class represents an html table for an \ArrayIterator with the use of a column model, a data provider and a
 * cell renderer
 *
 * @package Chamilo\Libraries\UserInterface\Table\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop>
 */
abstract class RecordListTableRenderer extends ListTableRenderer
{
    /**
     * @param string[] $result
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, mixed $result): string
    {
        return $result[$column->getName()];
    }

    /**
     * @param string[] $result
     */
    protected function renderIdentifierCell(mixed $result): string
    {
        return $result[DataClass::PROPERTY_ID];
    }
}
