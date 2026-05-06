<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package Chamilo\Core\Group\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DeleteComponent extends Manager
{
    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function run(?User $currentUser = null): Response
    {
        $identifiers = $this->getRequest()->getFromRequestOrQuery(DataClass::PROPERTY_ID);

        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $failures = 0;

        if (!empty($identifiers)) {
            if (!is_array($identifiers)) {
                $identifiers = [$identifiers];
            }

            foreach ($identifiers as $identifier) {
                $group = $this->groupService->retrieveGroupByIdentifier($identifier);

                try {
                    $this->groupService->deleteGroup($group, $currentUser);
                }
                catch (Throwable) {
                    $failures ++;
                }
            }

            if ($failures) {
                if (count($identifiers) == 1) {
                    $message = $this->translator->trans(
                        'ObjectNotDeleted',
                        ['%Object%' => $this->translator->trans('SelectedGroup', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    );
                }
                else {
                    $message = $this->translator->trans(
                        'ObjectsNotDeleted',
                        ['%Object%' => $this->translator->trans('SelectedGroups', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    );
                }
            }
            elseif (count($identifiers) == 1) {
                $message = $this->translator->trans(
                    'ObjectDeleted', ['%Object%' => $this->translator->trans('SelectedGroup', [], Manager::CONTEXT)],
                    StringUtilities::LIBRARIES
                );
            }
            else {
                $message = $this->translator->trans(
                    'ObjectsDeleted', ['%Object%' => $this->translator->trans('SelectedGroups', [], Manager::CONTEXT)],
                    StringUtilities::LIBRARIES
                );
            }

            $this->alertsManager->addAlert(
                new Alert(
                    $message, $failures ? AlertEnum::DANGER : AlertEnum::SUCCESS
                )
            );

            return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value
            ]));
        }
        else {
            throw new NoSuchParameterException(DataClass::PROPERTY_ID);
        }
    }
}
