<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component\Table\Configurer;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\PageConfig;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\Page;

class ConfigTableDataProvider extends DataClassTableDataProvider
{

    function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $page = DataManager :: retrieve_by_id(Page:: class_name() , $condition);
        $configs = $page->get_config();
        return $this->create_config_result_set($configs);
    }

    function count_data($condition)
    {
        $page = DataManager :: retrieve_by_id(Page:: class_name() , $condition);
        return count($page->get_config());
    }

    function create_config_result_set($configs)
    {
        $config_objects = array();
        
        foreach ($configs as $id => $config)
        {
            $config[PageConfig :: PROPERTY_ID] = $id;
            $config_object = new PageConfig($config);
            $config_objects[] = $config_object;
        }
        return new ArrayResultSet($config_objects);
    }
}
?>