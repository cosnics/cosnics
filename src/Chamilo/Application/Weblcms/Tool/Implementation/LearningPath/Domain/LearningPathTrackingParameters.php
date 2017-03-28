<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Domain;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathChildAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathQuestionAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTrackingParametersInterface;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Tracking Repository implementation
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathTrackingParameters implements LearningPathTrackingParametersInterface
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
     * LearningPathTrackingParameters constructor.
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
    public function getLearningPathAttemptClassName()
    {
        return LearningPathAttempt::class_name();
    }

    /**
     * @return string
     */
    public function getLearningPathChildAttemptClassName()
    {
        return LearningPathChildAttempt::class_name();
    }

    /**
     * @return string
     */
    public function getLearningPathQuestionAttemptClassName()
    {
        return LearningPathQuestionAttempt::class_name();
    }

    /**
     * @return Condition
     */
    public function getLearningPathAttemptConditions()
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $this->getLearningPathAttemptClassName(), LearningPathAttempt::PROPERTY_COURSE_ID
            ),
            new StaticConditionVariable($this->courseId)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $this->getLearningPathAttemptClassName(),
                LearningPathAttempt::PROPERTY_PUBLICATION_ID
            ),
            new StaticConditionVariable($this->publicationId)
        );

        return new AndCondition($conditions);
    }

    /**
     * Creates a new instance of the LearningPathAttempt extension
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathAttempt
     */
    public function createLearningPathAttemptInstance()
    {
        $learningPathAttempt = new LearningPathAttempt();

        $learningPathAttempt->set_course_id($this->courseId);
        $learningPathAttempt->set_publication_id($this->publicationId);

        return $learningPathAttempt;
    }

    /**
     * Creates a new instance of the LearningPathChildAttempt extension
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathChildAttempt
     */
    public function createLearningPathChildAttemptInstance()
    {
        return new LearningPathChildAttempt();
    }

    /**
     * Creates a new instance of the LearningPathQuestionAttempt extension
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\LearningPathQuestionAttempt
     */
    public function createLearningPathQuestionAttemptInstance()
    {
        return new LearningPathQuestionAttempt();
    }
}