<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Configuration;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNodeConfigurationInterface;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

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
    const PROPERTY_CONFIGURATION = 'configuration';
    const PROPERTY_CONFIGURATION_CLASS = 'configuration_class';

    /**
     * TreeNodeData constructor.
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
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
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
                self::PROPERTY_ENFORCE_DEFAULT_TRAVERSING_ORDER,
                self::PROPERTY_CONFIGURATION,
                self::PROPERTY_CONFIGURATION_CLASS
            )
        );
    }

    /**
     * @return int
     */
    public function getLearningPathId()
    {
        return $this->get_default_property(self::PROPERTY_LEARNING_PATH_ID);
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
            throw new \InvalidArgumentException(
                'The given learning path id must be a valid integer and must not be empty'
            );
        }

        $this->set_default_property(self::PROPERTY_LEARNING_PATH_ID, $learningPathId);

        return $this;
    }

    /**
     * @return int
     */
    public function getParentTreeNodeDataId()
    {
        return $this->get_default_property(self::PROPERTY_PARENT_LEARNING_PATH_CHILD_ID);
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
            throw new \InvalidArgumentException(
                'The given parent learning path child id must be a valid integer'
            );
        }

        $this->set_default_property(self::PROPERTY_PARENT_LEARNING_PATH_CHILD_ID, $parentTreeNodeDataId);

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

        if ($masteryScore < 0 || $masteryScore > 100)
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
        return $this->get_default_property(self::PROPERTY_DISPLAY_ORDER);
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

        $this->set_default_property(self::PROPERTY_DISPLAY_ORDER, $displayOrder);

        return $this;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return (int) $this->get_default_property(self::PROPERTY_USER_ID);
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
            throw new \InvalidArgumentException('The given user id must be a valid integer');
        }

        $this->set_default_property(self::PROPERTY_USER_ID, $userId);

        return $this;
    }

    /**
     * @return int
     */
    public function getAddedDate()
    {
        return (int) $this->get_default_property(self::PROPERTY_ADDED_DATE);
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
            throw new \InvalidArgumentException('The given added date must be a valid integer');
        }

        $this->set_default_property(self::PROPERTY_ADDED_DATE, $addedDate);

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
        return array(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_LEARNING_PATH_ID),
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_PARENT_LEARNING_PATH_CHILD_ID)
        );
    }

    /**
     * Sets whether or not the default traversing order should be enforced
     *
     * @param bool $enforceDefaultTraversingOrder
     */
    public function setEnforceDefaultTraversingOrder($enforceDefaultTraversingOrder = true)
    {
        if (!is_bool($enforceDefaultTraversingOrder))
        {
            throw new \InvalidArgumentException('The given enforceDefaultTraversingOrder is no valid boolean');
        }

        $this->set_default_property(self::PROPERTY_ENFORCE_DEFAULT_TRAVERSING_ORDER, $enforceDefaultTraversingOrder);
    }

    /**
     * Returns whether or not the default traversing order is enforced
     *
     * @return bool
     */
    public function enforcesDefaultTraversingOrder()
    {
        return (bool) $this->get_default_property(self::PROPERTY_ENFORCE_DEFAULT_TRAVERSING_ORDER);
    }

    /**
     * @param string $configurationJSONString
     */
    public function setConfiguration(string $configurationJSONString)
    {
        json_decode($configurationJSONString);
        if (json_last_error() != JSON_ERROR_NONE)
        {
            throw new \InvalidArgumentException('The given configuration string is not a valid JSON');
        }

        $this->set_default_property(self::PROPERTY_CONFIGURATION, $configurationJSONString);
    }

    /**
     * @return string
     */
    public function getConfiguration(): ?string
    {
        return $this->get_default_property(self::PROPERTY_CONFIGURATION);
    }

    /**
     * @param string $configurationClass
     */
    public function setConfigurationClass(string $configurationClass)
    {
        $interfaces = class_implements($configurationClass);

        if (!class_exists($configurationClass) || !array_key_exists(TreeNodeConfigurationInterface::class, $interfaces))
        {
            throw new \InvalidArgumentException(
                'The given configuration class must be a valid instance of TreeNodeConfigurationInterface'
            );
        }

        $this->set_default_property(self::PROPERTY_CONFIGURATION_CLASS, $configurationClass);
    }

    /**
     * @return null|string
     */
    public function getConfigurationClass(): ?string
    {
        return $this->get_default_property(self::PROPERTY_CONFIGURATION_CLASS);
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
    public static function get_table_name()
    {
        return 'repository_learning_path_tree_node_data';
    }
}