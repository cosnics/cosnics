<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exception\UserException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\HTML_QuickForm_stylesubmitbutton;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidator;
use Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Domain\NotificationMessage;
use HTML_QuickForm_Rule_Email;
use HTML_QuickForm_Rule_Required;
use HTML_QuickForm_text;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\User\Component
 */
class ResetPasswordComponent extends Manager implements NoAuthenticationSupportInterface
{
    protected FormValidator $passwordResetForm;

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Architecture\Exception\UserException
     */
    public function run(): Response
    {
        if (!$this->getContainer()->getParameter('cosnics.application.user.rights.retrievePassword')) {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();
        $userService = $this->getUserService();

        if ($this->getUser() instanceof User) {
            throw new UserException($translator->trans('AlreadyRegistered', [], Manager::CONTEXT));
        }

        $html = [];

        $html[] = $this->renderHeader();

        $requestKey = $this->getRequest()->query->get(self::PARAM_RESET_KEY);
        $requestUserIdentifier = $this->getRequest()->query->get(DataClass::PROPERTY_ID);

        if (!is_null($requestKey) && !is_null($requestUserIdentifier)) {
            $user = $userService->findUserByIdentifier($requestUserIdentifier);

            if ($userService->isValidKeyForUser($requestKey, $user)) {
                if (!$userService->createNewPasswordForUser($user)) {
                    throw new UserException($translator->trans('CreationOfNewPasswordFailed', [], Manager::CONTEXT));
                }
                else {
                    $html[] = $this->getNotificationMessageRenderer()->renderOne(
                        new NotificationMessage(
                            $translator->trans('YourNewPasswordHasBeenMailedToYou', [], Manager::CONTEXT)
                        ), false
                    );
                }
            }
            else {
                throw new UserException($translator->trans('InvalidRequest', [], Manager::CONTEXT));
            }
        }
        else {
            $passwordResetForm = $this->getPasswordResetForm();

            if ($passwordResetForm->validate()) {
                $user = $userService->findUserByEmail($passwordResetForm->exportValue(User::PROPERTY_EMAIL));

                if ($userService->sendPasswordResetLinkforUser($user)) {
                    $html[] = '<div class="alert alert-success">' . $translator->trans(
                            'ResetLinkSendForUser',
                            ['USER' => $user->getFullName() . ' (' . $user->getUsername() . ')'], Manager::CONTEXT
                        ) . '</div>';
                }
            }
            else {
                $html[] = $passwordResetForm->render();
            }
        }

        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    /**
     * @throws \QuickformException
     */
    protected function getPasswordResetForm(): FormValidator
    {
        if (!isset($this->passwordResetForm)) {
            $translator = $this->getTranslator();

            $this->passwordResetForm = new FormValidator(
                'lost_password', FormValidator::FORM_METHOD_POST, $this->getUrlGenerator()->fromRequest()
            );

            $this->passwordResetForm->addElement(
                HTML_QuickForm_text::class, User::PROPERTY_EMAIL, $translator->trans('Email', [], Manager::CONTEXT)
            );
            $this->passwordResetForm->addRule(
                User::PROPERTY_EMAIL, $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES),
                HTML_QuickForm_Rule_Required::class
            );
            $this->passwordResetForm->addRule(
                User::PROPERTY_EMAIL, $translator->trans('WrongEmail', [], Manager::CONTEXT),
                HTML_QuickForm_Rule_Email::class
            );
            $this->passwordResetForm->addElement(
                HTML_QuickForm_stylesubmitbutton::class, 'submit',
                $translator->trans('Ok', [], StringUtilities::LIBRARIES)
            );
        }

        return $this->passwordResetForm;
    }
}
