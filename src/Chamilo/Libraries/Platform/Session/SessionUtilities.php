<?php
namespace Chamilo\Libraries\Platform\Session;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @package    Chamilo\Libraries\Platform\Session
 * @author     Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author     Magali Gillard <magali.gillard@ehb.be>
 * @deprecated Use \Symfony\Component\HttpFoundation\Session\Session
 */
class SessionUtilities
{
    private ?string $securityKey;

    private SessionInterface $session;

    public function __construct(SessionInterface $session, ?string $securityKey = null)
    {
        $this->session = $session;
        $this->securityKey = $securityKey;
    }

    /**
     * @deprecated Use \Symfony\Component\HttpFoundation\Session\Session::clear()
     */
    public function clear(): void
    {
        $this->getSession()->clear();
    }

    /**
     * @deprecated Use \Symfony\Component\HttpFoundation\Session\Session::invalidate()
     */
    public function destroy(): void
    {
        $this->getSession()->invalidate();
    }

    /**
     * @deprecated Use \Symfony\Component\HttpFoundation\Session\Session::has($variable)
     */
    public function exists(string $variable): bool
    {
        return $this->getSession()->has($variable);
    }

    /**
     * @deprecated Use \Symfony\Component\HttpFoundation\Session\Session::get($variable, $default)
     */
    public function get(string $variable, $default = null)
    {
        return $this->getSession()->get($variable, $default);
    }

    protected function getSecurityKey(): ?string
    {
        return $this->securityKey;
    }

    protected function getSession(): SessionInterface
    {
        return $this->session;
    }

    /**
     * @deprecated Use
     *             \Symfony\Component\HttpFoundation\Session\Session::get(\Chamilo\Core\User\Manager::SESSION_USER_IO)
     */
    public function getUserId(): ?int
    {
        return $this->retrieve('_uid');
    }

    /**
     * @deprecated Use \Symfony\Component\HttpFoundation\Session\Session::set($variable, $value)
     */
    public function register(string $variable, $value): void
    {
        $this->getSession()->set($variable, $value);
    }

    public function registerIfNotSet(string $variable, $value): void
    {
        $sessionValue = $this->retrieve($variable);

        if (is_null($sessionValue))
        {
            $this->register($variable, $value);
        }
    }

    /**
     * @deprecated Use \Symfony\Component\HttpFoundation\Session\Session::get($variable)
     */
    public function retrieve(string $variable)
    {
        return $this->getSession()->get($variable);
    }

    /**
     * @deprecated Use \Symfony\Component\HttpFoundation\Session\Session::start()
     */
    public function start(): void
    {
        $this->getSession()->start();
    }

    /**
     * @deprecated Use \Symfony\Component\HttpFoundation\Session\Session::remove($variable)
     */
    public function unregister(string $variable): void
    {
        $this->getSession()->remove($variable);
    }
}
