<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Chamilo\Core\User\Architecture\Interface\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\UserInterface\Form\AbstractUserFormType;
use Chamilo\Core\User\UserInterface\Form\UserFormType;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;
use Twig\Environment;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UpdateComponent extends Manager
{
    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, UserService $userService,
        protected readonly FormFactoryInterface $formFactory, protected readonly Environment $twigEnvironment,
        protected readonly bool $userCanChangePicture,
        protected readonly ?UserPictureProviderInterface $userPictureProvider
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator,
            $authenticationValidator, $userUrlGenerator, $activeMailer, $alertsManager, $userService
        );
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, $currentUser, 'ManageUsers');

        if (!$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        $userIdentifier = $this->getRequest()->query->get(self::PARAM_USER_ID);

        if ($userIdentifier) {
            $userToUpdate = $this->userService->retrieveUserByIdentifier($userIdentifier);
            $isLockoutRisk =
                $currentUser->getId() == $userToUpdate->getId() && $userToUpdate->isPlatformAdministrator();

            $updateUrl = $urlGenerator->fromParameters([
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::UPDATE->value,
                self::PARAM_USER_ID => $userIdentifier
            ]);

            $form = $this->formFactory->create(
                UserFormType::class, $userToUpdate->getDefaultProperties(), [
                    'action' => $updateUrl,
                    'user' => $userToUpdate,
                    'executingUser' => $currentUser,
                    'isLockoutRisk' => $isLockoutRisk
                ]
            );

            $form->handleRequest($this->getRequest());

            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $submittedData = $form->getData();

                    $this->userService->updateUserFromParameters(
                        $userToUpdate, $submittedData[User::PROPERTY_GIVEN_NAME],
                        $submittedData[User::PROPERTY_SURNAME], $submittedData[User::PROPERTY_USERNAME],
                        $submittedData[User::PROPERTY_OFFICIAL_CODE], $submittedData[User::PROPERTY_EMAIL],
                        (bool) $submittedData[AbstractUserFormType::PROPERTY_PASSWORD_GENERATE],
                        $submittedData[User::PROPERTY_PASSWORD],
                        (bool) $submittedData[User::PROPERTY_PLATFORM_ADMINISTRATOR],
                        (bool) $submittedData[User::PROPERTY_ACTIVE],
                        (bool) $submittedData[AbstractUserFormType::PROPERTY_SEND_MAIL]
                    );

                    if ($this->userPictureProvider instanceof UserPictureUpdateProviderInterface) {
                        $pictureInformation = $submittedData[User::PROPERTY_PICTURE_URI];

                        if ($pictureInformation instanceof UploadedFile && $pictureInformation->isValid()) {
                            if (!$this->userPictureProvider->updateUserPictureFromParameters(
                                $userToUpdate, $pictureInformation,
                                (bool) $submittedData[AbstractUserFormType::PROPERTY_PICTURE_REMOVE], $currentUser
                            )) {
                                $this->alertsManager->addAlert(
                                    new Alert(
                                        $translator->trans('UserPictureNotUpdated', [], Manager::CONTEXT),
                                        AlertEnum::WARNING
                                    )
                                );
                            }
                        }
                    }

                    $this->alertsManager->addAlert(
                        new Alert(
                            $translator->trans('UserUpdated', [], Manager::CONTEXT), AlertEnum::SUCCESS
                        )
                    );

                    return new RedirectResponse(
                        $urlGenerator->fromParameters(
                            [
                                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                                ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value
                            ]
                        )
                    );
                }
                catch (Exception $exception) {
                    $this->alertsManager->addAlert(
                        new Alert($exception->getMessage(), AlertEnum::DANGER)
                    );
                }
            }

            $html = [];

            $html[] = $this->renderHeader($currentUser);
            $html[] = $this->twigEnvironment->render('form.html.twig', [
                'form' => $form->createView(),
            ]);
            $html[] = $this->renderFooter();

            return new Response(implode(PHP_EOL, $html));
        }
        else {
            throw new NoSuchParameterException(self::PARAM_USER_ID);
        }
    }

    public function canUserChangePicture(): bool
    {
        return $this->userCanChangePicture && $this->userPictureProvider instanceof UserPictureUpdateProviderInterface;
    }
}
