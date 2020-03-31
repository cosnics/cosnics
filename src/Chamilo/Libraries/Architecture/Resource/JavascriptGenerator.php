<?php
namespace Chamilo\Libraries\Architecture\Resource;

use Chamilo\Libraries\Cache\Assetic\JavascriptCacheService;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Libraries\Architecture\Resource
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class JavascriptGenerator
{
    /**
     *
     * @var \Chamilo\Libraries\Cache\Assetic\JavascriptCacheService
     */
    private $javascriptCacheService;

    /**
     * JavascriptGenerator constructor.
     *
     * @param \Chamilo\Libraries\Cache\Assetic\JavascriptCacheService $javascriptCacheService
     */
    public function __construct(JavascriptCacheService $javascriptCacheService)
    {
        $this->javascriptCacheService = $javascriptCacheService;
    }

    public function run()
    {
        $response = new Response();

        $response->setPublic();
        // 24 hours cache
        $response->setMaxAge(3600 * 24);
        $response->headers->set('Content-Type', 'text/javascript');
        $response->setContent($this->getJavascriptCacheService()->get());
        $response->send();

        exit();
    }

    /**
     * @return \Chamilo\Libraries\Cache\Assetic\JavascriptCacheService
     */
    public function getJavascriptCacheService(): JavascriptCacheService
    {
        return $this->javascriptCacheService;
    }

    /**
     * @param \Chamilo\Libraries\Cache\Assetic\JavascriptCacheService $javascriptCacheService
     */
    public function setJavascriptCacheService(JavascriptCacheService $javascriptCacheService): void
    {
        $this->javascriptCacheService = $javascriptCacheService;
    }
}
