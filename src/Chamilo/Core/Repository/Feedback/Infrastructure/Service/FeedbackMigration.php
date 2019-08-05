<?php

namespace Chamilo\Core\Repository\Feedback\Infrastructure\Service;

use Chamilo\Core\Repository\ContentObject\Feedback\Storage\DataClass\Feedback;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package Chamilo\Core\Repository\Feedback\Infrastructure\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FeedbackMigration
{
    /**
     * @var \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     * @var \Chamilo\Core\Repository\Common\Includes\ContentObjectIncluder
     */
    protected $contentObjectIncluder;

    /**
     * FeedbackMigration constructor.
     *
     * @param \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository $contentObjectRepository
     * @param \Chamilo\Core\Repository\Common\Includes\ContentObjectIncluder $contentObjectIncluder
     */
    public function __construct(
        \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository $contentObjectRepository,
        \Chamilo\Core\Repository\Common\Includes\ContentObjectIncluder $contentObjectIncluder
    )
    {
        $this->contentObjectRepository = $contentObjectRepository;
        $this->contentObjectIncluder = $contentObjectIncluder;
    }

    /**
     * Retrieves all the feedback content objects and adds the includes
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function migrate(OutputInterface $output)
    {
        $feedbackObjects = $this->contentObjectRepository->findAll(Feedback::class, new DataClassRetrievesParameters());
        $output->writeln(sprintf('Migrating %s feedback objects', $feedbackObjects->size()));

        while($feedback = $feedbackObjects->next_result())
        {
            $this->contentObjectIncluder->scanForResourcesAndIncludeContentObjects($feedback);
            $output->writeln(sprintf('Migrated feedback object %s', $feedback->getId()));
        }
    }
}