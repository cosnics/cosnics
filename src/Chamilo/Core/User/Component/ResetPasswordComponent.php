<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertRenderer;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
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
    protected FormValidator $passwordResetForm;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, UserService $userService,
        protected readonly AlertRenderer $alertRenderer, protected readonly bool $userCanRetrievePassword
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator,
            $authenticationValidator, $userUrlGenerator, $activeMailer, $alertsManager, $userService
        );
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$this->userCanRetrievePassword) {
            throw new NotAllowedException();
        }

        if ($currentUser instanceof User) {
            throw new UserException($this->translator->trans('AlreadyRegistered', [], Manager::CONTEXT));
        }

        $html = [];

        $html[] = $this->renderHeader($currentUser);

        $requestKey = $this->getRequest()->query->get(self::PARAM_RESET_KEY);
        $requestUserIdentifier = $this->getRequest()->query->get(DataClass::PROPERTY_ID);

        if (!is_null($requestKey) && !is_null($requestUserIdentifier)) {
            $userToCreateNewPasswordFor = $this->userService->findUserByIdentifier($requestUserIdentifier);

            if ($this->userService->isValidKeyForUser($requestKey, $userToCreateNewPasswordFor)) {
                if (!$this->userService->createNewPasswordForUser($userToCreateNewPasswordFor, $currentUser)) {
                    throw new UserException(
                        $this->translator->trans('CreationOfNewPasswordFailed', [], Manager::CONTEXT)
                    );
                }
                else {
                    $html[] = $this->alertRenderer->render(
                        new Alert(
                            $this->translator->trans('YourNewPasswordHasBeenMailedToYou', [], Manager::CONTEXT)
                        )
                    );
                }
            }
            else {
                throw new UserException($this->translator->trans('InvalidRequest', [], Manager::CONTEXT));
            }
        }
        else {
            $passwordResetForm = $this->getPasswordResetForm();

            if ($passwordResetForm->validate()) {
                $userToResetPasswordFor =
                    $this->userService->findUserByEmail($passwordResetForm->exportValue(User::PROPERTY_EMAIL));

                if ($this->userService->sendPasswordResetLinkforUser($userToResetPasswordFor)) {
                    $html[] = '<div class="alert alert-success">' . $this->translator->trans(
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

    /**
     * @throws \QuickformException
     */
    protected function getPasswordResetForm(): FormValidator
    {
        if (!isset($this->passwordResetForm)) {
            $this->passwordResetForm = new FormValidator(
                'lost_password', FormValidator::FORM_METHOD_POST, $this->getUrlGenerator()->fromRequest()
            );

            $this->passwordResetForm->addElement(
                HTML_QuickForm_text::class, User::PROPERTY_EMAIL,
                $this->translator->trans('Email', [], Manager::CONTEXT)
            );
            $this->passwordResetForm->addRule(
                User::PROPERTY_EMAIL, $this->translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES),
                HTML_QuickForm_Rule_Required::class
            );
            $this->passwordResetForm->addRule(
                User::PROPERTY_EMAIL, $this->translator->trans('WrongEmail', [], Manager::CONTEXT),
                HTML_QuickForm_Rule_Email::class
            );
            $this->passwordResetForm->addElement(
                HTML_QuickForm_button_submit::class, 'submit',
                $this->translator->trans('Ok', [], StringUtilities::LIBRARIES)
            );
        }

        return $this->passwordResetForm;
    }
}
