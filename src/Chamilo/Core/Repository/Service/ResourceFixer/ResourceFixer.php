<?php

namespace Chamilo\Core\Repository\Service\ResourceFixer;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\Repository\ResourceFixerRepository;
use Monolog\Logger;

/**
 * Fixes the resource tags of content objects by adding a security code to them
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class ResourceFixer
{
    /**
     * @var ResourceFixerRepository
     */
    protected $contentObjectResourceFixerRepository;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * ResourceFixer constructor.
     *
     * @param ResourceFixerRepository $contentObjectResourceFixerRepository
     * @param Logger $logger
     */
    public function __construct(ResourceFixerRepository $contentObjectResourceFixerRepository, Logger $logger)
    {
        $this->contentObjectResourceFixerRepository = $contentObjectResourceFixerRepository;
        $this->logger = $logger;
    }

    /**
     * Fixes the resource tags in a given text
     *
     * @param string $textContent
     *
     * @return string
     */
    protected function fixResourcesInTextContent($textContent)
    {
        $domDocument = new \DOMDocument();
        $domDocument->loadHTML($textContent);

        $domXPath = new \DOMXPath($domDocument);

        $resourceTags = $domXPath->query('//resource');

        $this->logger->debug(sprintf('Found %s resource tags', $resourceTags->length));

        /** @var \DOMElement $resourceTag */
        foreach ($resourceTags as $resourceTag)
        {
            if ($resourceTag->hasAttribute('security_code'))
            {
                $this->logger->debug('Security code already found, skipping resource tag');
                continue;
            }

            $objectId = $resourceTag->getAttribute('source');
            if (!$objectId)
            {
                $this->logger->debug(
                    sprintf('The resource tag has no valid ID, skipping resource tag', $objectId)
                );

                continue;
            }

            try
            {
                $object = $this->contentObjectResourceFixerRepository->findContentObjectById($objectId);
            }
            catch (\Exception $ex)
            {
                $this->logger->debug(
                    sprintf('The content object with ID %s is not found, skipping resource tag', $objectId)
                );

                continue;
            }

            if (!$object instanceof ContentObject)
            {
                $this->logger->debug(
                    sprintf(
                        'The content object with ID %s is not a valid content object, skipping resource tag', $objectId
                    )
                );

                continue;
            }

            $originalResourceTagHTML = $resourceTag->ownerDocument->saveHTML($resourceTag);
            $resourceTag->setAttribute('security_code', $object->calculate_security_code());
            $fixedResourceTagHTML = $resourceTag->ownerDocument->saveHTML($resourceTag);

            $this->logger->info(
                sprintf(
                    'Fixing resource tag with object ID %s and security code %s', $objectId,
                    $object->calculate_security_code()
                )
            );

            $textContent = str_replace(
                $originalResourceTagHTML, $fixedResourceTagHTML, $textContent
            );
        }

        return $textContent;
    }

    /**
     * Fixes the content object resources
     *
     * @param bool $forceUpdate
     */
    abstract function fixResources($forceUpdate = false);
}