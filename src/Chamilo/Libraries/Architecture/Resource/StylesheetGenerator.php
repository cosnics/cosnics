<?php
namespace Chamilo\Libraries\Architecture\Resource;

use Chamilo\Libraries\Cache\Assetic\StylesheetCommonCacheService;
use Chamilo\Libraries\Cache\Assetic\StylesheetVendorCacheService;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Libraries\Architecture\Resource
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StylesheetGenerator
{

    /**
     * @var \Chamilo\Libraries\Cache\Assetic\StylesheetCommonCacheService
     */
    private $stylesheetCommonCacheService;

    /**
     * @var \Chamilo\Libraries\Cache\Assetic\StylesheetVendorCacheService
     */
    private $styleSheetVendorCacheService;

    /**
     * @param \Chamilo\Libraries\Cache\Assetic\StylesheetCommonCacheService $stylesheetCommonCacheService
     * @param \Chamilo\Libraries\Cache\Assetic\StylesheetVendorCacheService $styleSheetVendorCacheService
     */
    public function __construct(
        StylesheetCommonCacheService $stylesheetCommonCacheService,
        StylesheetVendorCacheService $styleSheetVendorCacheService
    )
    {
        $this->stylesheetCommonCacheService = $stylesheetCommonCacheService;
        $this->styleSheetVendorCacheService = $styleSheetVendorCacheService;
    }

    /**
     * @param string $content
     */
    public function run(string $content)
    {
        $response = new Response();

        $response->setPublic();
        // 24 hours cache
        $response->setMaxAge(3600 * 24);
        $response->headers->set('Content-Type', 'text/css');
        $response->setContent($content);
        $response->send();

        exit();
    }

    /**
     * @return \Chamilo\Libraries\Cache\Assetic\StylesheetVendorCacheService
     */
    public function getStyleSheetVendorCacheService(): StylesheetVendorCacheService
    {
        return $this->styleSheetVendorCacheService;
    }

    /**
     * @param \Chamilo\Libraries\Cache\Assetic\StylesheetVendorCacheService $styleSheetVendorCacheService
     *
     * @return StylesheetGenerator
     */
    public function setStyleSheetVendorCacheService(
        StylesheetVendorCacheService $styleSheetVendorCacheService
    ): StylesheetGenerator
    {
        $this->styleSheetVendorCacheService = $styleSheetVendorCacheService;

        return $this;
    }

    /**
     * @return \Chamilo\Libraries\Cache\Assetic\StylesheetCommonCacheService
     */
    public function getStylesheetCommonCacheService(): StylesheetCommonCacheService
    {
        return $this->stylesheetCommonCacheService;
    }

    /**
     * @param \Chamilo\Libraries\Cache\Assetic\StylesheetCommonCacheService $stylesheetCommonCacheService
     *
     * @return StylesheetGenerator
     */
    public function setStylesheetCommonCacheService(
        StylesheetCommonCacheService $stylesheetCommonCacheService
    ): StylesheetGenerator
    {
        $this->stylesheetCommonCacheService = $stylesheetCommonCacheService;

        return $this;
    }

    /**
     * @param string $theme
     */
    public function runCommon(string $theme)
    {
        $stylesheetCacheService = $this->getStylesheetCommonCacheService();
        $stylesheetCacheService->getThemePathBuilder()->setTheme($theme);

        $this->run($stylesheetCacheService->get());
    }

    public function runVendor()
    {
        $this->run($this->getStylesheetVendorCacheService()->get());
    }

}
