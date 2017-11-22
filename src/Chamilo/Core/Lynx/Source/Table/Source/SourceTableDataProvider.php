<?php
namespace Chamilo\Core\Lynx\Source\Table\Source;

use Chamilo\Core\Lynx\Source\DataClass\Source;
use Chamilo\Core\Lynx\Source\DataManager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class SourceTableDataProvider extends DataClassTableDataProvider
{

    /**
     * Retrieves the objects for this table
     *
     * @param $offset int
     * @param $count int
     * @param $order_property String
     *
     * @return \libraries\storage\ResultSet
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_property);
        return DataManager::retrieves(Source::class_name(), $parameters);
    }

    /**
     * Counts the number of objects for this table
     *
     * @return int
     */
    public function count_data($condition)
    {
        $parameters = new DataClassCountParameters($condition);
        return DataManager::count(Source::class_name(), $parameters);
    }
}
