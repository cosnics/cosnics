<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Redirect;
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
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    function run()
    {
        $authorizationCode = $this->getRequest()->getFromUrl(self::PARAM_AUTHORIZATION_CODE);
        $state = $this->getRequest()->getFromUrl(self::PARAM_AUTHORIZATION_STATE);

        $this->getOffice365Service()->authorizeUserByAuthorizationCode($authorizationCode);


        $decodedState = json_decode(base64_decode($state), true);
        if(!is_array($decodedState) || !array_key_exists('currentUrlParameters', $decodedState))
        {
            throw new NotAllowedException();
        }

        $redirect = new Redirect($decodedState['currentUrlParameters']);
        return new RedirectResponse($redirect->getUrl());
    }
}