<?php
namespace Chamilo\Libraries\Platform\Session;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

/**
 * @package Chamilo\Core\User\Factory
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class SessionFactory
{
    private ?string $securityKey;

    private SessionStorageInterface $sessionStorage;

    public function __construct(SessionStorageInterface $sessionStorage, ?string $securityKey = null)
    {
        $this->sessionStorage = $sessionStorage;
        $this->securityKey = $securityKey;
    }

    public function getSecurityKey(): ?string
    {
        return $this->securityKey;
    }

    public function getSession(): Session
    {
        $session = new Session($this->getSessionStorage());

        $sessionKey = $this->getSecurityKey();

        if (is_null($sessionKey))
        {
            $sessionKey = 'cosnics_sid';
        }

        $session->setName($sessionKey);

        return $session;
    }

    public function getSessionStorage(): SessionStorageInterface
    {
        return $this->sessionStorage;
    }
}

