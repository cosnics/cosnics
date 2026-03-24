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
use Chamilo\Core\User\UserInterface\Form\UserPictureUpdateFormType;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Chamilo\Libraries\UserInterface\Tab\Service\TabsRenderer;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;
use Twig\Environment;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class UpdateUserPictureComponent extends ProfileComponent
{
    protected UserPictureUpdateFormType $userPictureUpdateFormType;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, UserService $userService,
        UrlGenerator $urlGenerator, TabsRenderer $tabsRenderer, FormFactoryInterface $formFactory,
        Environment $twigEnvironment, ?UserPictureProviderInterface $userPictureProvider,
        UserPictureUpdateFormType $userPictureUpdateFormType, bool $userCanChangePicture
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $authenticationValidator,
            $userUrlGenerator, $activeMailer, $alertsManager, $userService, $urlGenerator, $tabsRenderer, $formFactory,
            $twigEnvironment, $userPictureProvider, $userCanChangePicture
        );

        $this->userPictureUpdateFormType = $userPictureUpdateFormType;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, $currentUser, 'ManageAccount');
        $translator = $this->getTranslator();
        $userPictureProvider = $this->getUserPictureProvider();

        if ($userPictureProvider instanceof UserPictureUpdateProviderInterface) {
            $form = $this->getFormFactory()->create(
                UserPictureUpdateFormType::class, [], [
                    'action' => $this->getUrlGenerator()->fromRequest(),
                    'user' => $currentUser,
                    'executingUser' => $currentUser
                ]
            );
            $form->handleRequest($this->getRequest());

            if ($form->isSubmitted() && $form->isValid()) {
                $submittedData = $form->getData();

                $pictureInformation = $submittedData[User::PROPERTY_PICTURE_URI];

                $success = $userPictureProvider->updateUserPictureFromParameters(
                    $currentUser, $pictureInformation,
                    (bool) $submittedData[AbstractUserFormType::PROPERTY_PICTURE_REMOVE]
                );

                if (!$success) {
                    if ($pictureInformation instanceof UploadedFile && !$pictureInformation->isValid()) {
                        $errorMessage = $pictureInformation->getErrorMessage();
                    }
                    else {
                        $errorMessage = 'UserProfileNotUpdated';
                    }
                }
                else {
                    $errorMessage = 'UserProfileNotUpdated';
                    $successMessage = 'UserProfileUpdated';
                }

                $this->getAlertsManager()->addAlert(
                    new Alert(
                        $this->getTranslator()->trans($success ? $successMessage : $errorMessage),
                        !$success ? AlertEnum::DANGER : AlertEnum::SUCCESS
                    )
                );

                return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::UPDATE_USER_PICTURE->value
                ]));
            }
        }
        else {
            throw new UserException(
                $translator->trans(
                    'UserPictureProviderDoesNotSuportUpdates', [], Manager::CONTEXT
                )
            );
        }

        $html = [];

        $html[] = $this->renderHeader($currentUser);
        $html[] = $this->getTwigEnvironment()->render('form.html.twig', [
            'form' => $form->createView(),
        ]);
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    public function getUserPictureUpdateFormType(): UserPictureUpdateFormType
    {
        return $this->userPictureUpdateFormType;
    }
}
