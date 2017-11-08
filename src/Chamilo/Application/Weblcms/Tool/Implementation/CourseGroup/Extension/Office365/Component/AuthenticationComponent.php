<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Manager;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AuthenticationComponent extends Manager
{
    const PARAM_AUTHORIZATION_CODE = 'code';
    const PARAM_AUTHORIZATION_STATE = 'state';

    /**
     * @return string
     */
    function run()
    {
        $authorizationCode = $this->getRequest()->getFromUrl(self::PARAM_AUTHORIZATION_CODE);
        $state = $this->getRequest()->getFromUrl(self::PARAM_AUTHORIZATION_STATE);

        $this->getOffice365Service()->authorizeUserByAuthorizationCode($authorizationCode);

        $currentRequestUrl = base64_decode($state);
        return new RedirectResponse($currentRequestUrl);
    }
}