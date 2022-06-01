<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Configuration;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use InvalidArgumentException;
use RangeException;

/**
 * Describes a relation between a learning path and another content object
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeNodeData extends DataClass implements DisplayOrderDataClassListenerSupport
{
    const PROPERTY_LEARNING_PATH_ID = 'learning_path_id';
    const PROPERTY_PARENT_LEARNING_PATH_CHILD_ID = 'parent_tree_node_data_id';
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_MAX_ATTEMPTS = 'max_attempts';
    const PROPERTY_MASTERY_SCORE = 'mastery_score';
    const PROPERTY_ALLOW_HINTS = 'allow_hints';
    const PROPERTY_SHOW_SCORE = 'show_score';
    const PROPERTY_SHOW_CORRECTION = 'show_correction';
    const PROPERTY_SHOW_SOLUTION = 'show_solution';
    const PROPERTY_SHOW_ANSWER_FEEDBACK = 'show_answer_feedback';
    const PROPERTY_FEEDBACK_LOCATION = 'feedback_location';
    const PROPERTY_BLOCKED = 'blocked';
    const PROPERTY_DISPLAY_ORDER = 'display_order';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_ADDED_DATE = 'added_date';
    const PROPERTY_ENFORCE_DEFAULT_TRAVERSING_ORDER = 'enforce_default_traversing_order';

    /**
     * TreeNodeData constructor.
     *
     * @param array $default_properties
     * @param array $additional_properties
     */
    public function __construct($default_properties = [], $additionalProperties = [])
    {
        parent::__construct($default_properties, $additionalProperties);
        $this->addListener(new DisplayOrderDataClassListener($this));
    }

    /**
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array(
                self::PROPERTY_LEARNING_PATH_ID,
                self::PROPERTY_PARENT_LEARNING_PATH_CHILD_ID,
                self::PROPERTY_CONTENT_OBJECT_ID,
                self::PROPERTY_MAX_ATTEMPTS,
                self::PROPERTY_MASTERY_SCORE,
                self::PROPERTY_ALLOW_HINTS,
                self::PROPERTY_SHOW_SCORE,
                self::PROPERTY_SHOW_CORRECTION,
                self::PROPERTY_SHOW_SOLUTION,
                self::PROPERTY_SHOW_ANSWER_FEEDBACK,
                self::PROPERTY_FEEDBACK_LOCATION,
                self::PROPERTY_BLOCKED,
                self::PROPERTY_DISPLAY_ORDER,
                self::PROPERTY_USER_ID,
                self::PROPERTY_ADDED_DATE,
                self::PROPERTY_ENFORCE_DEFAULT_TRAVERSING_ORDER
            )
        );
    }

    /**
     * @return int
     */
    public function getLearningPathId()
    {
        return $this->getDefaultProperty(self::PROPERTY_LEARNING_PATH_ID);
    }

    /**
     * @param int $learningPathId
     *
     * @return $this
     */
    public function setLearningPathId($learningPathId)
    {
        if (empty($learningPathId) || !is_integer($learningPathId))
        {
            throw new InvalidArgumentException(
                'The given learning path id must be a valid integer and must not be empty'
            );
        }

        $this->setDefaultProperty(self::PROPERTY_LEARNING_PATH_ID, $learningPathId);

        return $this;
    }

    /**
     * @return int
     */
    public function getParentTreeNodeDataId()
    {
        return $this->getDefaultProperty(self::PROPERTY_PARENT_LEARNING_PATH_CHILD_ID);
    }

    /**
     * @param int $parentTreeNodeDataId
     *
     * @return $this
     */
    public function setParentTreeNodeDataId($parentTreeNodeDataId)
    {
        if (!is_integer($parentTreeNodeDataId))
        {
            throw new InvalidArgumentException(
                'The given parent learning path child id must be a valid integer'
            );
        }

        $this->setDefaultProperty(self::PROPERTY_PARENT_LEARNING_PATH_CHILD_ID, $parentTreeNodeDataId);

        return $this;
    }

    /**
     * @return int
     */
    public function getContentObjectId()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * @param int $contentObjectId
     *
     * @return $this
     */
    public function setContentObjectId($contentObjectId)
    {
        if (empty($contentObjectId) || !is_integer($contentObjectId))
        {
            throw new InvalidArgumentException(
                'The given content object id must be a valid integer and must not be empty'
            );
        }

        $this->setDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID, $contentObjectId);

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxAttempts()
    {
        return $this->getDefaultProperty(self::PROPERTY_MAX_ATTEMPTS);
    }

    /**
     * @param int $maxAttempts
     *
     * @return $this
     */
    public function setMaxAttempts($maxAttempts)
    {
        if (!is_integer($maxAttempts))
        {
            throw new InvalidArgumentException(
                'The given max attempts must be a valid integer'
            );
        }

        $this->setDefaultProperty(self::PROPERTY_MAX_ATTEMPTS, $maxAttempts);

        return $this;
    }

    /**
     * @return int
     */
    public function getMasteryScore()
    {
        return $this->getDefaultProperty(self::PROPERTY_MASTERY_SCORE);
    }

    /**
     * @param int $masteryScore
     *
     * @return $this
     */
    public function setMasteryScore($masteryScore)
    {
        if (!is_integer($masteryScore))
        {
            throw new InvalidArgumentException('The given mastery score must be a valid integer');
        }

        if ($masteryScore < 0 || $masteryScore > 100)
        {
            throw new RangeException('The given mastery score must be between 0 and 100');
        }

        $this->setDefaultProperty(self::PROPERTY_MASTERY_SCORE, $masteryScore);

        return $this;
    }

    /**
     * @return boolean
     */
    public function getAllowHints()
    {
        return $this->getDefaultProperty(self::PROPERTY_ALLOW_HINTS);
    }

    /**
     * @param boolean $allowHints
     *
     * @return $this
     */
    public function setAllowHints($allowHints)
    {
        if (!is_bool($allowHints))
        {
            throw new InvalidArgumentException('The given allow hints must be a valid boolean');
        }

        $this->setDefaultProperty(self::PROPERTY_ALLOW_HINTS, $allowHints);

        return $this;
    }

    /**
     * @return boolean
     */
    public function getShowScore()
    {
        return $this->getDefaultProperty(self::PROPERTY_SHOW_SCORE);
    }

    /**
     * @param boolean $showScore
     *
     * @return $this
     */
    public function setShowScore($showScore)
    {
        if (!is_bool($showScore))
        {
            throw new InvalidArgumentException('The given show score must be a valid boolean');
        }

        $this->setDefaultProperty(self::PROPERTY_SHOW_SCORE, $showScore);

        return $this;
    }

    /**
     * @return boolean
     */
    public function getShowCorrection()
    {
        return $this->getDefaultProperty(self::PROPERTY_SHOW_CORRECTION);
    }

    /**
     * @param boolean $showCorrection
     *
     * @return $this
     */
    public function setShowCorrection($showCorrection)
    {
        if (!is_bool($showCorrection))
        {
            throw new InvalidArgumentException('The given show correction must be a valid boolean');
        }

        $this->setDefaultProperty(self::PROPERTY_SHOW_CORRECTION, $showCorrection);

        return $this;
    }

    /**
     * @return boolean
     */
    public function getShowSolution()
    {
        return $this->getDefaultProperty(self::PROPERTY_SHOW_SOLUTION);
    }

    /**
     * @param boolean $showSolution
     *
     * @return $this
     */
    public function setShowSolution($showSolution)
    {
        if (!is_bool($showSolution))
        {
            throw new InvalidArgumentException('The given show solution must be a valid boolean');
        }

        $this->setDefaultProperty(self::PROPERTY_SHOW_SOLUTION, $showSolution);

        return $this;
    }

    /**
     * @return int
     */
    public function getShowAnswerFeedback()
    {
        return $this->getDefaultProperty(self::PROPERTY_SHOW_ANSWER_FEEDBACK);
    }

    /**
     * @param int $showAnswerFeedback
     *
     * @return $this
     */
    public function setShowAnswerFeedback($showAnswerFeedback)
    {
        if (!is_integer($showAnswerFeedback))
        {
            throw new InvalidArgumentException('The given show answer feedback must be a valid integer');
        }

        $this->setDefaultProperty(self::PROPERTY_SHOW_ANSWER_FEEDBACK, $showAnswerFeedback);

        return $this;
    }

    /**
     * @return int
     */
    public function getFeedbackLocation()
    {
        return $this->getDefaultProperty(self::PROPERTY_FEEDBACK_LOCATION);
    }

    /**
     * @param int $feedbackLocation
     *
     * @return $this
     */
    public function setFeedbackLocation($feedbackLocation)
    {
        if (!is_integer($feedbackLocation))
        {
            throw new InvalidArgumentException('The given feedback location must be a valid integer');
        }

        $this->setDefaultProperty(self::PROPERTY_FEEDBACK_LOCATION, $feedbackLocation);

        return $this;
    }

    /**
     * @return bool
     */
    public function isBlocked()
    {
        return $this->getDefaultProperty(self::PROPERTY_BLOCKED);
    }

    /**
     * @param bool $blocked
     *
     * @return $this
     */
    public function setBlocked($blocked = false)
    {
        if (!is_bool($blocked))
        {
            throw new InvalidArgumentException('The given blocked must be a valid boolean');
        }

        $this->setDefaultProperty(self::PROPERTY_BLOCKED, $blocked);

        return $this;
    }

    /**
     * @return int
     */
    public function getDisplayOrder()
    {
        return $this->getDefaultProperty(self::PROPERTY_DISPLAY_ORDER);
    }

    /**
     * @param int $displayOrder
     *
     * @return $this
     */
    public function setDisplayOrder($displayOrder)
    {
        if (!is_integer($displayOrder))
        {
            throw new InvalidArgumentException('The given display order must be a valid integer');
        }

        $this->setDefaultProperty(self::PROPERTY_DISPLAY_ORDER, $displayOrder);

        return $this;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return (int) $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    /**
     * @param int $userId
     *
     * @return $this
     */
    public function setUserId($userId)
    {
        if (!is_integer($userId))
        {
            throw new InvalidArgumentException('The given user id must be a valid integer');
        }

        $this->setDefaultProperty(self::PROPERTY_USER_ID, $userId);

        return $this;
    }

    /**
     * @return int
     */
    public function getAddedDate()
    {
        return (int) $this->getDefaultProperty(self::PROPERTY_ADDED_DATE);
    }

    /**
     * @param int $addedDate
     *
     * @return $this
     */
    public function setAddedDate($addedDate)
    {
        if (!is_integer($addedDate))
        {
            throw new InvalidArgumentException('The given added date must be a valid integer');
        }

        $this->setDefaultProperty(self::PROPERTY_ADDED_DATE, $addedDate);

        return $this;
    }

    public function getDisplayOrderProperty(): PropertyConditionVariable
    {
        return new PropertyConditionVariable(self::class, self::PROPERTY_DISPLAY_ORDER);
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[]
     */
    public function getDisplayOrderContextProperties(): array
    {
        return array(
            new PropertyConditionVariable(self::class, self::PROPERTY_LEARNING_PATH_ID),
            new PropertyConditionVariable(self::class, self::PROPERTY_PARENT_LEARNING_PATH_CHILD_ID)
        );
    }

    /**
     * Sets whether or not the default traversing order should be enforced
     *
     * @param bool $enforceDefaultTraversingOrder
     */
    public function setEnforceDefaultTraversingOrder($enforceDefaultTraversingOrder = true)
    {
        if(!is_bool($enforceDefaultTraversingOrder))
        {
            throw new InvalidArgumentException('The given enforceDefaultTraversingOrder is no valid boolean');
        }

        $this->setDefaultProperty(self::PROPERTY_ENFORCE_DEFAULT_TRAVERSING_ORDER, $enforceDefaultTraversingOrder);
    }

    /**
     * Returns whether or not the default traversing order is enforced
     *
     * @return bool
     */
    public function enforcesDefaultTraversingOrder()
    {
        return (bool) $this->getDefaultProperty(self::PROPERTY_ENFORCE_DEFAULT_TRAVERSING_ORDER);
    }

    /**
     * Returns the assessment configuration
     *
     * @return Configuration
     */
    public function getAssessmentConfiguration()
    {
        return new Configuration(
            $this->getAllowHints(),
            $this->getShowScore(),
            $this->getShowCorrection(),
            $this->getShowSolution(),
            $this->getShowAnswerFeedback(),
            $this->getFeedbackLocation()
        );
    }

    /**
     * This data class needs the learning_path prefix in the database to make clear that the tree node data belongs
     * to the learning path
     *
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_learning_path_tree_node_data';
    }
}