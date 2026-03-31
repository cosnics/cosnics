<?php
namespace Chamilo\Libraries\Protocol\Security\Factory;

use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;

/**
 * @package Chamilo\Libraries\Protocol\Security\Factory
 * @author - Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CsrfTokenManagerFactory
{
    public function __construct(protected ChamiloRequest $request)
    {
    }

    /**
     * @return CsrfTokenManagerInterface
     */
    public function buildCsrfTokenManager(): CsrfTokenManagerInterface
    {
        $requestStack = new RequestStack([$this->request]);

        $csrfGenerator = new UriSafeTokenGenerator();
        $csrfStorage = new SessionTokenStorage($requestStack);

        return new CsrfTokenManager($csrfGenerator, $csrfStorage);
    }
}
