<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Domain;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeQuestionAttempt;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TrackingParametersInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Tracking parameters for the learning path tracking service and repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TrackingParameters implements TrackingParametersInterface
{
    /**
     * @var int
     */
    protected $courseId;

    /**
     * @var int
     */
    protected $publicationId;

    /**
     * @var int[]
     */
    protected $targetUserIds;

    /**
     * TrackingParameters constructor.
     *
     * @param int $courseId
     * @param int $publicationId
     */
    public function __construct($courseId, $publicationId)
    {
        $this->setCourseId($courseId)
            ->setPublicationId($publicationId);
    }

    /**
     * @param int $courseId
     *
     * @return $this
     */
    public function setCourseId($courseId)
    {
        if (empty($courseId) || !is_int($courseId))
        {
            throw new \InvalidArgumentException(
                'The given courseId should be a valid integer and should not be empty'
            );
        }

        $this->courseId = $courseId;

        return $this;
    }

    /**
     * @param int $publicationId
     *
     * @return $this
     */
    public function setPublicationId($publicationId)
    {
        if (empty($publicationId) || !is_int($publicationId))
        {
            throw new \InvalidArgumentException(
                'The given publicationId should be a valid integer and should not be empty'
            );
        }

        $this->publicationId = $publicationId;

        return $this;
    }

    /**
     * @return string
     */
    public function getTreeNodeAttemptClassName()
    {
        return LearningPathTreeNodeAttempt::class_name();
    }

    /**
     * @return string
     */
    public function getTreeNodeQuestionAttemptClassName()
    {
        return LearningPathTreeNodeQuestionAttempt::class_name();
    }

    /**
     * @return Condition
     */
    public function getTreeNodeAttemptConditions()
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                $this->getTreeNodeAttemptClassName(),
                LearningPathTreeNodeAttempt::PROPERTY_PUBLICATION_ID
            ),
            new StaticConditionVariable($this->publicationId)
        );
    }

    /**
     * Creates a new instance of the TreeNodeAttempt extension
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt
     */
    public function createTreeNodeAttemptInstance()
    {
        $treeNodeAttempt = new LearningPathTreeNodeAttempt();
        $treeNodeAttempt->set_publication_id($this->publicationId);
        return $treeNodeAttempt;
    }

    /**
     * Creates a new instance of the TreeNodeQuestionAttempt extension
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeQuestionAttempt
     */
    public function createTreeNodeQuestionAttemptInstance()
    {
        return new LearningPathTreeNodeQuestionAttempt();
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
        if(!isset($this->targetUserIds))
        {
            $this->targetUserIds = DataManager::getPublicationTargetUserIds($this->publicationId, $this->courseId);
        }

        return $this->targetUserIds;
    }
}