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
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\Breadcrumb;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package Chamilo\Core\Group\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SubscribeComponent extends Manager
{
    public const string PARAM_USER_ID = 'user_id';

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\ObjectAlreadyExistsException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function run(?User $currentUser = null): Response
    {
        $groupIdentifier = $this->getRequest()->query->get(DataClass::PROPERTY_ID);

        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $userIdentifiers = $this->getRequest()->getFromRequestOrQuery(self::PARAM_USER_ID);

        $this->breadcrumbTrail->add(
            new Breadcrumb($this->translator->trans('ViewerComponent', [], Manager::CONTEXT),
                $this->getUrlGenerator()->fromParameters(
                    [
                        self::PARAM_CONTEXT => Manager::CONTEXT,
                        self::PARAM_ACTION => ActionEnum::BROWSE->value,
                        DataClass::PROPERTY_ID => $groupIdentifier
                    ]
                ))
        );

        if (!empty($userIdentifiers)) {
            if (!is_array($userIdentifiers)) {
                $userIdentifiers = [$userIdentifiers];
            }

            try {
                $this->groupService->createGroupMembershipForGroupIdentifierAndUserIdentifiers(
                    $groupIdentifier, $userIdentifiers, $currentUser
                );

                $message = 'SelectedUserAddedToGroup';
                $messageType = AlertEnum::SUCCESS;
            }
            catch (Throwable) {
                $message = 'SelectedUsersNotAddedToGroup';
                $messageType = AlertEnum::DANGER;
            }

            $this->alertsManager->addAlert(
                new Alert(
                    $this->translator->trans($message, [], Manager::CONTEXT), $messageType
                )
            );

            return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value,
                DataClass::PROPERTY_ID => $groupIdentifier
            ]));
        }
        else {
            throw new NoSuchParameterException(DataClass::PROPERTY_ID);
        }
    }
}
