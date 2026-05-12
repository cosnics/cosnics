<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Core\User\UserInterface\Form\ResetPasswordFormType;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertRenderer;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Uid\Uuid;
use Throwable;
use Twig\Environment;

/**
 * @package Chamilo\Core\User\Component
 */
class ResetPasswordComponent extends Manager implements NoAuthenticationSupportInterface
{
    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, UserService $userService,
        protected readonly AlertRenderer $alertRenderer, protected readonly FormFactoryInterface $formFactory,
        protected readonly Environment $twigEnvironment, protected readonly bool $userCanRetrievePassword
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator,
            $authenticationValidator, $userUrlGenerator, $activeMailer, $alertsManager, $userService
        );
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
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
            $userToCreateNewPasswordFor =
                $this->userService->retrieveUserByIdentifier(Uuid::fromString($requestUserIdentifier));

            if ($this->userService->isValidKeyForUser($requestKey, $userToCreateNewPasswordFor)) {
                try {
                    $this->userService->createNewPasswordForUser($userToCreateNewPasswordFor, $currentUser);

                    $html[] = $this->alertRenderer->render(
                        new Alert(
                            $this->translator->trans('YourNewPasswordHasBeenMailedToYou', [], Manager::CONTEXT)
                        )
                    );
                }
                catch (Throwable) {
                    throw new UserException(
                        $this->translator->trans('CreationOfNewPasswordFailed', [], Manager::CONTEXT)
                    );
                }
            }
            else {
                throw new UserException($this->translator->trans('InvalidRequest', [], Manager::CONTEXT));
            }
        }
        else {
            $form = $this->formFactory->create(
                ResetPasswordFormType::class, [], [
                    'action' => $this->getUrlGenerator()->fromRequest()
                ]
            );
            $form->handleRequest($this->getRequest());

            if ($form->isSubmitted() && $form->isValid()) {
                $submittedData = $form->getData();

                $userToResetPasswordFor = $this->userService->retrieveUserByEmail($submittedData[User::PROPERTY_EMAIL]);

                try {
                    $this->userService->sendPasswordResetLinkforUser($userToResetPasswordFor);
                    $html[] = '<div class="alert alert-success">' . $this->translator->trans(
                            'ResetLinkSendForUser', [
                            '%User%' => $userToResetPasswordFor->getFullName() . ' (' .
                                $userToResetPasswordFor->getUsername() . ')'
                        ], Manager::CONTEXT
                        ) . '</div>';
                }
                catch (Throwable) {
                }
            }
            else {
                $html[] = $this->twigEnvironment->render('form.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
        }

        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }
}
