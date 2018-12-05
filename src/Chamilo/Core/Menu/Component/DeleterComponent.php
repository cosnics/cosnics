<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Manager;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

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
     * @return void
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     * @throws \Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function run()
    {
        $this->getRightsService()->isUserAllowedToAccessComponent($this->getUser());

        $items = $this->getItems();
        $failures = 0;
        $parentIdentifier = 0;

        foreach ($items as $item)
        {
            if (!$this->getItemService()->deleteItem($item))
            {
                $failures ++;
            }

            $parentIdentifier = $item->getParentId();
        }

        $message = $this->get_result(
            $failures, count($items), 'SelectedItemNotDeleted', 'SelectedItemsNotDeleted', 'SelectedItemDeleted',
            'SelectedItemsDeleted'
        );

        $this->redirect(
            $message, ($failures ? true : false),
            array(Manager::PARAM_ACTION => Manager::ACTION_BROWSE, Manager::PARAM_PARENT => $parentIdentifier)
        );
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->getHomeUrl(), $this->getTranslator()->trans('ManagerBrowserComponent', [], 'Chamilo\Core\Menu')
            )
        );
        $breadcrumbtrail->add_help('menu_deleter');
    }

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

    /**
     * @return string[]
     */
    public function get_additional_parameters()
    {
        return array(Manager::PARAM_ITEM, Manager::PARAM_DIRECTION);
    }
}
