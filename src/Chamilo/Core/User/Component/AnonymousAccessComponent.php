<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Form\AnonymousUserForm;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Roles\Service\Interfaces\UserRoleServiceInterface;
use Chamilo\Core\User\Roles\Service\UserRoleService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Format\Structure\PageConfiguration;
use Exception;
use ReCaptcha\ReCaptcha;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Landing page for anonymous users to start using Chamilo anonymously, requires an
 */
class AnonymousAccessComponent extends Manager implements NoAuthenticationSupportInterface
{

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        if (!$this->anonymousAccessAllowed())
        {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();
        $form = new AnonymousUserForm($this->get_url());
        $errorMessage = null;

        if ($form->validate())
        {
            try
            {
                $this->validateCaptcha(
                    $this->getRequest()->getFromRequestOrQuery(AnonymousUserForm::CAPTCHA_RESPONS_VALUE)
                );

                $anonymousUser = $this->createAnonymousUser();

                $this->addAnonymousRoleToUser($anonymousUser);
                $this->setAuthenticationCookieAndRedirect($anonymousUser);
            }
            catch (Exception)
            {
                $errorMessage = $translator->trans('UseCaptchaToProceed', [], Manager::CONTEXT);
            }
        }

        $this->getPageConfiguration()->setViewMode(PageConfiguration::VIEW_MODE_HEADERLESS);

        $html = [];

        $html[] = $this->renderHeader(' ');

        $html[] = '<div class="anonymous-page">';

        $html[] = '<div class="panel anonymous-container lead text-justify text-muted"">';
        $html[] = '<div class="panel-body">';

        $html[] = '<h1>';
        $html[] = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'institution']);
        $html[] = '</h1>';

        $html[] = '<div class="anonymous-welcome-message">';
        $html[] = $translator->trans('AnonymousWelcomeMessage', [], Manager::CONTEXT);
        $html[] = '</div>';

        $html[] = '<div class="anonymous-form-container">';

        if ($errorMessage)
        {
            $html[] = '<div class="alert alert-danger anonymous-error-message">' . $errorMessage . '</div>';
        }

        $html[] = '<div class="anonymous-captcha-form">';
        $html[] = $form->render();
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Exception
     */
    protected function addAnonymousRoleToUser(User $user): void
    {
        $userRoleService = $this->getUserRoleService();
        $userRoleService->addRoleForUser($user, 'ROLE_ANONYMOUS');
    }

    protected function anonymousAccessAllowed(): bool
    {
        if ($this->getUser() instanceof User)
        {
            return false;
        }

        $configurationConsulter = $this->getConfigurationConsulter();

        $anonymousAuthentication = $configurationConsulter->getSetting(
            ['Chamilo\Core\Admin', 'enableAnonymousAuthentication']
        );

        if (!$anonymousAuthentication)
        {
            return false;
        }

        $allowedAnonymousAuthenticationUrl = $configurationConsulter->getSetting(
            ['Chamilo\Core\Admin', 'anonymous_authentication_url']
        );

        $baseUrl = $this->getRequest()->server->get('SERVER_NAME');

        return str_contains($allowedAnonymousAuthenticationUrl, $baseUrl);
    }

    /**
     * @throws \Exception
     */
    protected function createAnonymousUser(): User
    {
        $user = new User();

        $user->set_firstname('Anonymous');
        $user->set_lastname('User');
        $user->set_username(uniqid());
        $user->set_email('no-reply@chamilo.org');

        if (!$this->getUserService()->createUser($user))
        {
            throw new Exception('Could not create a new anonymous user');
        }

        return $user;
    }

    protected function getUserRoleService(): UserRoleServiceInterface
    {
        return $this->getService(UserRoleService::class);
    }

    protected function setAuthenticationCookieAndRedirect(User $user): void
    {
        $cookie = new Cookie(md5('anonymous_authentication'), $user->get_security_token());

        $parameters = $this->getSession()->get('requested_url_parameters');

        if (empty($parameters) || ($parameters[self::PARAM_CONTEXT] == Manager::CONTEXT &&
                $parameters[self::PARAM_ACTION] == self::ACTION_ACCESS_ANONYMOUSLY) ||
            $parameters[self::PARAM_CONTEXT] == 'Chamilo\Core\Home')
        {
            $parameters = [
                self::PARAM_CONTEXT => $this->getConfigurationConsulter()->getSetting(
                    ['Chamilo\Core\Admin', 'page_after_anonymous_access']
                )
            ];
        }

        $response = new RedirectResponse($this->getUrlGenerator()->fromParameters($parameters));
        $response->headers->setCookie($cookie);

        $response->send();
        exit();
    }

    /**
     * @throws \Exception
     */
    protected function validateCaptcha(string $captchaResponseValue): void
    {
        $recaptchaSecretKey = $this->getConfigurationConsulter()->getSetting(
            ['Chamilo\Core\Admin', 'recaptcha_secret_key']
        );

        $recaptcha = new ReCaptcha($recaptchaSecretKey);
        $response = $recaptcha->verify($captchaResponseValue, $this->getRequest()->server->get('REMOTE_ADDR'));

        if (!$response->isSuccess())
        {
            throw new Exception('Could not verify the captcha code: ' . implode(' / ', $response->getErrorCodes()));
        }
    }
}
