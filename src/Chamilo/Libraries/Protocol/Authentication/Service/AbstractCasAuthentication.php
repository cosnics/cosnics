<?php
namespace Chamilo\Libraries\Protocol\Authentication\Service;

use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\AuthenticationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Trait\DefaultRedirectAfterLoginTrait;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
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
    use DefaultRedirectAfterLoginTrait;

    public function __construct(
        Translator $translator, ChamiloRequest $request, UserService $userService,
        AuthenticationValidator $authenticationValidator, protected SessionInterface $session, protected Logger $logger,
        protected UrlGenerator $urlGenerator, protected string $host = '', protected bool $enableLog = false,
        protected bool $checkCertificate = false, protected ?string $certificatePath = null,
        protected ?string $logPath = null, protected int $port = 443, protected string $uri = ''
    )
    {
        parent::__construct($translator, $request, $userService, $authenticationValidator);
    }

    abstract protected function getCasUserIdentifierFromAttributes(string $casUser, array $casUserAttributes = []
    ): string;

    abstract public function getPriority(): int;

    abstract protected function getUserByCasUserIdentifier(string $userIdentifier): ?User;

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException
     */
    protected function initializeClient(): void
    {
        if (!$this->isConfigured()) {
            throw new NotAuthenticatedException($this->translator->trans('CheckCASConfiguration'));
        }
        elseif (!phpCAS::isInitialized()) {
            // initialize phpCAS
            if ($this->isLogEnabled()) {
                phpCAS::setLogger($this->logger);
            }

            phpCAS::client(
                SAML_VERSION_1_1, $this->host, $this->port, $this->uri, $this->request->getSchemeAndHttpHost(), false
            );

            // SSL validation for the CAS server
            if ($this->isCertificateCheckEnabled()) {
                phpCAS::setCasServerCACert($this->certificatePath);
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
        if (!$this->host) {
            return false;
        }

        if ($this->isLogEnabled() && !$this->logPath) {
            return false;
        }

        if ($this->isCertificateCheckEnabled() && !$this->certificatePath) {
            return false;
        }

        return true;
    }

    public function isLogEnabled(): bool
    {
        return $this->enableLog;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException
     */
    public function login(bool $checkIfAuthenticationSourceIsEnabled = true): ?User
    {
        $this->checkAuthenticationSource($checkIfAuthenticationSourceIsEnabled);
        $this->initializeClient();

        $authenticationException = new NotAuthenticatedException(
            $this->translator->trans('CasAuthenticationError', [], StringUtilities::LIBRARIES)
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
                    $surrogateUser = $this->userService->findUserByUsername($surrogateUserName);
                    $this->session->set(AuthenticationValidator::PARAM_AS_ADMIN, $surrogateUser->getId());
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
     * @throws \Exception
     */
    abstract protected function registerUser(string $casUser, array $casUserAttributes = []): User;
}
