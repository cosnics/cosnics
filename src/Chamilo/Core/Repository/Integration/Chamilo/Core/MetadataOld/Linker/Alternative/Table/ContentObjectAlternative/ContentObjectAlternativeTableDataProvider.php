<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Alternative\Table\ContentObjectAlternative;

use Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld\Linker\Alternative\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;

/**
 * Table data provider for the ContentObjectAlternative data class
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectAlternativeTableDataProvider extends RecordTableDataProvider
{

    /**
     * Returns the data as a resultset
     * 
     * @param \libraries\storage\Condition $condition
     * @param $condition
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[] $order_property
     *
     * @return \libraries\storage\ResultSet
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return DataManager :: retrieve_alternative_content_objects(
            $this->get_component()->get_selected_content_object_id(), 
            $condition, 
            $count, 
            $offset, 
            $order_property);
    }

    /**
     * Counts the data
     * 
     * @param \libraries\storage\Condition $condition
     *
     * @return int
     */
    public function count_data($condition)
    {
        return DataManager :: count_alternative_content_objects(
            $this->get_component()->get_selected_content_object_id(), 
            $condition);
    }
}