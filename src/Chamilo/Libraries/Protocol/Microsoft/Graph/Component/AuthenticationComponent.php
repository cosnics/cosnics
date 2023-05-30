<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Component;

use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Manager;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Component
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AuthenticationComponent extends Manager
{
    public const PARAM_AUTHORIZATION_CODE = 'code';
    public const PARAM_AUTHORIZATION_STATE = 'state';

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        $authorizationCode = $this->getRequest()->getFromQuery(self::PARAM_AUTHORIZATION_CODE);
        $state = $this->getRequest()->getFromQuery(self::PARAM_AUTHORIZATION_STATE);

        $this->getGraphService()->authorizeUserByAuthorizationCode($authorizationCode);

        $decodedState = json_decode(base64_decode($state), true);

        if (!is_array($decodedState) || !array_key_exists('currentUrlParameters', $decodedState))
        {
            throw new NotAllowedException();
        }

        return new RedirectResponse($this->getUrlGenerator()->fromParameters($decodedState['currentUrlParameters']));
    }
}