<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Tracking\Storage\DataClass\ChangesTracker;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Hashing\HashingUtilities;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;
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

                $password = $this->getPasswordGenerator()->generatePassword();
                $user->set_password($this->getHashingUtilities()->hashString($password));

                if ($userService->updateUser($user))
                {
                    $mail_subject = $translator->trans('LoginRequest', [], Manager::CONTEXT);
                    $mail_body = [];

                    $mail_body[] = $user->get_fullname() . ',';
                    $mail_body[] = $translator->trans('YourAccountParam', [], Manager::CONTEXT) . ' ' .
                        $this->getWebPathBuilder()->getBasePath();
                    $mail_body[] = $translator->trans('UserName', [], Manager::CONTEXT) . ' :' . $user->get_username();
                    $mail_body[] = $translator->trans('Password', [], Manager::CONTEXT) . ' :' . $password;

                    $mail_body = implode(PHP_EOL, $mail_body);

                    $mail = new Mail($mail_subject, $mail_body, $user->get_email());

                    $mailer = $this->getActiveMailer();

                    try
                    {
                        $mailer->sendMail($mail);
                    }
                    catch (Exception)
                    {
                    }

                    Event::trigger(
                        'Update', Manager::CONTEXT, [
                            ChangesTracker::PROPERTY_REFERENCE_ID => $user->getId(),
                            ChangesTracker::PROPERTY_USER_ID => $this->getUser()->getId()
                        ]
                    );
                }
                else
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
