<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Describes a relation between a learning path and another content object
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class LearningPathChild extends DataClass implements DisplayOrderDataClassListenerSupport
{
    const PROPERTY_PARENT_LEARNING_PATH_ID = 'parent_learning_path_id';
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

    /**
     * LearningPathChild constructor.
     *
     * @param array $default_properties
     * @param array $additional_properties
     */
    public function __construct($default_properties = array(), $additional_properties = null)
    {
        parent::__construct($default_properties, $additional_properties);
        $this->add_listener(new DisplayOrderDataClassListener($this));
    }

    /**
     * @return string[]
     */
    public static function get_default_property_names()
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_PARENT_LEARNING_PATH_ID,
                self::PROPERTY_CONTENT_OBJECT_ID,
                self::PROPERTY_MAX_ATTEMPTS,
                self::PROPERTY_MASTERY_SCORE,
                self::PROPERTY_ALLOW_HINTS,
                self::PROPERTY_SHOW_SCORE,
                self::PROPERTY_SHOW_CORRECTION,
                self::PROPERTY_SHOW_SOLUTION,
                self::PROPERTY_SHOW_ANSWER_FEEDBACK,
                self::PROPERTY_FEEDBACK_LOCATION
            )
        );
    }

    /**
     * @return int
     */
    public function getParentLearningPathId()
    {
        return $this->get_default_property(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * @param int $parentLearningPathId
     *
     * @return $this
     */
    public function setParentLearningPathId($parentLearningPathId)
    {
        if (empty($parentLearningPathId) || !is_integer($parentLearningPathId))
        {
            throw new \InvalidArgumentException(
                'The given learning path id must be a valid integer and must not be empty'
            );
        }

        $this->set_default_property(self::PROPERTY_CONTENT_OBJECT_ID, $parentLearningPathId);

        return $this;
    }

    /**
     * @return int
     */
    public function getContentObjectId()
    {
        return $this->get_default_property(self::PROPERTY_CONTENT_OBJECT_ID);
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
            throw new \InvalidArgumentException(
                'The given content object id must be a valid integer and must not be empty'
            );
        }

        $this->set_default_property(self::PROPERTY_CONTENT_OBJECT_ID, $contentObjectId);

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxAttempts()
    {
        return $this->get_default_property(self::PROPERTY_MAX_ATTEMPTS);
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
            throw new \InvalidArgumentException(
                'The given max attempts must be a valid integer'
            );
        }

        $this->set_default_property(self::PROPERTY_MAX_ATTEMPTS, $maxAttempts);

        return $this;
    }

    /**
     * @return int
     */
    public function getMasteryScore()
    {
        return $this->get_default_property(self::PROPERTY_MASTERY_SCORE);
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
            throw new \InvalidArgumentException('The given mastery score must be a valid integer');
        }

        if($masteryScore < 0 || $masteryScore > 100)
        {
            throw new \RangeException('The given mastery score must be between 0 and 100');
        }

        $this->set_default_property(self::PROPERTY_MASTERY_SCORE, $masteryScore);

        return $this;
    }

    /**
     * @return boolean
     */
    public function getAllowHints()
    {
        return $this->get_default_property(self::PROPERTY_ALLOW_HINTS);
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
            throw new \InvalidArgumentException('The given allow hints must be a valid boolean');
        }

        $this->set_default_property(self::PROPERTY_ALLOW_HINTS, $allowHints);

        return $this;
    }

    /**
     * @return boolean
     */
    public function getShowScore()
    {
        return $this->get_default_property(self::PROPERTY_SHOW_SCORE);
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
            throw new \InvalidArgumentException('The given show score must be a valid boolean');
        }

        $this->set_default_property(self::PROPERTY_SHOW_SCORE, $showScore);

        return $this;
    }

    /**
     * @return boolean
     */
    public function getShowCorrection()
    {
        return $this->get_default_property(self::PROPERTY_SHOW_CORRECTION);
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
            throw new \InvalidArgumentException('The given show correction must be a valid boolean');
        }

        $this->set_default_property(self::PROPERTY_SHOW_CORRECTION, $showCorrection);

        return $this;
    }

    /**
     * @return boolean
     */
    public function getShowSolution()
    {
        return $this->get_default_property(self::PROPERTY_SHOW_SOLUTION);
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
            throw new \InvalidArgumentException('The given show solution must be a valid boolean');
        }

        $this->set_default_property(self::PROPERTY_SHOW_SOLUTION, $showSolution);

        return $this;
    }

    /**
     * @return int
     */
    public function getShowAnswerFeedback()
    {
        return $this->get_default_property(self::PROPERTY_SHOW_ANSWER_FEEDBACK);
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
            throw new \InvalidArgumentException('The given show answer feedback must be a valid integer');
        }

        $this->set_default_property(self::PROPERTY_SHOW_ANSWER_FEEDBACK, $showAnswerFeedback);

        return $this;
    }

    /**
     * @return int
     */
    public function getFeedbackLocation()
    {
        return $this->get_default_property(self::PROPERTY_FEEDBACK_LOCATION);
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
            throw new \InvalidArgumentException('The given feedback location must be a valid integer');
        }

        $this->set_default_property(self::PROPERTY_FEEDBACK_LOCATION, $feedbackLocation);

        return $this;
    }

    /**
     * @return bool
     */
    public function isBlocked()
    {
        return $this->get_default_property(self::PROPERTY_BLOCKED);
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
            throw new \InvalidArgumentException('The given blocked must be a valid boolean');
        }

        $this->set_default_property(self::PROPERTY_BLOCKED, $blocked);

        return $this;
    }

    /**
     * @return int
     */
    public function getDisplayOrder()
    {
        return $this->get_default_property(self::PROPERTY_BLOCKED);
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
            throw new \InvalidArgumentException('The given display order must be a valid integer');
        }

        $this->set_default_property(self::PROPERTY_BLOCKED, $displayOrder);

        return $this;
    }

    /**
     * Returns the property for the display order
     *
     * @return string
     */
    public function get_display_order_property()
    {
        return new PropertyConditionVariable(self::class_name(), self::PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Returns the properties that define the context for the display order (the properties on which has to be limited)
     *
     * @return PropertyConditionVariable[]
     */
    public function get_display_order_context_properties()
    {
        return array(new PropertyConditionVariable(self::class_name(), self::PROPERTY_PARENT_LEARNING_PATH_ID));
    }
}