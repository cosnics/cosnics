<?php

namespace Chamilo\Core\Repository\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package Chamilo\Core\Repository\Console\Command
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FeedbackMigrationCommand extends Command
{
    /**
     * @var \Chamilo\Core\Repository\Feedback\Infrastructure\Service\FeedbackMigration
     */
    protected $feedbackMigration;

    /**
     * FeedbackMigrationCommand constructor.
     *
     * @param \Chamilo\Core\Repository\Feedback\Infrastructure\Service\FeedbackMigration $feedbackMigration
     */
    public function __construct(
        \Chamilo\Core\Repository\Feedback\Infrastructure\Service\FeedbackMigration $feedbackMigration
    )
    {
        $this->feedbackMigration = $feedbackMigration;

        parent::__construct();
    }

    /**
     * Configures the current command
     */
    protected function configure()
    {
        $this->setName('chamilo:repository:feedback_migration')
            ->setDescription('Adds the included objects from the migrated feedback content objects. WARNING: ONLY USE THIS WHEN YOU HAVE COMPLETED THE SQL MIGRATION IN changes2019.sql');
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->feedbackMigration->migrate($output);
    }

}