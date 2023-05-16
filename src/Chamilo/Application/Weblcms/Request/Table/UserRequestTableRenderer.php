<?php
namespace Chamilo\Application\Weblcms\Request\Table;

use Chamilo\Core\Repository\Quota\Storage\DataClass\Request;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\TableResultPosition;

/**
 * @package Chamilo\Core\Repository\Quota\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserRequestTableRenderer extends RequestTableRenderer
{
    protected function initializeColumns()
    {
        parent::initializeColumns();

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(Request::class, Request::PROPERTY_DECISION)
        );
    }

    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $request): string
    {
        if ($column->get_name() == Request::PROPERTY_DECISION)
        {
            return $request->get_decision_icon();
        }

        return parent::renderCell($column, $resultPosition, $request);
    }

}