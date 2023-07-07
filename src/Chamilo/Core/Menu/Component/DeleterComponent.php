<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Menu\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class DeleterComponent extends Manager
{
    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     * @throws \Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function run()
    {
        $this->getRightsService()->isUserAllowedToAccessComponent($this->getUser());

        $items = $this->getItems();
        $failures = 0;
        $parentIdentifier = 0;

        foreach ($items as $item)
        {
            if (!$this->getCachedItemService()->deleteItem($item))
            {
                $failures ++;
            }

            $parentIdentifier = $item->getParentId();
        }

        $message = $this->get_result(
            $failures, count($items), 'SelectedItemNotDeleted', 'SelectedItemsNotDeleted', 'SelectedItemDeleted',
            'SelectedItemsDeleted'
        );

        $this->redirectWithMessage(
            $message, (bool) $failures,
            [Application::PARAM_ACTION => Manager::ACTION_BROWSE, Manager::PARAM_PARENT => $parentIdentifier]
        );
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->getHomeUrl(), $this->getTranslator()->trans('ManagerBrowserComponent', [], 'Chamilo\Core\Menu')
            )
        );
    }

    /**
     * @return string[]
     */
    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = Manager::PARAM_ITEM;
        $additionalParameters[] = Manager::PARAM_DIRECTION;

        return parent::getAdditionalParameters($additionalParameters);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function getItems(): ArrayCollection
    {
        $itemIdentifiers = $this->getRequest()->query->get(self::PARAM_ITEM);

        if (is_null($itemIdentifiers))
        {
            throw new ParameterNotDefinedException(self::PARAM_ITEM);
        }

        if (!is_array($itemIdentifiers))
        {
            $itemIdentifiers = [$itemIdentifiers];
        }

        return $this->getItemService()->findItemsByIdentifiers($itemIdentifiers);
    }
}
