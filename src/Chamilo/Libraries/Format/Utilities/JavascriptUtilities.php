<?php
namespace Chamilo\Libraries\Format\Utilities;

use Chamilo\Libraries\Cache\Assetic\JavascriptCacheService;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * @package Chamilo\Libraries\Format\Utilities
 * @author Laurent Opprecht
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class JavascriptUtilities extends ResourceUtilities
{

    public function run()
    {
        $javascriptCacheService = new JavascriptCacheService(
            $this->getPathBuilder(),
            $this->getConfigurablePathBuilder());

        $response = new Response();

        $response->headers->set('Content-Type', 'text/javascript');

        $response->setPublic();
        $response->setMaxAge(3600 * 24); // 24 hours cache
        $response->setContent($javascriptCacheService->get());
        $response->send();

        exit();
    }
}
