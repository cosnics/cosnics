<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;

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
    public function run(): Response
    {
        if (!$this->getUser() instanceof User || !$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

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

        return $this->redirectWithMessage(
            $message, (bool) $failures, [
                Application::PARAM_CONTEXT => $this->getContext(),
                Application::PARAM_ACTION => Manager::ACTION_BROWSE,
                Manager::PARAM_PARENT => $parentIdentifier
            ]
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exceptions\StorageMethodException
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
