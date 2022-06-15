<?php
namespace Chamilo\Core\Repository\Table\ExternalLink;

use ArrayIterator;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

class ExternalLinkTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return $this->getSynchronizationData() instanceof SynchronizationData ? 1 : 0;
    }

    protected function getSynchronizationData(?Condition $condition = null)
    {
        if (!isset($this->synchronizationData))
        {
            $this->synchronizationData = $this->get_component()->getContentObject()->get_synchronization_data();
        }

        return $this->synchronizationData;
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        $synchronizationDataSet = [];
        $synchronizationData = $this->getSynchronizationData($condition);

        if ($synchronizationData instanceof SynchronizationData)
        {
            $synchronizationDataSet[] = $synchronizationData;
        }

        return new ArrayIterator($synchronizationDataSet);
    }
}
