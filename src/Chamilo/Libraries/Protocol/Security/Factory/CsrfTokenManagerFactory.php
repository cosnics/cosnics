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
    protected ChamiloRequest $request;

    public function __construct(ChamiloRequest $request)
    {
        $this->request = $request;
    }

    /**
     * @return CsrfTokenManagerInterface
     */
    public function buildCsrfTokenManager(): CsrfTokenManagerInterface
    {
        $requestStack = new RequestStack([$this->getRequest()]);

        $csrfGenerator = new UriSafeTokenGenerator();
        $csrfStorage = new SessionTokenStorage($requestStack);
        $csrfManager = new CsrfTokenManager($csrfGenerator, $csrfStorage);

        return new CsrfTokenManager(
            new UriSafeTokenGenerator(), new SessionTokenStorage($requestStack)
        );
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }
}
