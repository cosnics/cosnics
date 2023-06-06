<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\DummyTreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TrackingParametersInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Tracking parameters for the learning path tracking service and repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TrackingParameters implements TrackingParametersInterface
{

    /**
     * Creates a new instance of the TreeNodeAttempt extension
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt
     */
    public function createTreeNodeAttemptInstance()
    {
        return new DummyTreeNodeAttempt();
    }

    /**
     * Creates a new instance of the TreeNodeQuestionAttempt extension
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeQuestionAttempt
     */
    public function createTreeNodeQuestionAttemptInstance()
    {
        return new DummyQuestionAttempt();
    }

    /**
     * Returns the user ids for whom the learning path was targeted
     *
     * @param LearningPath $learningPath
     *
     * @return \int[]
     */
    public function getLearningPathTargetUserIds(LearningPath $learningPath)
    {
        if (empty($this->getSession()->get(Manager::SESSION_USER_ID)))
        {
            return [];
        }

        return [$this->getSession()->get(Manager::SESSION_USER_ID)];
    }

    public function getSession(): SessionInterface
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SessionInterface::class);
    }

    /**
     * @return string
     */
    public function getTreeNodeAttemptClassName()
    {
        return DummyTreeNodeAttempt::class;
    }

    /**
     * @return Condition
     */
    public function getTreeNodeAttemptConditions()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getTreeNodeQuestionAttemptClassName()
    {
        return DummyQuestionAttempt::class;
    }
}