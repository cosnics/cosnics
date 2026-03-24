<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\UserInterface\Form\AbstractUserFormType;
use Chamilo\Core\User\UserInterface\Form\AccountFormType;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Chamilo\Libraries\UserInterface\Tab\Service\TabsRenderer;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;
use Throwable;
use Twig\Environment;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class AccountComponent extends ProfileComponent
{
    protected AccountFormType $accountFormType;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, UserService $userService,
        UrlGenerator $urlGenerator, TabsRenderer $tabsRenderer, FormFactoryInterface $formFactory,
        Environment $twigEnvironment, ?UserPictureProviderInterface $userPictureProvider,
        AccountFormType $accountFormType, bool $userCanChangePicture
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $authenticationValidator,
            $userUrlGenerator, $activeMailer, $alertsManager, $userService, $urlGenerator, $tabsRenderer, $formFactory,
            $twigEnvironment, $userPictureProvider, $userCanChangePicture
        );

        $this->accountFormType = $accountFormType;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function run(?User $currentUser = null): Response
    {
        $this->checkAuthorization(Manager::CONTEXT, $currentUser, 'ManageAccount');
        $translator = $this->getTranslator();

        $form = $this->getFormFactory()->create(
            AccountFormType::class, $currentUser->getDefaultProperties(), [
                'action' => $this->getUrlGenerator()->fromRequest(),
                'user' => $currentUser,
                'executingUser' => $currentUser
            ]
        );
        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $submittedData = $form->getData();

            try {
                $this->getUserService()->updateAccountFromParameters(
                    $currentUser, $submittedData[User::PROPERTY_GIVEN_NAME], $submittedData[User::PROPERTY_SURNAME],
                    $submittedData[User::PROPERTY_USERNAME], $submittedData[User::PROPERTY_OFFICIAL_CODE],
                    $submittedData[User::PROPERTY_EMAIL],
                    $submittedData[AbstractUserFormType::PROPERTY_PASSWORD_CURRENT],
                    $submittedData[User::PROPERTY_PASSWORD]
                );

                $this->getAlertsManager()->addAlert(
                    new Alert(
                        $translator->trans('UserProfileUpdated', [], Manager::CONTEXT), AlertEnum::SUCCESS
                    )
                );

                return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::ACCOUNT->value
                ]));
            }
            catch (Throwable) {
                $this->getAlertsManager()->addAlert(
                    new Alert(
                        $translator->trans('UserProfileNotUpdated', [], Manager::CONTEXT), AlertEnum::DANGER
                    )
                );
            }
        }

        $html = [];

        $html[] = $this->renderHeader($currentUser);
        $html[] = $this->getTwigEnvironment()->render('form.html.twig', [
            'form' => $form->createView(),
        ]);
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    public function getAccountFormType(): AccountFormType
    {
        return $this->accountFormType;
    }
}
