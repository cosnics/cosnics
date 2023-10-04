<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

/**
 * Renders the form for the anonymous users
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AnonymousUserForm extends FormValidator
{
    public const CAPTCHA_RESPONS_VALUE = 'g-recaptcha-response';

    /**
     * @throws \QuickformException
     */
    public function __construct(string $action)
    {
        parent::__construct('user_settings', self::FORM_METHOD_POST, $action);

        $defaultRenderer = $this->defaultRenderer();
        $defaultRenderer->setElementTemplate('<div>{element}</div>');

        $this->accept($defaultRenderer);

        $this->buildForm();
    }

    /**
     * @throws \QuickformException
     */
    protected function addCaptchaElement(): void
    {
        $recaptchaSiteKey =
            $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'recaptcha_site_key']);

        $html = [];

        $html[] = '<script src="https://www.google.com/recaptcha/api.js"></script>';
        $html[] = '<div class="g-recaptcha" data-sitekey="' . $recaptchaSiteKey . '"></div>';

        $this->addElement('html', implode(PHP_EOL, $html));
    }

    /**
     * @throws \QuickformException
     */
    protected function buildForm(): void
    {
        $this->addCaptchaElement();

        $this->addElement(
            'style_submit_button', 'submit', $this->getTranslator()->trans('ViewAnonymously', [], Manager::CONTEXT),
            ['class' => 'anonymous-view-button'], null, new FontAwesomeGlyph('user')
        );

        $this->defaultRenderer()->setElementTemplate(
            '<div class="btn-group btn-group-justified" role="group"><div class="btn-group" role="group">{element}</div></div>',
            'submit'
        );
    }
}