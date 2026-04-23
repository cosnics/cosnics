<?php
namespace Chamilo\Core\User\Implementation\Home;

use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\UserInterface\HomeRenderer\BlockRenderer;
use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\UserInterface\Form\LoginFormType;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonRenderer;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Translation\Translator;
use Twig\Environment;

readonly class LoginBlockRenderer extends BlockRenderer
{
    public const string CONTEXT = Manager::CONTEXT;

    public function __construct(
        HomeService $homeService, UrlGenerator $urlGenerator, Translator $translator, protected ChamiloRequest $request,
        protected FormFactoryInterface $formFactory, protected Environment $twigEnvironment,
        protected ButtonRenderer $buttonRenderer, protected bool $canRetrievePassword, protected bool $canRegister
    )
    {
        parent::__construct($homeService, $urlGenerator, $translator);
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function displayContent(Element $block, ?User $user = null): string
    {
        $html = [];

        if (!$user instanceof User) {
            $message = $this->request->query->get(AuthenticationValidator::PARAM_AUTHENTICATION_ERROR);

            if ($message) {
                $html[] =
                    '<div class="error-message" style="width: auto; left: 0%; right: 0%; margin: auto;">' . $message .
                    '</div>';
            }

            $form = $this->formFactory->create(
                type: LoginFormType::class, options: [
                'action' => $this->urlGenerator->fromRequest()
            ]
            );

            $html[] = $this->twigEnvironment->render('form.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        else {
            $profilePhotoUrl = $this->urlGenerator->fromParameters(
                [
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::DOWNLOAD_USER_PICTURE->value,
                    Manager::PARAM_USER_ID => $user->getId()
                ]
            );

            $html[] =
                '<img src="' . htmlspecialchars($profilePhotoUrl) . '" alt="' . htmlspecialchars($user->getFullName()) .
                '"  class="img-thumbnail" style="max-width: 100%; max-height:100px" />';
            $html[] = '<h3>' . htmlspecialchars($user->getFullName()) . '</h3>';
            $html[] = '<p>' . htmlspecialchars($user->getEmail()) . '</p>';

            $logoutUri = $this->urlGenerator->fromParameters(
                [
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::LOGOUT->value
                ]
            );

            $logoutText = $this->translator->trans('Logout', [], Manager::CONTEXT);
            $logoutGlyph = new FontAwesomeGlyph('sign-out-alt', ['me-1'], $logoutText, 'fas');

            $button = new Button($logoutText, $logoutGlyph, $logoutUri, classes: ['btn', 'btn-danger']);

            $html[] = '<p>';
            $html[] = $this->buttonRenderer->render($button);
            $html[] = '</p>';
        }

        return implode(PHP_EOL, $html);
    }
}
