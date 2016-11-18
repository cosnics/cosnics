<?php
namespace Chamilo\Core\Repository\Table\ImpactView;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 * Description of impact_view_table_data_provider
 * 
 * @author Pieterjan Broekaert
 */
class ImpactViewTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return \Chamilo\Core\Repository\Storage\DataManager::retrieve_active_content_objects(
            ContentObject::class_name(), 
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property));
    }

    public function count_data($condition)
    {
        return DataManager::count_active_content_objects(
            ContentObject::class_name(), 
            new DataClassCountParameters($condition));
    }
}
