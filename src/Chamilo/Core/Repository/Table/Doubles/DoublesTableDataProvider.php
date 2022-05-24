<?php
namespace Chamilo\Core\Repository\Table\Doubles;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

class DoublesTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        $condition = $condition[$this->get_table()->is_detail()];

        if ($this->get_table()->is_detail())
        {
            return DataManager::count_active_content_objects(ContentObject::class, $condition);
        }

        return DataManager::count_doubles_in_repository($condition);
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        $condition = $condition[$this->get_table()->is_detail()];

        if ($this->get_table()->is_detail())
        {
            return DataManager::retrieve_active_content_objects(ContentObject::class, $condition);
        }

        return DataManager::retrieve_doubles_in_repository($condition, $count, $offset, $orderBy);
    }
}
