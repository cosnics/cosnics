<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Architecture\Enum\ActionEnum;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Architecture\Exception\ParameterNotDefinedException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Domain\NotificationMessage;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Menu\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class DeleteComponent extends Manager
{
    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exception\ParameterNotDefinedException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $items = $this->getItems();
        $failures = 0;
        $parentIdentifier = 0;

        foreach ($items as $item) {
            if (!$this->getCachedItemService()->deleteItem($item)) {
                $failures ++;
            }

            $parentIdentifier = $item->getParentId();
        }

        $translator = $this->getTranslator();

        if ($failures) {
            if (count($items) == 1) {
                $message = $translator->trans(
                    'SelectedItemNotDeleted', [], StringUtilities::LIBRARIES
                );
            }
            else {
                $message = $translator->trans(
                    'SelectedItemsNotDeleted', [], StringUtilities::LIBRARIES
                );
            }
        }
        elseif (count($items) == 1) {
            $message = $translator->trans(
                'SelectedItemDeleted', [], StringUtilities::LIBRARIES
            );
        }
        else {
            $message = $translator->trans(
                'SelectedItemsDeleted', [], StringUtilities::LIBRARIES
            );
        }

        $this->getNotificationMessageManager()->addMessage(
            new NotificationMessage(
                $translator->trans($message, [], \Chamilo\Core\Group\Manager::CONTEXT),
                $failures ? NotificationMessage::TYPE_DANGER : NotificationMessage::TYPE_SUCCESS
            )
        );

        return new RedirectResponse($this->getUrlGenerator()->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Application::PARAM_ACTION => ActionEnum::BROWSE->value,
            Manager::PARAM_PARENT => $parentIdentifier
        ]));
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Architecture\Exception\ParameterNotDefinedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function getItems(): ArrayCollection
    {
        $itemIdentifiers = $this->getRequest()->query->get(self::PARAM_ITEM);

        if (is_null($itemIdentifiers)) {
            throw new ParameterNotDefinedException(self::PARAM_ITEM);
        }

        if (!is_array($itemIdentifiers)) {
            $itemIdentifiers = [$itemIdentifiers];
        }

        return $this->getItemService()->findItemsByIdentifiers($itemIdentifiers);
    }
}
