<?php
namespace Chamilo\Core\Repository\Instance\Table\Instance;

use Chamilo\Core\Repository\Instance\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Table data provider for the schema
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class InstanceTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return DataManager::count($this->get_component()->getType(), new DataClassCountParameters($condition));
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $orderBy);

        return DataManager::retrieves($this->get_component()->getType(), $parameters);
    }
}