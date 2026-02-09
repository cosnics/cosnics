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
    protected ?string $certificatePath;

    protected bool $checkCertificate;

    protected bool $enableLog;

    protected string $host;

    protected ?string $logPath;

    protected Logger $logger;

    protected int $port;

    protected SessionInterface $session;

    protected string $uri;

    public function __construct(
        Translator $translator, ChamiloRequest $request, UserService $userService,
        AuthenticationValidator $authenticationValidator, SessionInterface $session, Logger $logger,
        string $host = '', bool $enableLog = false, bool $checkCertificate = false, ?string $certificatePath = null,
        ?string $logPath = null, int $port = 443, string $uri = ''
    )
    {
        parent::__construct($translator, $request, $userService, $authenticationValidator);

        $this->session = $session;
        $this->logger = $logger;
        $this->host = $host;
        $this->enableLog = $enableLog;
        $this->checkCertificate = $checkCertificate;
        $this->certificatePath = $certificatePath;
        $this->logPath = $logPath;
        $this->port = $port;
        $this->uri = $uri;
    }

    abstract protected function getCasUserIdentifierFromAttributes(string $casUser, array $casUserAttributes = []
    ): string;

    public function getCertificatePath(): ?string
    {
        return $this->certificatePath;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getLogPath(): ?string
    {
        return $this->logPath;
    }

    public function getLogger(): Logger
    {
        return $this->logger;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    abstract public function getPriority(): int;

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function getUri(): ?string
    {
        return $this->uri;
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
            if ($this->isLogEnabled()) {
                phpCAS::setLogger($this->logger);
            }

            phpCAS::client(
                SAML_VERSION_1_1, $this->getHost(), $this->getPort(), $this->getUri(), $request->getSchemeAndHttpHost(),
                false
            );

            // SSL validation for the CAS server
            if ($this->isCertificateCheckEnabled()) {
                phpCAS::setCasServerCACert($this->getCertificatePath());
            }
            else {
                phpCAS::setNoCasServerValidation();
            }
        }
    }

    public function isCertificateCheckEnabled(): bool
    {
        return $this->checkCertificate;
    }

    protected function isConfigured(): bool
    {
        if (!$this->getHost()) {
            return false;
        }

        if ($this->isLogEnabled() && !$this->getLogPath()) {
            return false;
        }

        if ($this->isCertificateCheckEnabled() && !$this->getCertificatePath()) {
            return false;
        }

        return true;
    }

    public function isLogEnabled(): bool
    {
        return $this->enableLog;
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
