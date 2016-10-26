<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Form\AnonymousUserForm;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Translation;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Landing page for anonymous users to start using Chamilo anonymously, requires an
 */
class AnonymousAccessComponent extends Manager implements NoAuthenticationSupport
{
    /**
     * Runs this component and returns it's output
     */
    public function run()
    {
        $form = new AnonymousUserForm($this->get_url());
        $errorMessage = null;

        if($form->validate())
        {
            try
            {
                $this->validateCaptcha($this->getRequest()->get(AnonymousUserForm::CAPTCHA_RESPONS_VALUE));
                $this->setAuthenticationCookie();
            }
            catch(\Exception $ex)
            {
                $errorMessage = Translation::getInstance()->getTranslation('UseCaptchaToProceed');
            };
        }

        $html = array();

        $html[] = $this->render_header(' ');

        $html[] = '<div class="anonymous-page">';

        $html[] = '<div class="alert alert-info">';
        $html[] = Translation::getInstance()->getTranslation('AnonymousWelcomeMessage', null, Manager::context());
        $html[] = '</div>';

        $html[] = '<div class="anonymous-form-container">';

        if($errorMessage)
        {
            $html[] = '<div class="alert alert-danger">' . $errorMessage . '</div>';
        }

        $html[] = $form->toHtml();

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Validates the given captcha value
     *
     * @param string $captchaResponseValue
     *
     * @throws \Exception
     */
    protected function validateCaptcha($captchaResponseValue)
    {
        $recaptcha = new \ReCaptcha\ReCaptcha('6Lf3TAoUAAAAAPF8E-METNpQZvSnExRAriCBB2pj');
        $response = $recaptcha->verify($captchaResponseValue, $this->getRequest()->server->get('REMOTE_ADDR'));

        if (!$response->isSuccess())
        {
            throw new \Exception('Could not verify the captcha code: ' . implode(' / ', $response->getErrorCodes()));
        }
    }

    /**
     * Sets the anonymous authentication cookie
     */
    protected function setAuthenticationCookie()
    {
        $cookie = new Cookie(md5('anonymous_authentication'), '707d3e39e60d06fe589ae29de21854e97d75d942');

        $redirect = new Redirect();

        $response = new RedirectResponse($redirect->getUrl());
        $response->headers->setCookie($cookie);

        $response->send();

        exit;
    }
}
