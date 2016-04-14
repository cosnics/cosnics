<?php
namespace Chamilo\Core\Repository\Viewer\Table\ContentObject;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 * This class represents a data provider for a publication candidate table
 */
class ContentObjectTableDataProvider extends DataClassTableDataProvider
{
    /*
     * Inherited
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        if (! $this->get_component()->is_shared_object_browser())
        {
            $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
            return \Chamilo\Core\Repository\Storage\DataManager :: retrieve_active_content_objects(
                ContentObject :: class_name(), 
                $parameters);
        }
        else
        {
            return \Chamilo\Core\Repository\Storage\DataManager :: retrieve_shared_content_objects(
                $condition, 
                $offset, 
                $count, 
                null);
        }
    }
    
    /*
     * Inherited
     */
    public function count_data($condition)
    {
        if (! $this->get_component()->is_shared_object_browser())
        {
            $parameters = new DataClassCountParameters($condition);
            return \Chamilo\Core\Repository\Storage\DataManager :: count_active_content_objects(
                ContentObject :: class_name(), 
                $parameters);
        }
        else
        {
            return \Chamilo\Core\Repository\Storage\DataManager :: count_shared_content_objects($condition);
        }
    }
}
