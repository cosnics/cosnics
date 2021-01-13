<?php

namespace Chamilo\Core\Repository\Common\Includes;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * This service is a WIP. Currently this only scans the description for included content objects and includes them
 * into the content object. The old include-scanner service works based on the (metadata of the) contentobject form.
 * The old scanner uses the forms to determine the available html editors based on the values array of the form. This
 * system should be transformed and embedded into this service to make it possible to scan a content object for it's
 * includes in all the available html editors.
 *
 * @package Chamilo\Core\Repository\Common\Includes
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectIncluder
{
    /**
     * @var \Chamilo\Core\Repository\Common\ContentObjectResourceParser
     */
    protected $contentObjectResourceParser;

    /**
     * ContentObjectIncluder constructor.
     *
     * @param \Chamilo\Core\Repository\Common\ContentObjectResourceParser $contentObjectResourceParser
     */
    public function __construct(
        \Chamilo\Core\Repository\Common\ContentObjectResourceParser $contentObjectResourceParser
    )
    {
        $this->contentObjectResourceParser = $contentObjectResourceParser;
    }

    /**
     * Scans the resources that are included in the content object and includes them in the content object
     *
     * TODO: fix this for the other html editors of the content object as well.
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     */
    public function scanForResourcesAndIncludeContentObjects(ContentObject $contentObject)
    {
        $description = $contentObject->get_description();
        $this->processNewResources($contentObject, $description);
        $this->processOldResources($contentObject, $description);
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param string $content
     */
    protected function processNewResources(ContentObject $contentObject, string $content = null)
    {
        if(empty($content))
        {
            return;
        }

        $resourceContentObjects = $this->contentObjectResourceParser->getContentObjects($content);
        foreach($resourceContentObjects as $resourceContentObject)
        {
            $contentObject->include_content_object($resourceContentObject->getId());
        }
    }

    /**
     * Processes the old resource tags (this code needs to be here for legacy html content)
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param string $content
     */
    protected function processOldResources(ContentObject $contentObject, string $content = null)
    {
        if(empty($content))
        {
            return;
        }

        $domDocument = $this->contentObjectResourceParser->getDomDocument($content);
        $domXpath = $this->contentObjectResourceParser->getDomXPath($domDocument);

        $resources = $domXpath->query('//resource');
        foreach ($resources as $resource)
        {
            /** @var \DOMElement $resource * */
            $source = $resource->getAttribute('source');

            try
            {
                /** @var ContentObject $contentObject */
                $resourceContentObject = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                    ContentObject::class_name(),
                    $source
                );

                if($resourceContentObject instanceof ContentObject)
                {
                    $contentObject->include_content_object($resourceContentObject->getId());
                }
            }
            catch (\Exception $exception)
            {
                continue;
            }
        }
    }
}