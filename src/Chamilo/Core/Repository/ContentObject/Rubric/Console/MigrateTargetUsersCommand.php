<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Chamilo\Core\Repository\ContentObject\Rubric\Service\MigrationService;

/**
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class MigrateTargetUsersCommand extends Command
{

    /**
     * @var MigrationService
     */
    protected $migrationService;

    /**
     * MigrateCommand constructor.
     *
     * @param MigrationService $migrationService
     */
    public function __construct(MigrationService $migrationService)
    {
        parent::__construct();

        $this->migrationService = $migrationService;
    }

    /**
     * Configure command, set parameters definition and help.
     */
    protected function configure()
    {
        $this->setName('chamilo:repository:rubric:migrate_target_users');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->migrationService->migrateTargetUsers();
    }
}
