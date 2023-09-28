<?php
namespace Chamilo\Libraries\Authentication\Cas;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\Authentication\AuthenticationException;
use Chamilo\Libraries\Authentication\AuthenticationInterface;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;
use phpCAS;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Authentication\Cas
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class AbstractCasAuthentication extends Authentication implements AuthenticationInterface
{

    protected SessionInterface $session;

    /**
     * @var string[]
     */
    protected array $settings;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, Translator $translator, ChamiloRequest $request,
        UserService $userService, SessionInterface $session
    )
    {
        parent::__construct($configurationConsulter, $translator, $request, $userService);

        $this->session = $session;
    }

    abstract public function getAuthenticationType(): string;

    abstract protected function getCasUserIdentifierFromAttributes(string $casUser, array $casUserAttributes = []
    ): string;

    /**
     * @return string[]
     */
    protected function getConfiguration(): array
    {
        if (!isset($this->settings))
        {
            $this->settings = [];
            $this->settings['host'] = $this->configurationConsulter->getSetting(['Chamilo\Core\Admin', 'cas_host']);
            $this->settings['port'] = $this->configurationConsulter->getSetting(['Chamilo\Core\Admin', 'cas_port']);
            $this->settings['uri'] = $this->configurationConsulter->getSetting(['Chamilo\Core\Admin', 'cas_uri']);
            $this->settings['certificate'] = $this->configurationConsulter->getSetting(
                ['Chamilo\Core\Admin', 'cas_certificate']
            );
            $this->settings['log'] = $this->configurationConsulter->getSetting(['Chamilo\Core\Admin', 'cas_log']);
            $this->settings['enable_log'] = $this->configurationConsulter->getSetting(
                ['Chamilo\Core\Admin', 'cas_enable_log']
            );
        }

        return $this->settings;
    }

    abstract public function getPriority(): int;

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    abstract protected function getUserByCasUserIdentifier(string $userIdentifier): ?User;

    /**
     * @throws \Exception
     */
    protected function initializeClient(): void
    {

        if (!$this->isConfigured())
        {
            throw new Exception($this->getTranslator()->trans('CheckCASConfiguration'));
        }
        elseif (!phpCAS::isInitialized())
        {
            $settings = $this->getConfiguration();
            $request = $this->getRequest();

            // initialize phpCAS
            if ($settings['enable_log'])
            {
                phpCAS::setDebug($settings['log']);
            }

            $configurationConsulter = $this->getConfigurationConsulter();

            $casVersion = $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'cas_version']);

            if ($casVersion == 'SAML_VERSION_1_1')
            {
                phpCAS::client(
                    SAML_VERSION_1_1, $settings['host'], (int) $settings['port'], $settings['uri'],
                    $request->getSchemeAndHttpHost(), false
                );
            }
            else
            {
                phpCAS::client(
                    CAS_VERSION_2_0, $settings['host'], (int) $settings['port'], $settings['uri'],
                    $request->getSchemeAndHttpHost(), false
                );
            }

            $casCheckCertificate = $configurationConsulter->getSetting(
                ['Chamilo\Core\Admin', 'cas_check_certificate']
            );

            // SSL validation for the CAS server
            if ($casCheckCertificate == '1')
            {
                phpCAS::setCasServerCACert($settings['certificate']);
            }
            else
            {
                phpCAS::setNoCasServerValidation();
            }
        }
    }

    protected function isConfigured(): bool
    {
        $settings = $this->getConfiguration();

        foreach ($settings as $setting => $value)
        {
            if (empty($value) && !in_array(
                    $setting, ['uri', 'certificate', 'log', 'enable_log']
                ))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     * @throws \Exception
     */
    public function login(): ?User
    {

        if (!$this->isAuthSourceActive())
        {
            return null;
        }

        $this->initializeClient();
        $configurationConsulter = $this->getConfigurationConsulter();

        $externalAuthenticationEnabled = $configurationConsulter->getSetting(
            ['Chamilo\Core\Admin', 'enableExternalAuthentication']
        );

        $bypassExternalAuthentication = (boolean) $this->getRequest()->query->get('noExtAuth', false);

        if (!$externalAuthenticationEnabled || $bypassExternalAuthentication)
        {
            return null;
        }

        $authenticationException = new AuthenticationException(
            $this->getTranslator()->trans(
                'CasAuthenticationError', [
                'PLATFORM' => $configurationConsulter->getSetting(
                    ['Chamilo\Core\Admin', 'site_name']
                )
            ], StringUtilities::LIBRARIES
            )
        );

        try
        {
            phpCAS::forceAuthentication();

            $userAttributes = phpCAS::getAttributes();
            $userIdentifier = $this->getCasUserIdentifierFromAttributes(phpCAS::getUser(), $userAttributes);

            if ($userIdentifier)
            {
                $user = $this->getUserByCasUserIdentifier($userIdentifier);

                if (!$user instanceof User)
                {
                    $user = $this->registerUser(phpCAS::getUser(), $userAttributes);
                }

                if ($userAttributes && isset($userAttributes['surrogatePrincipal']))
                {
                    $surrogateUserName = array_pop($userAttributes['surrogatePrincipal']);
                    $surrogateUser = $this->getUserService()->findUserByUsername($surrogateUserName);
                    $this->getSession()->set('_as_admin', $surrogateUser->getId());
                }

                return $user;
            }
            else
            {
                throw $authenticationException;
            }
        }
        catch (Exception)
        {
            throw $authenticationException;
        }
    }

    /**
     * @throws \Exception
     */
    public function logout(User $user): void
    {
        $this->initializeClient();

        phpCAS::logout();
    }

    /**
     * @param string $casUser
     * @param string[] $casUserAttributes
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     * @throws \Exception
     */
    abstract protected function registerUser(string $casUser, array $casUserAttributes = []): User;
}
