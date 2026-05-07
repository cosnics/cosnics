<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Architecture\Enum\ActionEnum;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

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
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException
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
            try {
                $this->cachedItemService->deleteItem($item);
            }
            catch (Throwable) {
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

        $this->alertsManager->addAlert(
            new Alert(
                $translator->trans($message, [], \Chamilo\Core\Group\Manager::CONTEXT),
                $failures ? AlertEnum::DANGER : AlertEnum::SUCCESS
            )
        );

        return new RedirectResponse($this->getUrlGenerator()->fromParameters([
            ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
            ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value,
            Manager::PARAM_PARENT => $parentIdentifier
        ]));
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Menu\Storage\DataClass\Item>
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function getItems(): ArrayCollection
    {
        $itemIdentifiers = $this->getRequest()->query->get(self::PARAM_ITEM);

        if (is_null($itemIdentifiers)) {
            throw new NoSuchParameterException(self::PARAM_ITEM);
        }

        if (!is_array($itemIdentifiers)) {
            $itemIdentifiers = [$itemIdentifiers];
        }

        return $this->itemService->findItemsByIdentifiers($itemIdentifiers);
    }
}
