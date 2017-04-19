<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Console\Command;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathMigrationTrackingParameters;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathMigrationService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathTrackingRepository;
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
class LearningPathMigrationCommand extends ChamiloCommand
{
    /**
     * @var LearningPathService
     */
    protected $learningPathService;

    /**
     * @var ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     * @var DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * LearningPathMigrationCommand constructor.
     *
     * @param Translator $translator
     * @param LearningPathService $learningPathService
     * @param ContentObjectRepository $contentObjectRepository
     * @param DataClassRepository $dataClassRepository
     */
    public function __construct(
        Translator $translator, LearningPathService $learningPathService,
        ContentObjectRepository $contentObjectRepository,
        DataClassRepository $dataClassRepository
    )
    {
        $this->learningPathService = $learningPathService;
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
        $learningPathTrackingRepository = new LearningPathTrackingRepository(
            $this->dataClassRepository, new LearningPathMigrationTrackingParameters()
        );

        $learningPathMigrationService = new LearningPathMigrationService(
            $this->learningPathService, $learningPathTrackingRepository, $this->contentObjectRepository
        );

        $learningPathMigrationService->migrateLearningPaths();
    }

}