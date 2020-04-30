<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component\AssessmentMerger;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

class ObjectTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return DataManager::retrieve_active_content_objects(
            ContentObject::class, 
            $condition);
    }

    public function count_data($condition)
    {
        return DataManager::count_active_content_objects(
            ContentObject::class, 
            $condition);
    }
}
