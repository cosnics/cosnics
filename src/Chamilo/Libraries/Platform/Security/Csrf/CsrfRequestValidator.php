<?php

namespace Chamilo\Libraries\Platform\Security\Csrf;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Class CsrfRequestValidator
 * @package Chamilo\Libraries\Platform\Security\Csrf
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class CsrfRequestValidator
{
    const COMPONENT_TOKEN_ID = 'ComponentToken';
    const PARAM_CSRF_TOKEN = '_csrf_token';

    /**
     * @var CsrfTokenManagerInterface
     */
    protected $csrfTokenManagerInterface;

    /**
     * CsrfRequestValidator constructor.
     *
     * @param CsrfTokenManagerInterface $csrfTokenManagerInterface
     */
    public function __construct(CsrfTokenManagerInterface $csrfTokenManagerInterface)
    {
        $this->csrfTokenManagerInterface = $csrfTokenManagerInterface;
    }

    /**
     * @param Application $application
     *
     * @throws NotAllowedException
     */
    public function validateRequestForApplication(Application $application)
    {
        if(!$application instanceof CsrfComponentInterface && $application instanceof Application)
        {
            return;
        }

        $this->validateRequest($application->getRequest());
    }

    /**
     * @param ChamiloRequest $request
     *
     * @throws NotAllowedException
     */
    public function validateRequest(ChamiloRequest $request)
    {
        $csrfTokenFromRequest = $request->getFromPostOrUrl(self::PARAM_CSRF_TOKEN);
        $tokenObject = new CsrfToken(self::COMPONENT_TOKEN_ID, $csrfTokenFromRequest);

        if(!$this->csrfTokenManagerInterface->isTokenValid($tokenObject))
        {
            throw new NotAllowedException();
        }
    }
}
