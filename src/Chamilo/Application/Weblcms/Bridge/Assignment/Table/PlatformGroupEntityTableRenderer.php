<?php
namespace Chamilo\Application\Weblcms\Bridge\Assignment\Table;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\TableResultPosition;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entity
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class PlatformGroupEntityTableRenderer extends EntityTableRenderer
{
    protected function initializeColumns()
    {
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(Group::class, Group::PROPERTY_NAME)
        );

        parent::initializeColumns();
    }

    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $entity): string
    {
        if ($column->get_name() == Group::PROPERTY_NAME)
        {
            if ($this->canViewEntity($entity))
            {
                return '<a href="' . $this->getEntityUrl($entity) . '">' . $entity[$column->get_name()] . '</a>';
            }
            else
            {
                return $entity[$column->get_name()];
            }
        }

        return parent::renderCell($column, $resultPosition, $entity);
    }

}
