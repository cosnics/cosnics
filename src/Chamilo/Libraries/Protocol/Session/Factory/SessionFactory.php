<?php
namespace Chamilo\Libraries\Protocol\Session\Factory;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

/**
 * @package Chamilo\Libraries\Protocol\Session\Factory
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class SessionFactory
{
    public function __construct(protected SessionStorageInterface $sessionStorage, protected ?string $securityKey = null
    )
    {
    }

    public function getSession(): Session
    {
        $session = new Session($this->sessionStorage);

        if (is_null($this->securityKey)) {
            $this->securityKey = 'cosnics_sid';
        }

        $session->setName($this->securityKey);

        return $session;
    }
}

