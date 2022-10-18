<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component\AssessmentMerger;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

class ObjectTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return DataManager::count_active_content_objects(
            ContentObject::class, $condition
        );
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return DataManager::retrieve_active_content_objects(
            ContentObject::class, $condition
        );
    }
}
