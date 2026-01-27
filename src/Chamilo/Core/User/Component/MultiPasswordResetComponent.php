<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\Security\Service\HashingUtilities;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Hackzilla\PasswordGenerator\Generator\PasswordGeneratorInterface;
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
    public function run(): Response
    {
        $userIdentifiers = (array) $this->getRequest()->getFromRequestOrQuery(self::PARAM_USER_ID, []);
        $translator = $this->getTranslator();

        if (!$this->getUser()->isPlatformAdministrator())
        {
            throw new NotAllowedException();
        }

        if (count($userIdentifiers) > 0)
        {
            $userService = $this->getUserService();

            $failures = 0;

            foreach ($userIdentifiers as $userIdentifier)
            {
                $user = $userService->findUserByIdentifier($userIdentifier);

                if (!$userService->createNewPasswordForUser($user))
                {
                    $failures ++;
                }
            }

            $message = $this->getResult(
                $failures, count($userIdentifiers), 'UserPasswordNotResetted', 'UserPasswordsNotResetted',
                'UserPasswordResetted', 'UserPasswordsResetted'
            );

            return $this->redirectWithMessage(
                $message, ($failures > 0), [
                    Application::PARAM_CONTEXT => $this->getContext(),
                    Application::PARAM_ACTION => self::ACTION_BROWSE
                ]
            );
        }
        else
        {
            return new Response(
                $this->display_error_page(
                    htmlentities(
                        $translator->trans(
                            'NoObjectSelected', ['OBJECT' => $translator->trans('User', [], Manager::CONTEXT)],
                            StringUtilities::LIBRARIES
                        )
                    )
                )
            );
        }
    }

    public function getHashingUtilities(): HashingUtilities
    {
        return $this->getService(HashingUtilities::class);
    }

    public function getPasswordGenerator(): PasswordGeneratorInterface
    {
        return $this->getService(PasswordGeneratorInterface::class);
    }
}
