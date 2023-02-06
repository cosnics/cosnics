<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathStepContext;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathStepContextRepository;

/**
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LearningPathStepContextService
{
    /**
     * @var LearningPathStepContextRepository
     */
    protected $learningPathStepContextRepository;

    /**
     * LearningPathStepContextService constructor.
     *
     * @param LearningPathStepContextRepository $learningPathStepContextRepository
     */
    public function __construct(LearningPathStepContextRepository $learningPathStepContextRepository)
    {
        $this->learningPathStepContextRepository = $learningPathStepContextRepository;
    }

    /**
     * @param int $stepId
     * @param string $contextClass
     * @param int $contextId
     *
     * @return LearningPathStepContext
     */
    public function getOrCreateLearningPathStepContext(int $stepId, string $contextClass, int $contextId): LearningPathStepContext
    {
        $learningPathStepContext = $this->learningPathStepContextRepository->findLearningPathStepContext($stepId, $contextClass, $contextId);
        if (!$learningPathStepContext instanceof LearningPathStepContext)
        {
            $learningPathStepContext = new LearningPathStepContext();
            $learningPathStepContext->setLearningPathStepId($stepId);
            $learningPathStepContext->setContextClass($contextClass);
            $learningPathStepContext->setContextId($contextId);
            $this->learningPathStepContextRepository->create($learningPathStepContext);
        }
        return $learningPathStepContext;
    }
}