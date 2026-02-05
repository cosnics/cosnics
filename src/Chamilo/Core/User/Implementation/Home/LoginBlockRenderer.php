<?php
namespace Chamilo\Core\User\Implementation\Home;

use Chamilo\Core\Admin\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\UserInterface\HomeRenderer\BlockRenderer;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\HTML_QuickForm_stylesubmitbutton;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidator;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use HTML_QuickForm_html;
use HTML_QuickForm_password;
use HTML_QuickForm_Rule_Required;
use HTML_QuickForm_static;
use HTML_QuickForm_text;
use Symfony\Component\Translation\Translator;

class LoginBlockRenderer extends BlockRenderer
{
    public const CONTEXT = Manager::CONTEXT;

    protected ChamiloRequest $request;

    public function __construct(
        HomeService $homeService, UrlGenerator $urlGenerator, Translator $translator,
        ConfigurationConsulter $configurationConsulter, ChamiloRequest $request
    )
    {
        parent::__construct(
            $homeService, $urlGenerator, $translator, $configurationConsulter
        );

        $this->request = $request;
    }

    /**
     * @throws \QuickformException
     */
    public function displayContent(Element $block, ?User $user = null): string
    {
        $html = [];

        if (!$user instanceof User) {
            $message = $this->getRequest()->query->get(AuthenticationValidator::PARAM_AUTHENTICATION_ERROR);

            if ($message) {
                $html[] =
                    '<div class="error-message" style="width: auto; left: 0%; right: 0%; margin: auto;">' . $message .
                    '</div>';
            }

            $html[] = $this->displayLoginForm();
        }
        else {
            $profilePhotoUrl = $this->getUrlGenerator()->fromParameters(
                [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => Manager::ACTION_USER_PICTURE,
                    Manager::PARAM_USER_ID => $user->getId()
                ]
            );

            $logoutLink = $this->getUrlGenerator()->fromParameters(
                [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => Manager::ACTION_LOGOUT
                ]
            );

            $html[] =
                '<img src="' . htmlspecialchars($profilePhotoUrl) . '" alt="' . htmlspecialchars($user->getFullName()) .
                '"  class="img-thumbnail" style="max-width: 100%; max-height:100px" />';
            $html[] = '<h3>' . htmlspecialchars($user->getFullName()) . '</h3>';
            $html[] = '<p>' . htmlspecialchars($user->getEmail()) . '</p>';
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
        $form->addElement(HTML_QuickForm_html::class, $html);
        $form->addElement(
            HTML_QuickForm_text::class, 'login', $translator->trans('UserName', [], Manager::CONTEXT),
            ['style' => 'width: 90%;']
        );
        $form->addRule('login', $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES),
            HTML_QuickForm_Rule_Required::class);
        $form->addElement(
            HTML_QuickForm_password::class, 'password', $translator->trans('Password', [], Manager::CONTEXT),
            ['style' => 'width: 90%;']
        );
        $form->addRule(
            'password', $translator->trans('ThisFieldIsRequired', [], StringUtilities::LIBRARIES),
            HTML_QuickForm_Rule_Required::class
        );

        $buttons = [];
        $buttons[] = $form->createElement(
            HTML_QuickForm_stylesubmitbutton::class, 'submitAuth', $translator->trans('Login', [], Manager::CONTEXT),
            null, null, new FontAwesomeGlyph('sign-in-alt')
        );

        if ($configurationConsulter->getSetting(
                [Manager::CONTEXT, 'allow_registration']
            ) || $configurationConsulter->getSetting(
                [Manager::CONTEXT, 'allow_password_retrieval']
            )) {
            if ($configurationConsulter->getSetting(
                [Manager::CONTEXT, 'allow_registration']
            )) {
                $link = $this->getUrlGenerator()->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => Manager::ACTION_REGISTER
                    ]
                );

                $glyph = new FontAwesomeGlyph('user', [], null, 'fas');

                $buttons[] = $form->createElement(
                    HTML_QuickForm_static::class, null, null,
                    '<a href="' . htmlspecialchars($link) . '" class="btn btn-default">' . $glyph->render() . ' ' .
                    htmlspecialchars(
                        $translator->trans('Reg', [], Manager::CONTEXT)
                    ) . '</a>'
                );
            }
            if ($configurationConsulter->getSetting(
                [Manager::CONTEXT, 'allow_password_retrieval']
            )) {
                $link = $this->getUrlGenerator()->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => Manager::ACTION_RESET_PASSWORD
                    ]
                );

                $glyph = new FontAwesomeGlyph('question-circle', [], null, 'fas');

                $buttons[] = $form->createElement(
                    HTML_QuickForm_static::class, null, null,
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
