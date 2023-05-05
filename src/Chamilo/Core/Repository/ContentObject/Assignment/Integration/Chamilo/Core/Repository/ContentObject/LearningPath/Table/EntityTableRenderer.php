<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\TableResultPosition;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EntityTableRenderer extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\EntityTableRenderer
{

    public function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_FIRSTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_LASTNAME));

        parent::initializeColumns();
    }

    protected function isEntity($entityId, $userId): bool
    {
        return $entityId == $userId;
    }

    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $entity): string
    {
        switch ($column->get_name())
        {
            case User::PROPERTY_FIRSTNAME:
            case User::PROPERTY_LASTNAME:
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