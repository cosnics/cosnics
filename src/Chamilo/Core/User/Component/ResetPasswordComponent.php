<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertRenderer;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\HTML_QuickForm_button_submit;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidator;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use HTML_QuickForm_Rule_Email;
use HTML_QuickForm_Rule_Required;
use HTML_QuickForm_text;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Component
 */
class ResetPasswordComponent extends Manager implements NoAuthenticationSupportInterface
{
    protected AlertRenderer $alertRenderer;

    protected FormValidator $passwordResetForm;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertRenderer $alertRenderer
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $authenticationValidator,
            $userUrlGenerator, $activeMailer
        );

        $this->alertRenderer = $alertRenderer;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \QuickformException
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$this->getContainer()->getParameter('cosnics.application.user.rights.retrievePassword')) {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();
        $userService = $this->getUserService();

        if ($currentUser instanceof User) {
            throw new UserException($translator->trans('AlreadyRegistered', [], Manager::CONTEXT));
        }

        $html = [];

        $html[] = $this->renderHeader($currentUser);

        $requestKey = $this->getRequest()->query->get(self::PARAM_RESET_KEY);
        $requestUserIdentifier = $this->getRequest()->query->get(DataClass::PROPERTY_ID);

        if (!is_null($requestKey) && !is_null($requestUserIdentifier)) {
            $userToCreateNewPasswordFor = $userService->findUserByIdentifier($requestUserIdentifier);

            if ($userService->isValidKeyForUser($requestKey, $userToCreateNewPasswordFor)) {
                if (!$userService->createNewPasswordForUser($userToCreateNewPasswordFor)) {
                    throw new UserException($translator->trans('CreationOfNewPasswordFailed', [], Manager::CONTEXT));
                }
                else {
                    $html[] = $this->getAlertRenderer()->render(
                        new Alert(
                            $translator->trans('YourNewPasswordHasBeenMailedToYou', [], Manager::CONTEXT)
                        )
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
                $userToResetPasswordFor =
                    $userService->findUserByEmail($passwordResetForm->exportValue(User::PROPERTY_EMAIL));

                if ($userService->sendPasswordResetLinkforUser($userToResetPasswordFor)) {
                    $html[] = '<div class="alert alert-success">' . $translator->trans(
                            'ResetLinkSendForUser', [
                            '%User%' => $userToResetPasswordFor->getFullName() . ' (' .
                                $userToResetPasswordFor->getUsername() . ')'
                        ], Manager::CONTEXT
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

    public function getAlertRenderer(): AlertRenderer
    {
        return $this->alertRenderer;
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
                HTML_QuickForm_button_submit::class, 'submit', $translator->trans('Ok', [], StringUtilities::LIBRARIES)
            );
        }

        return $this->passwordResetForm;
    }
}
