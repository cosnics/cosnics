<?php
namespace Chamilo\Core\User\Form;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;

/**
 * Renders the form for the anonymous users
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AnonymousUserForm extends FormValidator
{
    const CAPTCHA_RESPONS_VALUE = 'g-recaptcha-response';

    /**
     * Constructor
     * 
     * @param string $action
     */
    public function __construct($action)
    {
        parent::__construct('user_settings', 'post', $action);
        
        $defaultRenderer = $this->defaultRenderer();
        $defaultRenderer->setElementTemplate('<div>{element}</div>');
        
        $this->accept($defaultRenderer);
        
        $this->buildForm();
    }

    /**
     * Builds the form
     */
    protected function buildForm()
    {
        $this->addCaptchaElement();
        
        $this->addElement(
            'style_submit_button', 
            'submit', 
            Translation::getInstance()->getTranslation('ViewAnonymously', null, Manager::context()), 
            array('class' => 'anonymous-view-button'),
            null, 
            'user');
    }

    /**
     * Adds the captcha element
     */
    protected function addCaptchaElement()
    {
        $recaptchaSiteKey = Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'recaptcha_site_key'));
        
        $html = array();
        
        $html[] = '<script src="https://www.google.com/recaptcha/api.js"></script>';
        $html[] = '<div class="g-recaptcha" data-sitekey="' . $recaptchaSiteKey . '"></div>';
        
        $this->addElement('html', implode(PHP_EOL, $html));
    }
}