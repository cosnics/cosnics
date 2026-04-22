<?php
namespace Chamilo\Core\User\Implementation\Home;

use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\UserInterface\HomeRenderer\BlockRenderer;
use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\HTML_QuickForm_button_submit;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidator;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use HTML_QuickForm_html;
use HTML_QuickForm_password;
use HTML_QuickForm_Rule_Required;
use HTML_QuickForm_static;
use HTML_QuickForm_text;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Translation\Translator;
use Twig\Environment;

readonly class LoginBlockRenderer extends BlockRenderer
{
    public const string CONTEXT = Manager::CONTEXT;

    public function __construct(
        HomeService $homeService, UrlGenerator $urlGenerator, Translator $translator, protected ChamiloRequest $request,
        protected  FormFactoryInterface $formFactory, protected  Environment $twigEnvironment,
        protected bool $canRetrievePassword, protected bool $canRegister
    )
    {
        parent::__construct($homeService, $urlGenerator, $translator);
    }

    /**
     * @throws \QuickformException
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

            $html[] = $this->displayLoginForm();
        }
        else {
            $profilePhotoUrl = $this->urlGenerator->fromParameters(
                [
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::DOWNLOAD_USER_PICTURE->value,
                    Manager::PARAM_USER_ID => $user->getId()
                ]
            );

            $logoutLink = $this->urlGenerator->fromParameters(
                [
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::LOGOUT->value
                ]
            );

            $html[] =
                '<img src="' . htmlspecialchars($profilePhotoUrl) . '" alt="' . htmlspecialchars($user->getFullName()) .
                '"  class="img-thumbnail" style="max-width: 100%; max-height:100px" />';
            $html[] = '<h3>' . htmlspecialchars($user->getFullName()) . '</h3>';
            $html[] = '<p>' . htmlspecialchars($user->getEmail()) . '</p>';
            $html[] = '<p><a href="' . $logoutLink . '" class="btn btn-danger" role="button">' . htmlspecialchars(
                    $this->translator->trans('Logout', [], Manager::CONTEXT)
                ) . '</a></p>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \QuickformException
     */
    public function displayLoginForm(): string
    {
        $form = new FormValidator('formLogin', FormValidator::FORM_METHOD_POST);
        $renderer = $form->defaultRenderer();
        $renderer->setElementTemplate('<div class="row">{label}<br />{element}</div>');
        $form->setRequiredNote('');
        $html = '<script>$(document).ready(function(){document.formLogin.login.focus();});</script>';
        $form->addElement(HTML_QuickForm_html::class, $html);
        $form->addElement(
            HTML_QuickForm_text::class, 'login', $this->translator->trans('Username', [], Manager::CONTEXT),
            ['style' => 'width: 90%;']
        );
        $form->addRule('login', $this->translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES),
            HTML_QuickForm_Rule_Required::class);
        $form->addElement(
            HTML_QuickForm_password::class, 'password', $this->translator->trans('Password', [], Manager::CONTEXT),
            ['style' => 'width: 90%;']
        );
        $form->addRule(
            'password', $this->translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES),
            HTML_QuickForm_Rule_Required::class
        );

        $buttons = [];
        $buttons[] = $form->createElement(
            HTML_QuickForm_button_submit::class, 'submitAuth', $this->translator->trans('Login', [], Manager::CONTEXT),
            null, null, new FontAwesomeGlyph('sign-in-alt')
        );

        if ($this->canRegister) {
            $link = $this->urlGenerator->fromParameters(
                [
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::REGISTER->value
                ]
            );

            $glyph = new FontAwesomeGlyph('user', [], null, 'fas');

            $buttons[] = $form->createElement(
                HTML_QuickForm_static::class, null, null,
                '<a href="' . htmlspecialchars($link) . '" class="btn btn-light">' . $glyph->render() . ' ' .
                htmlspecialchars(
                    $this->translator->trans('Reg', [], Manager::CONTEXT)
                ) . '</a>'
            );
        }

        if ($this->canRetrievePassword) {
            $link = $this->urlGenerator->fromParameters(
                [
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::RESET_PASSWORD->value
                ]
            );

            $glyph = new FontAwesomeGlyph('question-circle', [], null, 'fas');

            $buttons[] = $form->createElement(
                HTML_QuickForm_static::class, null, null,
                '<a href="' . htmlspecialchars($link) . '" class="btn btn-light">' . $glyph->render() . ' ' .
                htmlspecialchars(
                    $this->translator->trans('ResetPassword', [], Manager::CONTEXT)
                ) . '</a>'
            );
        }

        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        return $form->render();
    }
}
