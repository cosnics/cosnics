<?php
namespace Chamilo\Libraries\Protocol\Authentication\Service;

use Chamilo\Core\Admin\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\AuthenticationException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\AuthenticationInterface;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Exception;
use Monolog\Logger;
use phpCAS;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Protocol\Authentication\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class AbstractCasAuthentication extends Authentication implements AuthenticationInterface
{
    protected Logger $logger;

    protected SessionInterface $session;

    /**
     * @var string[]
     */
    protected array $settings;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, Translator $translator, ChamiloRequest $request,
        UserService $userService, SessionInterface $session, Logger $logger
    )
    {
        parent::__construct($configurationConsulter, $translator, $request, $userService);

        $this->session = $session;
        $this->logger = $logger;
    }

    abstract protected function getCasUserIdentifierFromAttributes(string $casUser, array $casUserAttributes = []
    ): string;

    /**
     * @return string[]
     */
    protected function getConfiguration(): array
    {
        if (!isset($this->settings)) {
            $this->settings = [];
            $this->settings['host'] = $this->configurationConsulter->getSetting(['Chamilo\Libraries', 'cas_host']);
            $this->settings['port'] = $this->configurationConsulter->getSetting(['Chamilo\Libraries', 'cas_port']);
            $this->settings['uri'] = $this->configurationConsulter->getSetting(['Chamilo\Libraries', 'cas_uri']);
            $this->settings['certificate'] = $this->configurationConsulter->getSetting(
                ['Libraries', 'cas_certificate']
            );
            $this->settings['log'] = $this->configurationConsulter->getSetting(['Chamilo\Libraries', 'cas_log']);
            $this->settings['enable_log'] = $this->configurationConsulter->getSetting(
                ['Chamilo\Libraries', 'cas_enable_log']
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
        if (!$this->isConfigured()) {
            throw new Exception($this->getTranslator()->trans('CheckCASConfiguration'));
        }
        elseif (!phpCAS::isInitialized()) {
            $settings = $this->getConfiguration();
            $request = $this->getRequest();

            // initialize phpCAS
            if ($settings['enable_log']) {
                phpCAS::setLogger($this->logger);
            }

            $configurationConsulter = $this->getConfigurationConsulter();

            $casVersion = $configurationConsulter->getSetting(['Libraries', 'cas_version']);

            if ($casVersion == 'SAML_VERSION_1_1') {
                phpCAS::client(
                    SAML_VERSION_1_1, $settings['host'], (int) $settings['port'], $settings['uri'],
                    $request->getSchemeAndHttpHost(), false
                );
            }
            else {
                phpCAS::client(
                    CAS_VERSION_2_0, $settings['host'], (int) $settings['port'], $settings['uri'],
                    $request->getSchemeAndHttpHost(), false
                );
            }

            $casCheckCertificate = $configurationConsulter->getSetting(
                ['Libraries', 'cas_check_certificate']
            );

            // SSL validation for the CAS server
            if ($casCheckCertificate == '1') {
                phpCAS::setCasServerCACert($settings['certificate']);
            }
            else {
                phpCAS::setNoCasServerValidation();
            }
        }
    }

    protected function isConfigured(): bool
    {
        $settings = $this->getConfiguration();

        foreach ($settings as $setting => $value) {
            if (empty($value) && !in_array(
                    $setting, ['uri', 'certificate', 'log', 'enable_log']
                )) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\AuthenticationException
     * @throws \Exception
     */
    public function login(): ?User
    {
        if (!$this->isAuthSourceActive()) {
            return null;
        }

        $this->initializeClient();
        $configurationConsulter = $this->getConfigurationConsulter();

        $externalAuthenticationEnabled = $configurationConsulter->getSetting(
            ['Chamilo\Libraries', 'enableExternalAuthentication']
        );

        $bypassExternalAuthentication = (boolean) $this->getRequest()->query->get('noExtAuth', false);

        if (!$externalAuthenticationEnabled || $bypassExternalAuthentication) {
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

        try {
            phpCAS::forceAuthentication();

            $userAttributes = phpCAS::getAttributes();
            $userIdentifier = $this->getCasUserIdentifierFromAttributes(phpCAS::getUser(), $userAttributes);

            if ($userIdentifier) {
                $user = $this->getUserByCasUserIdentifier($userIdentifier);

                if (!$user instanceof User) {
                    $user = $this->registerUser(phpCAS::getUser(), $userAttributes);
                }

                if ($userAttributes && isset($userAttributes['surrogatePrincipal'])) {
                    $surrogateUserName = array_pop($userAttributes['surrogatePrincipal']);
                    $surrogateUser = $this->getUserService()->findUserByUsername($surrogateUserName);
                    $this->getSession()->set(AuthenticationValidator::PARAM_AS_ADMIN, $surrogateUser->getId());
                }

                return $user;
            }
            else {
                throw $authenticationException;
            }
        }
        catch (Exception) {
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
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\AuthenticationException
     * @throws \Exception
     */
    abstract protected function registerUser(string $casUser, array $casUserAttributes = []): User;
}
