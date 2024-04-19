<?php
namespace Chamilo\Core\API\Component;

use Chamilo\Core\API\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TestComponent extends Manager
{
    const PARAM_ID = 'id';

    function run(): JsonResponse
    {
        if($this->getRequest()->attributes->get('oauth_client_id') != 'sven')
            throw new NotAllowedException();

        return new JsonResponse(['id' => $this->get_parameter(self::PARAM_ID)]);
    }


}
