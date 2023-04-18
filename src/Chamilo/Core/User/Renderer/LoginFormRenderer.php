<?php

namespace Chamilo\Core\User\Renderer;

use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Utilities\Utilities;
use Symfony\Component\Translation\Translator;

/**
 * Class LoginFormRenderer
 * @package Chamilo\Core\User\Renderer
 */
class LoginFormRenderer
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * LoginFormRenderer constructor.
     *
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return string
     */
    public function renderLoginForm()
    {
        $redirect = new Redirect();

        $form = new FormValidator('formLogin', 'post', $redirect->getCurrentUrl());

        $form->get_renderer()->setElementTemplate('{element}');

        $form->setRequiredNote('');

        $form->addElement('html', '<div class="form-group">');
        $form->addElement('html', '<div class="input-group">');

        $form->addElement(
            'html',
            '<div class="input-group-addon">' . $this->getTranslation('Username') . '</div>');

        $form->addElement(
            'text',
            'login',
            $this->getTranslation('UserName'),
            array('size' => 20, 'onclick' => 'this.value=\'\';', 'class' => 'form-control'));

        $form->addElement('html', '</div>');
        $form->addElement('html', '</div>');

        $form->addElement('html', '<div class="form-group">');
        $form->addElement('html', '<div class="input-group">');

        $form->addElement(
            'html',
            '<div class="input-group-addon">' . $this->getTranslation('Password') . '</div>');

        $form->addElement(
            'password',
            'password',
            $this->getTranslation('Pass'),
            array('size' => 20, 'onclick' => 'this.value=\'\';', 'class' => 'form-control'));

        $form->addElement('html', '</div>');
        $form->addElement('html', '</div>');

        $form->addElement('html', '<div class="form-group text-right">');
        $form->addElement('style_submit_button', 'submitAuth', $this->getTranslation('Login'), null, null, 'log-in');
        $form->addElement('html', '</div>');

        $form->addRule('password', $this->getTranslation('ThisFieldIsRequired'), 'required');

        $form->addRule('login', $this->getTranslation('ThisFieldIsRequired', Utilities::COMMON_LIBRARIES), 'required');

        return $form->render();
    }

    /**
     * @param string $variable
     * @param string $context
     *
     * @return string
     */
    protected function getTranslation(string $variable, $context = 'Chamilo\\Core\\User')
    {
        return $this->translator->trans($variable, [], $context);
    }
}
