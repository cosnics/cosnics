<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Component;

use Chamilo\Libraries\Protocol\Microsoft\Graph\Manager;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AuthenticationComponent extends Manager
{
    const PARAM_AUTHORIZATION_CODE = 'code';
    const PARAM_AUTHORIZATION_STATE = 'state';

    /**
     *
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