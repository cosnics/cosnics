<?php

namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

/**
 * Class JavascriptHeaders
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class JavascriptHeaders
{
    /**
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     * @var PathBuilder
     */
    protected $pathBuilder;

    /**
     * JavascriptHeaders constructor.
     *
     * @param ResourceManager $resourceManager
     * @param PathBuilder $pathBuilder
     */
    public function __construct(ResourceManager $resourceManager, PathBuilder $pathBuilder)
    {
        $this->resourceManager = $resourceManager;
        $this->pathBuilder = $pathBuilder;
    }

    /**
     * @param BaseHeader $baseHeader
     */
    public function addGlobalJavascriptFilesToHeader(BaseHeader $baseHeader)
    {
        $javascriptFiles = [
            'cosnics.jquery.min.js',
            'cosnics.angular.min.js',
            'cosnics.vue.min.js',
            'cosnics.vendors.min.js',
            'cosnics.common.min.js'
        ];

        $basePath = $this->pathBuilder->getResourcesPath('Chamilo\Libraries', true) . 'Javascript' . DIRECTORY_SEPARATOR;

        foreach($javascriptFiles as $javascriptFile)
        {
            $baseHeader->addHtmlHeader($this->resourceManager->get_resource_html($basePath . $javascriptFile));
        }
    }

}
