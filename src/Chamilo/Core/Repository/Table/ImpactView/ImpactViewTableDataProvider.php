<?php
namespace Chamilo\Core\Repository\Table\ImpactView;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

/**
 * Description of impact_view_table_data_provider
 * 
 * @author Pieterjan Broekaert
 */
class ImpactViewTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count)
    {
        return \Chamilo\Core\Repository\Storage\DataManager :: count_active_content_objects(
            ContentObject :: class_name(), 
            $condition);
    }

    public function count_data($condition)
    {
        return DataManager :: retrieve_active_content_objects(ContentObject :: class_name(), $condition);
    }
}
