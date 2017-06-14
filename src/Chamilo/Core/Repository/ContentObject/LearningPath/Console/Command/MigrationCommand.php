<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Console\Command;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\MigrationTrackingParameters;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathService;
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
class MigrationCommand extends ChamiloCommand
{
    /**
     * @var LearningPathService
     */
    protected $learningPathService;

    /**
     * @var TreeNodeDataService
     */
    protected $treeNodeDataService;

    /**
     * @var ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     * @var DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * MigrationCommand constructor.
     *
     * @param Translator $translator
     * @param LearningPathService $learningPathService
     * @param TreeNodeDataService $treeNodeDataService
     * @param ContentObjectRepository $contentObjectRepository
     * @param DataClassRepository $dataClassRepository
     */
    public function __construct(
        Translator $translator, LearningPathService $learningPathService,
        TreeNodeDataService $treeNodeDataService,
        ContentObjectRepository $contentObjectRepository,
        DataClassRepository $dataClassRepository
    )
    {
        $this->learningPathService = $learningPathService;
        $this->treeNodeDataService = $treeNodeDataService;
        $this->contentObjectRepository = $contentObjectRepository;
        $this->dataClassRepository = $dataClassRepository;

        parent::__construct($translator);
    }

    /**
     * Configures the current command
     */
    protected function configure()
    {
        $this->setName('chamilo:repository:learning_path:migrate')
            ->setDescription(
                $this->translator->trans(
                    'LearningPathMigrationDescription', array(), 'Chamilo\Core\Repository\ContentObject\LearningPath'
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
        $trackingRepository = new TrackingRepository(
            $this->dataClassRepository, new MigrationTrackingParameters()
        );

        $migrationService = new MigrationService(
            $this->learningPathService, $this->treeNodeDataService,
            $trackingRepository, $this->contentObjectRepository
        );

        $migrationService->migrateLearningPaths();
    }

}