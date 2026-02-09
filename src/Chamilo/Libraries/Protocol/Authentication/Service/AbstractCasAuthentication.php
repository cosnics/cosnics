<?php
namespace Chamilo\Libraries\Protocol\Authentication\Service;

use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\AuthenticationException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\AuthenticationInterface;
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
        Translator $translator, ChamiloRequest $request, UserService $userService,
        AuthenticationValidator $authenticationValidator, SessionInterface $session, Logger $logger, array $settings
    )
    {
        parent::__construct($translator, $request, $userService, $authenticationValidator);

        $this->session = $session;
        $this->logger = $logger;
        $this->settings = $settings;
    }

    abstract protected function getCasUserIdentifierFromAttributes(string $casUser, array $casUserAttributes = []
    ): string;

    abstract public function getPriority(): int;

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    protected function getSetting(string $variable): ?string
    {
        return array_key_exists($variable, $this->settings) ? $this->settings[$variable] : null;
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
            $request = $this->getRequest();

            // initialize phpCAS
            if ($this->getSetting('enableLog')) {
                phpCAS::setLogger($this->logger);
            }

            phpCAS::client(
                SAML_VERSION_1_1, $this->getSetting('host'), $this->getSetting('port'), $this->getSetting('uri'),
                $request->getSchemeAndHttpHost(), false
            );

            // SSL validation for the CAS server
            if ($this->getSetting('checkCertificate')) {
                phpCAS::setCasServerCACert($this->getSetting('certificatePath'));
            }
            else {
                phpCAS::setNoCasServerValidation();
            }
        }
    }

    protected function isConfigured(): bool
    {
        if (!$this->getSetting('host')) {
            return false;
        }

        if ($this->getSetting('enableLog') && !$this->getSetting('logPath')) {
            return false;
        }

        if ($this->getSetting('checkCertificate') && !$this->getSetting('certificatePath')) {
            return false;
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\AuthenticationException
     * @throws \Exception
     */
    public function login(bool $checkIfAuthenticationSourceIsEnabled = true): ?User
    {
        $this->checkAuthenticationSource($checkIfAuthenticationSourceIsEnabled);
        $this->initializeClient();

        $authenticationException = new AuthenticationException($this->getTranslator()->trans('CasAuthenticationError'));

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
