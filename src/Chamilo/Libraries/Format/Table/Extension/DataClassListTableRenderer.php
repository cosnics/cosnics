<?php
namespace Chamilo\Libraries\Format\Table\Extension;

use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\ListTableRenderer;
use Chamilo\Libraries\Format\Table\TableResultPosition;

/**
 * @package Chamilo\Libraries\Format\Table\Extension
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 * @author  Hans De Bisschop <hans.de.bisschop>
 */
abstract class DataClassListTableRenderer extends ListTableRenderer
{
    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $result
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, mixed $result): string
    {
        return (string) $result->getDefaultProperty($column->getName());
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $result
     */
    protected function renderIdentifierCell(mixed $result): string
    {
        return $result->getId();
    }
}