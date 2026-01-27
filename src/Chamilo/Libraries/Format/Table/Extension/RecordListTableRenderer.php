<?php
namespace Chamilo\Libraries\Format\Table\Extension;

use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\ListTableRenderer;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * This class represents an html table for an \ArrayIterator with the use of a column model, a data provider and a
 * cell renderer
 *
 * @package Chamilo\Libraries\Format\Table\Extension
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
