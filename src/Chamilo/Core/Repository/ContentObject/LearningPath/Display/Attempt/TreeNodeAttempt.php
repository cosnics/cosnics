<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class TreeNodeAttempt extends DataClass
{
    // Properties
    const PROPERTY_LEARNING_PATH_ID = 'learning_path_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_TREE_NODE_DATA_ID = 'tree_node_data_id';
    const PROPERTY_START_TIME = 'start_time';
    const PROPERTY_TOTAL_TIME = 'total_time';
    const PROPERTY_SCORE = 'score';
    const PROPERTY_COMPLETED = 'completed';

    public static function get_default_property_names($extended_property_names = [])
    {
        $extended_property_names[] = self::PROPERTY_LEARNING_PATH_ID;
        $extended_property_names[] = self::PROPERTY_USER_ID;
        $extended_property_names[] = self::PROPERTY_TREE_NODE_DATA_ID;
        $extended_property_names[] = self::PROPERTY_START_TIME;
        $extended_property_names[] = self::PROPERTY_TOTAL_TIME;
        $extended_property_names[] = self::PROPERTY_SCORE;
        $extended_property_names[] = self::PROPERTY_COMPLETED;

        return parent::get_default_property_names($extended_property_names);
    }

    public function get_start_time()
    {
        return $this->get_default_property(self::PROPERTY_START_TIME);
    }

    public function set_start_time($start_time)
    {
        $this->set_default_property(self::PROPERTY_START_TIME, $start_time);

        return $this;
    }

    public function getTreeNodeDataId()
    {
        return $this->get_default_property(self::PROPERTY_TREE_NODE_DATA_ID);
    }

    public function setTreeNodeDataId($learning_path_item_id)
    {
        $this->set_default_property(self::PROPERTY_TREE_NODE_DATA_ID, $learning_path_item_id);

        return $this;
    }

    public function get_total_time()
    {
        return $this->get_default_property(self::PROPERTY_TOTAL_TIME);
    }

    public function set_total_time($total_time)
    {
        $this->set_default_property(self::PROPERTY_TOTAL_TIME, $total_time);

        return $this;
    }

    public function get_score()
    {
        return $this->get_default_property(self::PROPERTY_SCORE);
    }

    public function set_score($score)
    {
        $this->set_default_property(self::PROPERTY_SCORE, $score);

        return $this;
    }

    /**
     * @return bool
     */
    public function isCompleted()
    {
        return $this->get_default_property(self::PROPERTY_COMPLETED);
    }

    /**
     * @param bool $completed
     *
     * @return $this
     */
    public function setCompleted($completed = true)
    {
        $this->set_default_property(self::PROPERTY_COMPLETED, $completed);

        return $this;
    }

    /**
     * @return int
     */
    public function getLearningPathId()
    {
        return (int) $this->get_default_property(self::PROPERTY_LEARNING_PATH_ID);
    }

    /**
     * @param int $learningPathId
     *
     * @return $this
     */
    public function setLearningPathId($learningPathId)
    {
        $this->set_default_property(self::PROPERTY_LEARNING_PATH_ID, $learningPathId);

        return $this;
    }

    /**
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    /**
     *
     * @param int $user_id
     *
     * @return $this
     */
    public function setUserId($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);

        return $this;
    }

    /**
     * Calculates and sets the total time
     */
    public function calculateAndSetTotalTime()
    {
        $this->set_total_time($this->get_total_time() + (time() - $this->get_start_time()));

        return $this;
    }
}
