<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\User\Component
 */
class ResetPasswordComponent extends Manager implements NoAuthenticationSupportInterface
{

    protected FormValidator $passwordResetForm;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function run()
    {
        if (!$this->getConfigurationConsulter()->getSetting([Manager::CONTEXT, 'allow_password_retrieval']))
        {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();
        $userService = $this->getUserService();

        if ($this->getUser() instanceof User)
        {
            throw new UserException($translator->trans('AlreadyRegistered', [], Manager::CONTEXT));
        }

        $html = [];

        $html[] = $this->renderHeader();

        $requestKey = $this->getRequest()->query->get(self::PARAM_RESET_KEY);
        $requestUserIdentifier = $this->getRequest()->query->get(DataClass::PROPERTY_ID);

        if (!is_null($requestKey) && !is_null($requestUserIdentifier))
        {
            $user = $userService->findUserByIdentifier($requestUserIdentifier);

            if ($userService->isValidKeyForUser($requestKey, $user))
            {
                if (!$userService->createNewPasswordForUser($user))
                {
                    throw new UserException($translator->trans('CreationOfNewPasswordFailed', [], Manager::CONTEXT));
                }
                else
                {
                    $html[] = Display::normal_message(
                        $translator->trans(
                            'YourNewPasswordHasBeenMailedToYou', [], Manager::CONTEXT
                        )
                    );
                }
            }
            else
            {
                throw new UserException($translator->trans('InvalidRequest', [], Manager::CONTEXT));
            }
        }
        else
        {
            $passwordResetForm = $this->getPasswordResetForm();

            if ($passwordResetForm->validate())
            {
                $user = $userService->findUserByEmail($passwordResetForm->exportValue(User::PROPERTY_EMAIL));

                if ($userService->sendPasswordResetLinkforUser($user))
                {
                    $html[] = '<div class="alert alert-success">' . $translator->trans(
                            'ResetLinkSendForUser',
                            ['USER' => $user->get_fullname() . ' (' . $user->get_username() . ')'], Manager::CONTEXT
                        ) . '</div>';
                }
            }
            else
            {
                $html[] = $passwordResetForm->render();
            }
        }

        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \QuickformException
     */
    protected function getPasswordResetForm(): FormValidator
    {
        if (!isset($this->passwordResetForm))
        {
            $translator = $this->getTranslator();

            $this->passwordResetForm =
                new FormValidator('lost_password', FormValidator::FORM_METHOD_POST, $this->get_url());

            $this->passwordResetForm->addElement(
                'text', User::PROPERTY_EMAIL, $translator->trans('Email', [], Manager::CONTEXT)
            );
            $this->passwordResetForm->addRule(
                User::PROPERTY_EMAIL, $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES),
                'required'
            );
            $this->passwordResetForm->addRule(
                User::PROPERTY_EMAIL, $translator->trans('WrongEmail', [], Manager::CONTEXT), 'email'
            );
            $this->passwordResetForm->addElement(
                'style_submit_button', 'submit', $translator->trans('Ok', [], StringUtilities::LIBRARIES)
            );
        }

        return $this->passwordResetForm;
    }
}
