<?php
namespace Chamilo\Application\Weblcms\Table\Publication\Table;

use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;

/**
 * DataProvider for the object publication table
 *
 * @package application.weblcms
 * @author Original Author Unknown
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring to record table
 */

/**
 * This class represents a data provider for a publication candidate table
 */
class ObjectPublicationTableDataProvider extends RecordTableDataProvider
{

    /**
     * Cached the count of the number of objects for reusage.
     *
     * @var int
     */
    private $count_cache;

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
        $tool_browser = $this->get_component()->get_tool_browser();

        if (! $order_property)
        {
            $order_property = $tool_browser->get_default_order_property();
        }

        $type = $tool_browser->get_publication_type();
        switch ($type)
        {
            case \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_FROM_ME :
                return DataManager::retrieve_my_publications(
                    $tool_browser->get_location(),
                    $tool_browser->get_entities(),
                    $tool_browser->get_publication_conditions(),
                    $order_property,
                    $offset,
                    $count,
                    $tool_browser->get_user_id());
                break;
            case \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_ALL :
                return DataManager::retrieve_content_object_publications(
                    $tool_browser->get_publication_conditions(),
                    $order_property,
                    $offset,
                    $count);
                break;
            default :
                return DataManager::retrieve_content_object_publications_with_view_right_granted_in_category_location(
                    $tool_browser->get_location(),
                    $tool_browser->get_entities(),
                    $tool_browser->get_publication_conditions(),
                    $order_property,
                    $offset,
                    $count,
                    $tool_browser->get_user_id());
                break;
        }
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
        $tool_browser = $this->get_component()->get_tool_browser();
        $type = $tool_browser->get_publication_type();

        if (is_null($this->count_cache))
        {
            switch ($type)
            {
                case \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_FROM_ME :
                    $this->count_cache = DataManager::count_my_publications(
                        $tool_browser->get_location(),
                        $tool_browser->get_entities(),
                        $tool_browser->get_publication_conditions(),
                        $tool_browser->get_user_id());
                    break;
                case \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_ALL :
                    $this->count_cache = DataManager::count_content_object_publications(
                        $tool_browser->get_publication_conditions());
                    break;
                default :
                    $this->count_cache = DataManager::count_content_object_publications_with_view_right_granted_in_category_location(
                        $tool_browser->get_location(),
                        $tool_browser->get_entities(),
                        $tool_browser->get_publication_conditions(),
                        $tool_browser->get_user_id());
                    break;
            }
        }
        return $this->count_cache;
    }
}
