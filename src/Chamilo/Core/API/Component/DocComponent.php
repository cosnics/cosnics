<?php
namespace Chamilo\Core\API\Component;

use Chamilo\Core\API\Manager;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DocComponent extends Manager implements NoAuthenticationSupport
{
    function run(): Response
    {
        $basePath = $this->getPathBuilder()->getBasePath(true);
        return new Response($this->getTwig()->render(Manager::context() . ':SwaggerUI.html.twig', ['BASE_PATH' => $basePath]));
    }


}
