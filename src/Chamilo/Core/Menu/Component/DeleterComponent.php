<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Storage\Repository\ItemRepository;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;
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
    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[]
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     */
    protected function getItems()
    {
        $itemIdentifiers = $this->getRequest()->query->get(self::PARAM_ITEM);

        if (is_null($itemIdentifiers))
        {
            throw new ParameterNotDefinedException(self::PARAM_ITEM);
        }

        if (!is_array($itemIdentifiers))
        {
            $itemIdentifiers = array($itemIdentifiers);
        }

        return $this->getItemService()->findItemsByIdentifiers($itemIdentifiers);
    }

    public function run()
    {
        $this->check_allowed();

        $items = $this->getItems();
        $failures = 0;

        foreach ($items as $item)
        {
            if (!$this->getItemService()->deleteItem($item))
            {
                $failures ++;
            }
        }

        $message = $this->get_result(
            $failures, count($items), 'SelectedItemNotDeleted', 'SelectedItemsNotDeleted', 'SelectedItemDeleted',
            'SelectedItemsDeleted'
        );

        $this->redirect(
            $message, ($failures ? true : false),
            array(Manager::PARAM_ACTION => Manager::ACTION_BROWSE, Manager::PARAM_PARENT => $item->get_parent())
        );
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
