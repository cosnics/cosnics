<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component\Configurer;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\PageConfig;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

class ConfigTableDataProvider extends DataClassTableDataProvider
{

    function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $page = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object($condition);
        $configs = $page->get_config();
        return $this->create_config_result_set($configs);
    }

    function count_data($condition)
    {
        $page = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object($condition);
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