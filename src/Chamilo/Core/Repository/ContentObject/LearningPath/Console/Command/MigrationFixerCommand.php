<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Console\Command;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\MigrationTrackingParameters;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\MigrationFixer;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\MigrationService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepository;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\Console\Command\ChamiloCommand;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\Translator;

/**
 * Command to migrate the learning paths
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MigrationFixerCommand extends ChamiloCommand
{
    /**
     * @var MigrationFixer
     */
    protected $migrationFixer;

    /**
     * @param Translator $translator
     * @param MigrationFixer $migrationFixer
     */
    public function __construct(
        Translator $translator, MigrationFixer $migrationFixer
    )
    {
        $this->migrationFixer = $migrationFixer;

        parent::__construct($translator);
    }

    /**
     * Configures the current command
     */
    protected function configure()
    {
        $this->setName('chamilo:repository:learning_path:migration_fixer')
            ->setDescription(
                $this->translator->trans(
                    'LearningPathMigrationFixerDescription', array(),
                    'Chamilo\Core\Repository\ContentObject\LearningPath'
                )
            );
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
        $this->migrationFixer->migrateLearningPaths($output);
    }

}