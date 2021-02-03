<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\ExternalTool\Service;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeAttempt;

/**
 * @package Chamilo\Application\Weblcms\Bridge\LearningPath\ExternalTool\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExternalToolResultService
{
    /**
     * @var \Chamilo\Application\Weblcms\Bridge\LearningPath\ExternalTool\Storage\Repository\LearningPathTreeNodeAttemptRepository
     */
    protected $treeNodeAttemptRepository;

    /**
     * ExternalToolResultService constructor.
     *
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\ExternalTool\Storage\Repository\LearningPathTreeNodeAttemptRepository $externalToolResultRepository
     */
    public function __construct(
        \Chamilo\Application\Weblcms\Bridge\LearningPath\ExternalTool\Storage\Repository\LearningPathTreeNodeAttemptRepository $externalToolResultRepository
    )
    {
        $this->treeNodeAttemptRepository = $externalToolResultRepository;
    }

    /**
     * @param int $id
     *
     * @return int
     */
    public function getResultById(int $id)
    {
        return $this->getAttemptById($id)->get_score();
    }

    /**
     * @param int $id
     *
     * @return LearningPathTreeNodeAttempt
     */
    protected function getAttemptById(int $id)
    {
        $attempt = $this->treeNodeAttemptRepository->findTreeNodeAttemptById($id);
        if (!$attempt instanceof LearningPathTreeNodeAttempt)
        {
            throw new \RuntimeException(
                sprintf('The given learning path tree node attempt with id %s is not found', $id)
            );
        }

        return $attempt;
    }

    /**
     * @param int $resultId
     * @param float $score
     */
    public function updateResultByIdAndLTIScore(int $resultId, float $score = null)
    {
        $attempt = $this->getAttemptById($resultId);

        if (!is_null($score))
        {
            $attempt->set_score(intval($score * 100));
            $attempt->setCompleted(true);
        }
        else
        {
            $attempt->set_score(null);
        }

        if (!$this->treeNodeAttemptRepository->updateTreeNodeAttempt($attempt))
        {
            throw new \RuntimeException(
                sprintf('Could not update the learning path tree node attempt with id %s', $resultId)
            );
        }
    }

    /**
     * @param int $resultId
     */
    public function deleteResultById(int $resultId)
    {
        $this->updateResultByIdAndLTIScore($resultId, null);
    }
}