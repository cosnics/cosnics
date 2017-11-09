<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Repository\ItemRepository;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;

/**
 *
 * @package Chamilo\Core\Menu\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class MoverComponent extends Manager
{

    public function run()
    {
        $this->check_allowed();
        $direction = Request::get(Manager::PARAM_DIRECTION);
        $this->set_parameter(Manager::PARAM_DIRECTION, $direction);
        $item = intval(Request::get(Manager::PARAM_ITEM));
        $this->set_parameter(Manager::PARAM_ITEM, $item);

        if (isset($direction) && isset($item))
        {
            $move_item = DataManager::retrieve_by_id(Item::class_name(), (int) $item);

            $max = DataManager::count(
                Item::class_name(),
                new DataClassCountParameters(
                    new EqualityCondition(
                        new PropertyConditionVariable(Item::class_name(), Item::PROPERTY_PARENT),
                        new StaticConditionVariable($move_item->get_parent()))));

            $display_order = $move_item->get_sort();
            $new_place = ($display_order + ($direction == Manager::PARAM_DIRECTION_UP ? - 1 : 1));

            if ($new_place > 0 && $new_place <= $max)
            {
                $move_item->set_sort($new_place);
                $success = $move_item->update();
            }

            $message = $success ? Translation::get(
                'ObjectMoved',
                array('OBJECT' => Translation::get('ManagerItem')),
                Utilities::COMMON_LIBRARIES) : Translation::get(
                'ObjectNotMoved',
                array('OBJECT' => Translation::get('ManagerItem')),
                Utilities::COMMON_LIBRARIES);

            $itemService = new ItemService(new ItemRepository());
            $itemService->resetCache();

            $this->redirect(
                $message,
                ($success ? false : true),
                array(
                    Manager::PARAM_ACTION => Manager::ACTION_BROWSE,
                    Manager::PARAM_PARENT => $move_item->get_parent()));
        }
        else
        {
            return $this->display_error_page(Translation::get('NoObjectsSelected', null, Utilities::COMMON_LIBRARIES));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_menu_home_url(), Translation::get('ManagerBrowserComponent')));
        $breadcrumbtrail->add_help('menu_mover');
    }
}
