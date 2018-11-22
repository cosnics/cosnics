<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Storage\Repository\ItemRepository;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Menu\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DeleterComponent extends Manager
{

    public function run()
    {
        $this->check_allowed();
        $items = $this->getRequest()->get(Manager::PARAM_ITEM);
        
        $parent_ids = array();
        $failures = 0;
        
        if (! empty($items))
        {
            if (! is_array($items))
            {
                $items = array($items);
            }
            
            foreach ($items as $id)
            {
                $item = DataManager::retrieve_by_id(Item::class_name(), intval($id));
                $parent_ids[$item->get_parent()] = $item->get_parent();
                
                if (! $item->delete())
                {
                    $failures ++;
                }
            }
            
            // Reassign sorts to the remaining menu items
            foreach ($parent_ids as $parent_id)
            {
                $condition = new EqualityCondition(
                    new PropertyConditionVariable(Item::class_name(), Item::PROPERTY_PARENT), 
                    new StaticConditionVariable($parent_id));
                $order_by = array();
                $order_by[] = new OrderBy(new PropertyConditionVariable(Item::class_name(), Item::PROPERTY_SORT));
                $parameters = new DataClassRetrievesParameters($condition, null, null, $order_by);
                
                $remaining_items = DataManager::retrieves(Item::class_name(), $parameters);
                $count = 1;
                while ($remaining_item = $remaining_items->next_result())
                {
                    $remaining_item->set_sort($count);
                    if (! $remaining_item->update())
                    {
                        break;
                    }
                    $count ++;
                }
            }
            
            $message = $this->get_result(
                $failures, 
                count($items), 
                'SelectedItemNotDeleted', 
                'SelectedItemsNotDeleted', 
                'SelectedItemDeleted', 
                'SelectedItemsDeleted');
            
            $itemService = new ItemService(new ItemRepository());
            $itemService->resetCache();
            
            $this->redirect(
                $message, 
                ($failures ? true : false), 
                array(Manager::PARAM_ACTION => Manager::ACTION_BROWSE, Manager::PARAM_PARENT => $item->get_parent()));
        }
        else
        {
            return $this->display_error_page(
                Translation::get('NoObjectsSelected', null, Utilities::COMMON_LIBRARIES));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_menu_home_url(), Translation::get('ManagerBrowserComponent')));
        $breadcrumbtrail->add_help('menu_deleter');
    }

    public function get_additional_parameters()
    {
        return array(Manager::PARAM_ITEM, Manager::PARAM_DIRECTION);
    }
}
