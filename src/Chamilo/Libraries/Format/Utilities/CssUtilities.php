<?php
namespace Chamilo\Libraries\Format\Utilities;

use Chamilo\Libraries\Cache\Assetic\StylesheetCacheService;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * @package libraries
 * @author Laurent Opprecht
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CssUtilities extends ResourceUtilities
{

    public function run()
    {
        $stylesheetCacheService = new StylesheetCacheService(
            $this->getPathBuilder(),
            $this->getConfigurablePathBuilder(),
            $this->getThemeUtilities());

        $response = new Response();
        $response->setContent($stylesheetCacheService->get());
        $response->setPublic();
        $response->setMaxAge(3600 * 24); // 24 hours cache
        $response->headers->set('Content-Type', 'text/css');
        $response->send();
        exit();
    }
}
