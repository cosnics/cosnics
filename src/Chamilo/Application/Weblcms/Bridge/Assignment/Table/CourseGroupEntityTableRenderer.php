<?php
namespace Chamilo\Application\Weblcms\Bridge\Assignment\Table;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\TableResultPosition;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entity
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class CourseGroupEntityTableRenderer extends EntityTableRenderer
{
    protected function initializeColumns(): void
    {
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(CourseGroup::class, CourseGroup::PROPERTY_NAME)
        );

        parent::initializeColumns();
    }

    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $entity): string
    {
        if ($column->get_name() == CourseGroup::PROPERTY_NAME)
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
