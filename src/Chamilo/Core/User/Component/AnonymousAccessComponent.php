<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\User\Form\AnonymousUserForm;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Roles\Service\Interfaces\UserRoleServiceInterface;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Platform\Session\Session;
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
        if (!$this->anonymousAccessAllowed())
        {
            throw new NotAllowedException();
        }

        $form = new AnonymousUserForm($this->get_url());
        $errorMessage = null;

        if ($form->validate())
        {
            try
            {
                $this->validateCaptcha($this->getRequest()->get(AnonymousUserForm::CAPTCHA_RESPONS_VALUE));
                $anonymousUser = $this->createAnonymousUser();
                $this->addAnonymousRoleToUser($anonymousUser);
                $this->setAuthenticationCookieAndRedirect($anonymousUser);
            }
            catch (\Exception $ex)
            {
                $errorMessage =
                    Translation::getInstance()->getTranslation('UseCaptchaToProceed', null, Manager::context());
            }
        }

        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);

        $html = array();

        $html[] = $this->render_header(' ');

        $html[] = '<div class="anonymous-page">';

        $html[] = '<div class="panel anonymous-container">';
        $html[] = '<div class="panel-body">';

        $html[] = '<h2 class="header">';
        $html[] = $this->getConfigurationConsulter()->getSetting(array('Chamilo\Core\Admin', 'institution'));
        $html[] = '</h2>';

        $html[] = '<div class="anonymous-welcome-message">';
        $html[] = Translation::getInstance()->getTranslation('AnonymousWelcomeMessage', null, Manager::context());
        $html[] = '</div>';

        $html[] = '<div class="anonymous-form-container">';

        if ($errorMessage)
        {
            $html[] = '<div class="alert alert-danger anonymous-error-message">' . $errorMessage . '</div>';
        }

        $html[] = '<div class="anonymous-captcha-form">';
        $html[] = $form->toHtml();
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

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
        $recaptchaSecretKey = Configuration::getInstance()->get_setting(
            array('Chamilo\Core\Admin', 'recaptcha_secret_key')
        );

        $recaptcha = new \ReCaptcha\ReCaptcha($recaptchaSecretKey);
        $response = $recaptcha->verify($captchaResponseValue, $this->getRequest()->server->get('REMOTE_ADDR'));

        if (!$response->isSuccess())
        {
            throw new \Exception('Could not verify the captcha code: ' . implode(' / ', $response->getErrorCodes()));
        }
    }

    /**
     * Creates an anonymous user
     *
     * @return User
     *
     * @throws \Exception
     */
    protected function createAnonymousUser()
    {
        $user = new User();
        $user->set_firstname('Anonymous');
        $user->set_lastname('User');
        $user->set_username(uniqid());
        $user->set_email('no-reply@chamilo.org');

        if (!$user->create())
        {
            throw new \Exception('Could not create a new anonymous user');
        }

        return $user;
    }

    /**
     * Adds the anonymous role to the user
     *
     * @param User $user
     */
    protected function addAnonymousRoleToUser(User $user)
    {
        $userRoleService = $this->getUserRoleService();
        $userRoleService->addRoleForUser($user, 'ROLE_ANONYMOUS');
    }

    /**
     * Sets the anonymous authentication cookie
     */
    protected function setAuthenticationCookieAndRedirect(User $user)
    {
        $cookie = new Cookie(md5('anonymous_authentication'), $user->get_security_token());

        $parameters = Session::get('requested_url_parameters');

        if (empty($parameters) || ($parameters[self::PARAM_CONTEXT] == self::context() &&
                $parameters[self::PARAM_ACTION] == self::ACTION_ACCESS_ANONYMOUSLY) ||
            $parameters[self::PARAM_CONTEXT] == 'Chamilo\Core\Home'
        )
        {
            $parameters = array(
                self::PARAM_CONTEXT => Configuration::getInstance()->get_setting(
                    array('Chamilo\Core\Admin', 'page_after_anonymous_access')
                )
            );
        }

        $redirect = new Redirect($parameters);

        $response = new RedirectResponse($redirect->getUrl());
        $response->headers->setCookie($cookie);

        $response->send();

        exit();
    }

    /**
     * Checks whether or not the anonymous access is allowed
     *
     * @return bool
     */
    protected function anonymousAccessAllowed()
    {
        if ($this->getUser() instanceof User)
        {
            return false;
        }

        $anonymousAuthentication = Configuration::getInstance()->get_setting(
            array('Chamilo\Core\Admin', 'enableAnonymousAuthentication')
        );

        if (!$anonymousAuthentication)
        {
            return false;
        }

        $allowedAnonymousAuthenticationUrl = Configuration::getInstance()->get_setting(
            array('Chamilo\Core\Admin', 'anonymous_authentication_url')
        );

        $baseUrl = $this->getRequest()->server->get('SERVER_NAME');

        return strpos($allowedAnonymousAuthenticationUrl, $baseUrl) !== false;
    }

    /**
     *
     * @return UserRoleServiceInterface
     */
    protected function getUserRoleService()
    {
        return $this->getService('chamilo.core.user.roles.service.user_role_service');
    }

    /**
     * @return ConfigurationConsulter
     */
    protected function getConfigurationConsulter()
    {
        return $this->getService('chamilo.configuration.service.configuration_consulter');
    }
}
