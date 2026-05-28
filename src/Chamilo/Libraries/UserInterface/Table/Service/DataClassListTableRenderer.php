<?php
namespace Chamilo\Libraries\UserInterface\Table\Service;

use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\TableColumn;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableResultPosition;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * @package Chamilo\Libraries\UserInterface\Table\Service
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 * @author  Hans De Bisschop <hans.de.bisschop>
 */
abstract class DataClassListTableRenderer extends ListTableRenderer
{
    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\DataClass $result
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, mixed $result): string
    {
        if ($result instanceof DataClass) {
            return (string) $result->getDefaultProperty($column->getName());
        }
        else {
            $propertyAccessor = new PropertyAccessor();
            $value = $propertyAccessor->getValue($result, $column->getName());

            return $value ?: '';
        }
    }

    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\DataClass $result
     */
    protected function renderIdentifierCell(mixed $result): string
    {
        if ($result instanceof DataClass) {
            return $result->getId();
        }
        else {
            return $result->getIdentifier();
        }
    }
}