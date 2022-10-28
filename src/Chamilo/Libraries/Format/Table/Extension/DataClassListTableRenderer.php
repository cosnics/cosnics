<?php
namespace Chamilo\Libraries\Format\Table\Extension;

use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\ListTableRenderer;

/**
 * @package Chamilo\Libraries\Format\Table\Extension\DataClassTable
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 * @author  Hans De Bisschop <hans.de.bisschop>
 */
abstract class DataClassListTableRenderer extends ListTableRenderer
{
    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     */
    protected function renderCell(TableColumn $column, $dataClass): string
    {
        return $dataClass->getDefaultProperty($column->get_name());
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     */
    protected function renderIdentifierCell($dataClass): string
    {
        return $dataClass->getId();
    }
}