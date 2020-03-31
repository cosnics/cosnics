<?php
namespace Chamilo\Libraries\Architecture\Resource;

use Chamilo\Libraries\Cache\Assetic\StylesheetCacheService;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Libraries\Architecture\Resource
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StylesheetGenerator
{

    /**
     * @var \Chamilo\Libraries\Cache\Assetic\StylesheetCacheService
     */
    private $stylesheetCacheService;

    /**
     *
     * @param \Chamilo\Libraries\Cache\Assetic\StylesheetCacheService $stylesheetCacheService
     */
    public function __construct(StylesheetCacheService $stylesheetCacheService)
    {
        $this->stylesheetCacheService = $stylesheetCacheService;
    }

    /**
     * @param string $theme
     */
    public function run(string $theme)
    {
        $response = new Response();
        $stylesheetCacheService = $this->getStylesheetCacheService();
        $stylesheetCacheService->getThemePathBuilder()->setTheme($theme);

        $response->setPublic();
        // 24 hours cache
        $response->setMaxAge(3600 * 24);
        $response->headers->set('Content-Type', 'text/css');
        $response->setContent($stylesheetCacheService->get());
        $response->send();

        exit();
    }

    /**
     * @return \Chamilo\Libraries\Cache\Assetic\StylesheetCacheService
     */
    public function getStylesheetCacheService(): StylesheetCacheService
    {
        return $this->stylesheetCacheService;
    }

    /**
     * @param \Chamilo\Libraries\Cache\Assetic\StylesheetCacheService $stylesheetCacheService
     */
    public function setStylesheetCacheService(StylesheetCacheService $stylesheetCacheService): void
    {
        $this->stylesheetCacheService = $stylesheetCacheService;
    }
}
