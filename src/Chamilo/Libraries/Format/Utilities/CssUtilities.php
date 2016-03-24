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
        $stylesheetCacheService = new StylesheetCacheService($this->getPathUtilities(), $this->getThemeUtilities());

        $response = new Response();
        $response->setContent($stylesheetCacheService->get());
        $response->headers->set('Content-Type', 'text/css');
        $response->send();
        exit();
    }
}
