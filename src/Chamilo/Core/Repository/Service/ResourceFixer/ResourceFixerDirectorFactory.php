<?php

namespace Chamilo\Core\Repository\Service\ResourceFixer;

use Chamilo\Core\Repository\Storage\Repository\ResourceFixerRepository;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Builds the ResourceFixerDirector class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResourceFixerDirectorFactory
{
    /**
     * @var ResourceFixerRepository
     */
    protected $contentObjectResourceFixerRepository;

    /**
     * @var ConfigurablePathBuilder
     */
    protected $configurablePathBuilder;

    /**
     * ResourceFixer constructor.
     *
     * @param ResourceFixerRepository $contentObjectResourceFixerRepository
     * @param ConfigurablePathBuilder $configurablePathBuilder
     */
    public function __construct(
        ResourceFixerRepository $contentObjectResourceFixerRepository, ConfigurablePathBuilder $configurablePathBuilder
    )
    {
        $this->contentObjectResourceFixerRepository = $contentObjectResourceFixerRepository;
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    /**
     * Builds the ResourceFixerDirector
     */
    public function buildResourceFixerDirector()
    {
        $logger = new Logger('ResourceFixer');
        $logger->pushHandler(
            new StreamHandler($this->configurablePathBuilder->getLogPath() . 'ResourceFixer.log', Logger::INFO)
        );

        $resourceFixerDirector = new ResourceFixerDirector($logger);

        $resourceFixerDirector->addResourceFixer(
            new AssessmentMatchingQuestionResourceFixer($this->contentObjectResourceFixerRepository, $logger)
        );

        $resourceFixerDirector->addResourceFixer(
            new AssessmentMatchNumericQuestionResourceFixer($this->contentObjectResourceFixerRepository, $logger)
        );

        $resourceFixerDirector->addResourceFixer(
            new AssessmentMatchTextQuestionResourceFixer($this->contentObjectResourceFixerRepository, $logger)
        );

        $resourceFixerDirector->addResourceFixer(
            new AssessmentMatrixQuestionResourceFixer($this->contentObjectResourceFixerRepository, $logger)
        );

        $resourceFixerDirector->addResourceFixer(
            new AssessmentMultipleChoiceQuestionResourceFixer($this->contentObjectResourceFixerRepository, $logger)
        );

        $resourceFixerDirector->addResourceFixer(
            new AssessmentRatingQuestionResourceFixer($this->contentObjectResourceFixerRepository, $logger)
        );

        $resourceFixerDirector->addResourceFixer(
            new AssessmentSelectQuestionResourceFixer($this->contentObjectResourceFixerRepository, $logger)
        );

        $resourceFixerDirector->addResourceFixer(
            new ContentObjectDescriptionResourceFixer($this->contentObjectResourceFixerRepository, $logger)
        );

        $resourceFixerDirector->addResourceFixer(
            new ForumPostResourceFixer($this->contentObjectResourceFixerRepository, $logger)
        );

        $resourceFixerDirector->addResourceFixer(
            new HotspotQuestionResourceFixer($this->contentObjectResourceFixerRepository, $logger)
        );

        $resourceFixerDirector->addResourceFixer(
            new OrderingQuestionResourceFixer($this->contentObjectResourceFixerRepository, $logger)
        );

        $resourceFixerDirector->addResourceFixer(
            new WorkspaceResourceFixer($this->contentObjectResourceFixerRepository, $logger)
        );

        return $resourceFixerDirector;
    }
}