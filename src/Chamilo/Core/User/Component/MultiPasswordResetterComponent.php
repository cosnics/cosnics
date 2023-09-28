<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Hashing\HashingUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Hackzilla\PasswordGenerator\Generator\PasswordGeneratorInterface;

/**
 * @package Chamilo\Core\User\Component
 */
class MultiPasswordResetterComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        $userIdentifiers = (array) $this->getRequest()->getFromRequestOrQuery(self::PARAM_USER_USER_ID, []);
        $translator = $this->getTranslator();
        $this->set_parameter(self::PARAM_USER_USER_ID, $userIdentifiers);

        if (!$this->getUser()->isPlatformAdmin())
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

            $message = $this->get_result(
                $failures, count($userIdentifiers), 'UserPasswordNotResetted', 'UserPasswordsNotResetted',
                'UserPasswordResetted', 'UserPasswordsResetted'
            );

            $this->redirectWithMessage(
                $message, ($failures > 0), [Application::PARAM_ACTION => self::ACTION_BROWSE_USERS]
            );
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    $translator->trans(
                        'NoObjectSelected', ['OBJECT' => $translator->trans('User', [], Manager::CONTEXT)],
                        StringUtilities::LIBRARIES
                    )
                )
            );
        }
    }

    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([self::PARAM_ACTION => self::ACTION_BROWSE_USERS]),
                $this->getTranslator()->trans('AdminUserBrowserComponent', [], Manager::CONTEXT)
            )
        );
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
