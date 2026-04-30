<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Group\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TruncateComponent extends Manager
{
    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $groupIdentifiers = $this->getRequest()->getFromRequestOrQuery(DataClass::PROPERTY_ID);

        $failures = 0;

        if (!empty($groupIdentifiers)) {
            if (!is_array($groupIdentifiers)) {
                $groupIdentifiers = [$groupIdentifiers];
            }

            foreach ($groupIdentifiers as $groupIdentifier) {
                $group = $this->groupService->findGroupByIdentifier($groupIdentifier);

                try {
                    $this->groupMembershipService->deleteGroupMembershipsByGroup($group, $currentUser);
                }
                catch (RuntimeException) {
                    $failures ++;
                }
            }

            if ($failures) {
                if (count($groupIdentifiers) == 1) {
                    $message = 'SelectedGroupNotEmptied';
                }
                else {
                    $message = 'SelectedGroupsNotEmptied';
                }
            }
            elseif (count($groupIdentifiers) == 1) {
                $message = 'SelectedGroupEmptied';
            }
            else {
                $message = 'SelectedGroupsEmptied';
            }

            $this->alertsManager->addAlert(
                new Alert(
                    $this->translator->trans($message, [], Manager::CONTEXT),
                    $failures ? AlertEnum::DANGER : AlertEnum::SUCCESS
                )
            );

            if (count($groupIdentifiers) == 1) {
                $redirectUrl = $this->getUrlGenerator()->fromParameters([
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value,
                    DataClass::PROPERTY_ID => $groupIdentifiers[0]
                ]);
            }
            else {
                $redirectUrl = $this->getUrlGenerator()->fromParameters([
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value
                ]);
            }

            return new RedirectResponse($redirectUrl);
        }
        else {
            throw new NoSuchParameterException(DataClass::PROPERTY_ID);
        }
    }
}
