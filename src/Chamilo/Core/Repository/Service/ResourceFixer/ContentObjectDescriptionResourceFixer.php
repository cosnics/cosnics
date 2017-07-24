<?php

namespace Chamilo\Core\Repository\Service\ResourceFixer;

use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumTopic;

/**
 * Fixes the resource tags for the description fields of all content objects
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectDescriptionResourceFixer extends ResourceFixer
{
    /**
     * Fixes the resources
     *
     * @param bool $forceUpdate
     */
    public function fixResources($forceUpdate = false)
    {
        $this->logger->addInfo('Started fixing ContentObject objects');

        $offset = 0;
        $count = $this->contentObjectResourceFixerRepository->countContentObjectsWithResourceTags();

        $this->logger->addInfo(sprintf('Found %s ContentObject objects', $count));

        while ($offset < $count)
        {
            $contentObjects = $this->contentObjectResourceFixerRepository->findContentObjectsWithResourceTags($offset);

            foreach($contentObjects as $contentObject)
            {
                $this->logger->debug(
                    sprintf(
                        'Parsing ContentObject with id %s', $contentObject->getId()
                    )
                );
                
                $newDescription = $this->fixResourcesInTextContent($contentObject->get_description());
                if ($newDescription != $contentObject->get_description())
                {
                    $this->logger->info(
                        sprintf(
                            'Some fields have changed, updating ContentObject with id %s',
                            $contentObject->getId()
                        )
                    );
                    
                    $contentObject->set_description($newDescription);

                    if($forceUpdate)
                    {
                        if($contentObject instanceof ForumTopic)
                        {
                            $contentObject->update(true);
                        }
                        else
                        {
                            $contentObject->update(false);
                        }
                    }
                }

            }

            $offset += 1000;
        }

        $this->logger->addInfo('Finished fixing ContentObject objects');
    }
}