<?php
namespace Chamilo\Core\User\Service\Home;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Home\Architecture\Interfaces\AnonymousBlockInterface;
use Chamilo\Core\Home\Architecture\Interfaces\ReadOnlyBlockInterface;
use Chamilo\Core\Home\Form\ConfigurationFormFactory;
use Chamilo\Core\Home\Renderer\BlockRenderer;
use Chamilo\Core\Home\Rights\Service\ElementRightsService;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Authentication\AuthenticationValidator;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

class LoginBlockRenderer extends BlockRenderer implements AnonymousBlockInterface, ReadOnlyBlockInterface
{
    public const CONTEXT = Manager::CONTEXT;

    protected ChamiloRequest $request;

    public function __construct(
        HomeService $homeService, UrlGenerator $urlGenerator, Translator $translator,
        ConfigurationConsulter $configurationConsulter, ChamiloRequest $request,
        ElementRightsService $elementRightsService, ConfigurationFormFactory $configurationFormFactory
    )
    {
        parent::__construct(
            $homeService, $urlGenerator, $translator, $configurationConsulter, $elementRightsService,
            $configurationFormFactory
        );

        $this->request = $request;
    }

    /**
     * @throws \QuickformException
     */
    public function displayContent(Element $block, ?User $user = null): string
    {
        $html = [];

        if (!$user instanceof User || $user->is_anonymous_user())
        {
            $message = $this->getRequest()->query->get(AuthenticationValidator::PARAM_AUTHENTICATION_ERROR);

            if ($message)
            {
                $html[] =
                    '<div class="error-message" style="width: auto; left: 0%; right: 0%; margin: auto;">' . $message .
                    '</div>';
            }

            $html[] = $this->displayLoginForm();
        }
        else
        {
            $profilePhotoUrl = $this->getUrlGenerator()->fromParameters(
                [
                    Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::CONTEXT,
                    Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE,
                    Manager::PARAM_USER_USER_ID => $user->getId()
                ]
            );

            $maximumHeight = $this->getConfigurationConsulter()->getSetting(
                [Manager::CONTEXT, 'restrict_picture_height']
            ) ? 'max-height:100px' : null;

            $logoutLink = $this->getUrlGenerator()->fromParameters(
                [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => Manager::ACTION_LOGOUT
                ]
            );

            $html[] = '<img src="' . htmlspecialchars($profilePhotoUrl) . '" alt="' .
                htmlspecialchars($user->get_fullname()) . '"  class="img-thumbnail" style="max-width: 100%; ' .
                $maximumHeight . '" />';
            $html[] = '<h3>' . htmlspecialchars($user->get_fullname()) . '</h3>';
            $html[] = '<p>' . htmlspecialchars($user->get_email()) . '</p>';
            $html[] = '<p><a href="' . $logoutLink . '" class="btn btn-danger" role="button">' . htmlspecialchars(
                    $this->getTranslator()->trans('Logout', [], Manager::CONTEXT)
                ) . '</a></p>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \QuickformException
     */
    public function displayLoginForm(): string
    {
        $translator = $this->getTranslator();
        $configurationConsulter = $this->getConfigurationConsulter();

        $form = new FormValidator('formLogin', FormValidator::FORM_METHOD_POST);
        $renderer = $form->defaultRenderer();
        $renderer->setElementTemplate('<div class="form-row">{label}<br />{element}</div>');
        $form->setRequiredNote('');
        $html = '<script>$(document).ready(function(){document.formLogin.login.focus();});</script>';
        $form->addElement('html', $html);
        $form->addElement(
            'text', 'login', $translator->trans('UserName', [], Manager::CONTEXT), ['style' => 'width: 90%;']
        );
        $form->addRule('login', $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES), 'required');
        $form->addElement(
            'password', 'password', $translator->trans('Password', [], Manager::CONTEXT), ['style' => 'width: 90%;']
        );
        $form->addRule(
            'password', $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES), 'required'
        );

        $buttons = [];
        $buttons[] = $form->createElement(
            'style_submit_button', 'submitAuth', $translator->trans('Login', [], Manager::CONTEXT), null, null,
            new FontAwesomeGlyph('sign-in-alt')
        );

        if ($configurationConsulter->getSetting(
                [Manager::CONTEXT, 'allow_registration']
            ) || $configurationConsulter->getSetting(
                [Manager::CONTEXT, 'allow_password_retrieval']
            ))
        {
            if ($configurationConsulter->getSetting(
                [Manager::CONTEXT, 'allow_registration']
            ))
            {
                $link = $this->getUrlGenerator()->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => Manager::ACTION_REGISTER_USER
                    ]
                );

                $glyph = new FontAwesomeGlyph('user', [], null, 'fas');

                $buttons[] = $form->createElement(
                    'static', null, null,
                    '<a href="' . htmlspecialchars($link) . '" class="btn btn-default">' . $glyph->render() . ' ' .
                    htmlspecialchars(
                        $translator->trans('Reg', [], Manager::CONTEXT)
                    ) . '</a>'
                );
            }
            if ($configurationConsulter->getSetting(
                [Manager::CONTEXT, 'allow_password_retrieval']
            ))
            {
                $link = $this->getUrlGenerator()->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => Manager::ACTION_RESET_PASSWORD
                    ]
                );

                $glyph = new FontAwesomeGlyph('question-circle', [], null, 'fas');

                $buttons[] = $form->createElement(
                    'static', null, null,
                    '<a href="' . htmlspecialchars($link) . '" class="btn btn-default">' . $glyph->render() . ' ' .
                    htmlspecialchars(
                        $translator->trans('ResetPassword', [], Manager::CONTEXT)
                    ) . '</a>'
                );
            }
        }

        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        return $form->render();
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }
}
