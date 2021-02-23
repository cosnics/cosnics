<?php

namespace Chamilo\Libraries\Platform\Security\Csrf;

use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;

/**
 * Class CsrfTokenManagerFactory
 * @package Chamilo\Libraries\Platform\Security\Csrf
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class CsrfTokenManagerFactory
{
    /**
     * @var SessionUtilities
     */
    protected $sessionUtilities;

    /**
     * CsrfTokenManagerFactory constructor.
     *
     * @param SessionUtilities $sessionUtilities
     */
    public function __construct(SessionUtilities $sessionUtilities)
    {
        $this->sessionUtilities = $sessionUtilities;
    }

    /**
     * @return CsrfTokenManagerInterface
     */
    public function buildCsrfTokenManager()
    {
        return new CsrfTokenManager(
            new UriSafeTokenGenerator(), new SessionUtilitiesTokenStorage($this->sessionUtilities)
        );
    }
}
