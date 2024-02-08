<?php
namespace Chamilo\Core\Repository\Service\ResourceFixer;

/**
 * Fixes the resource tags for the ForumPost
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ForumPostResourceFixer extends ResourceFixer
{
    /**
     * Fixes the resources
     *
     * @param bool $forceUpdate
     */
    public function fixResources($forceUpdate = false)
    {
        $this->logger->info('Started fixing ForumPost objects');

        $offset = 0;
        $count = $this->contentObjectResourceFixerRepository->countForumPosts();

        $this->logger->info(sprintf('Found %s ForumPost objects', $count));

        while ($offset < $count)
        {
            $forumPosts = $this->contentObjectResourceFixerRepository->findForumPosts($offset);

            foreach($forumPosts as $forumPost)
            {
                $this->logger->debug(
                    sprintf(
                        'Parsing ForumPost with id %s', $forumPost->getId()
                    )
                );

                $content = $forumPost->get_content();
                $newContent = $this->fixResourcesInTextContent(null, $content);

                if($content != $newContent)
                {
                    $this->logger->info(
                        sprintf(
                            'Some fields have changed, updating ForumPost with id %s', $forumPost->getId()
                        )
                    );
                    
                    $forumPost->set_content($newContent);

                    if($forceUpdate)
                    {
                        $forumPost->update(true);
                    }
                }
            }

            $offset += 1000;
        }

        $this->logger->info('Finished fixing ForumPost objects');
    }
}