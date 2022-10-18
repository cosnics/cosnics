<?php
namespace Chamilo\Core\Repository\External\Table\Export;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

class ExportTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        $parameters = new DataClassCountParameters($condition);

        return DataManager::count_active_content_objects(
            (File::class), $parameters
        );
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $orderBy);

        return DataManager::retrieve_active_content_objects(
            File::class, $parameters
        );
    }
}
