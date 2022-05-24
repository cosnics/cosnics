<?php
namespace Chamilo\Core\Repository\External\Table\ExternalObject;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

class DefaultExternalObjectTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return $this->get_component()->count_external_repository_objects($condition);
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        return $this->get_component()->retrieve_external_repository_objects(
            $condition, $orderBy, $offset, $count
        );
    }
}
