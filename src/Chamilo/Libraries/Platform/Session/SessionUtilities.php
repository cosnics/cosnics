<?php
namespace Chamilo\Libraries\Platform\Session;

use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @package Chamilo\Libraries\Platform\Session
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class SessionUtilities
{
    private ?string $securityKey;

    private Session $session;

    public function __construct(Session $session, ?string $securityKey = null)
    {
        $this->session = $session;
        $this->securityKey = $securityKey;
    }

    public function clear()
    {
        $this->getSession()->clear();
    }

    public function destroy()
    {
        $this->getSession()->invalidate();
    }

    public function exists(string $variable): bool
    {
        return $this->getSession()->has($variable);
    }

    public function get(string $variable, $default = null)
    {
        $session = $this->getSession();

        if ($session->has($variable))
        {
            return $session->get($variable);
        }
        else
        {
            return $default;
        }
    }

    public function getSecurityKey(): ?string
    {
        return $this->securityKey;
    }

    public function getSession(): Session
    {
        return $this->session;
    }

    public function getUserId(): ?int
    {
        return $this->retrieve('_uid');
    }

    /**
     * @deprecated Use SessionUtilities::getUserId() now
     */
    public function get_user_id(): ?int
    {
        return $this->getUserId();
    }

    public function register(string $variable, $value)
    {
        $this->getSession()->set($variable, $value);
    }

    public function registerIfNotSet(string $variable, $value)
    {
        $sessionValue = $this->retrieve($variable);

        if (is_null($sessionValue))
        {
            $this->register($variable, $value);
        }
    }

    public function retrieve(string $variable)
    {
        $session = $this->getSession();

        if ($session->has($variable))
        {
            return $session->get($variable);
        }

        return null;
    }

    public function setSecurityKey(?string $securityKey)
    {
        $this->securityKey = $securityKey;
    }

    public function start()
    {
        $sessionKey = $this->getSecurityKey();

        if (is_null($sessionKey))
        {
            $sessionKey = 'dk_sid';
        }

        $session = $this->getSession();
        $session->setName($sessionKey);
        $session->start();
    }

    public function unregister(string $variable)
    {
        $session = $this->getSession();

        if ($session->has($variable))
        {
            $session->remove($variable);
        }
    }
}
