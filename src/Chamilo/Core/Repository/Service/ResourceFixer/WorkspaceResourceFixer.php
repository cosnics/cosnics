<?php

namespace Chamilo\Core\Repository\Service\ResourceFixer;

/**
 * Fixes the resource tags for the Workspace
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class WorkspaceResourceFixer extends ResourceFixer
{
    /**
     * Fixes the resources
     *
     * @param bool $forceUpdate
     */
    public function fixResources($forceUpdate = false)
    {
        $this->logger->addInfo('Started fixing Workspace objects');

        $offset = 0;
        $count = $this->contentObjectResourceFixerRepository->countWorkspaces();

        $this->logger->addInfo(sprintf('Found %s Workspace objects', $count));

        while ($offset < $count)
        {
            $workspaces = $this->contentObjectResourceFixerRepository->findWorkspaces($offset);

            foreach($workspaces as $workspace)
            {
                $this->logger->debug(
                    sprintf(
                        'Parsing Workspace with id %s', $workspace->getId()
                    )
                );

                $content = $workspace->getDescription();
                $newContent = $this->fixResourcesInTextContent($content);

                if($content != $newContent)
                {
                    $this->logger->info(
                        sprintf(
                            'Some fields have changed, updating Workspace with id %s',
                            $workspace->getId()
                        )
                    );

                    $workspace->setDescription($newContent);

                    if($forceUpdate)
                    {
                        $workspace->update();
                    }
                }
            }

            $offset += 1000;
        }

        $this->logger->addInfo('Finished fixing Workspace objects');
    }
}