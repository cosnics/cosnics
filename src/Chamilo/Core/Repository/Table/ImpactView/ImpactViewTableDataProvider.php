<?php
namespace Chamilo\Core\Repository\Table\ImpactView;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of impact_view_table_data_provider
 *
 * @author Pieterjan Broekaert
 */
class ImpactViewTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return DataManager::count_active_content_objects(
            ContentObject::class, new DataClassCountParameters($condition)
        );
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return DataManager::retrieve_active_content_objects(
            ContentObject::class, new DataClassRetrievesParameters($condition, $count, $offset, $orderBy)
        );
    }
}
