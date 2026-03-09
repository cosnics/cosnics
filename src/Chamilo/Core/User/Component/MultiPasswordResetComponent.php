<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\Security\Service\HashingAlgorithm;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Domain\NotificationMessage;
use Hackzilla\PasswordGenerator\Generator\PasswordGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\User\Component
 */
class MultiPasswordResetComponent extends Manager
{
    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function run(?User $currentUser = null): Response
    {
        $userIdentifiers = (array) $this->getRequest()->getFromRequestOrQuery(self::PARAM_USER_ID, []);
        $translator = $this->getTranslator();

        if (!$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        if (count($userIdentifiers) > 0) {
            $userService = $this->getUserService();

            $failures = 0;

            foreach ($userIdentifiers as $userIdentifier) {
                $userToReset = $userService->findUserByIdentifier($userIdentifier);

                if (!$userService->createNewPasswordForUser($userToReset)) {
                    $failures ++;
                }
            }

            if ($failures) {
                if (count($userIdentifiers) == 1) {
                    $message = $translator->trans(
                        'UserPasswordNotResetted', [], Manager::CONTEXT
                    );
                }
                else {
                    $message = $translator->trans(
                        'UserPasswordsNotResetted', [], Manager::CONTEXT
                    );
                }
            }
            elseif (count($userIdentifiers) == 1) {
                $message = $translator->trans(
                    'UserPasswordResetted', [], Manager::CONTEXT
                );
            }
            else {
                $message = $translator->trans(
                    'UserPasswordResetted', [], Manager::CONTEXT
                );
            }

            $this->getNotificationMessageManager()->addMessage(
                new NotificationMessage(
                    $message, $failures ? NotificationMessage::TYPE_DANGER : NotificationMessage::TYPE_SUCCESS
                )
            );

            return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => ActionEnum::BROWSE->value
            ]));
        }
        else {
            return new Response(
                $this->getErrorPageRenderer()->render(
                    $this, htmlentities(
                    $translator->trans(
                        'NoObjectSelected', ['%Object%' => $translator->trans('User', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    )
                ), $currentUser
                )
            );
        }
    }

    public function getHashingUtilities(): HashingAlgorithm
    {
        return $this->getService(HashingAlgorithm::class);
    }

    public function getPasswordGenerator(): PasswordGeneratorInterface
    {
        return $this->getService(PasswordGeneratorInterface::class);
    }
}
