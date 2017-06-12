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
    const PROPERTY_STATUS = 'status';

    // Status
    const STATUS_COMPLETED = 'completed';
    const STATUS_NOT_ATTEMPTED = 'not attempted';
    const STATUS_PASSED = 'passed';
    const STATUS_FAILED = 'failed';

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_LEARNING_PATH_ID,
                self::PROPERTY_USER_ID,
                self::PROPERTY_TREE_NODE_DATA_ID,
                self::PROPERTY_START_TIME,
                self::PROPERTY_TOTAL_TIME,
                self::PROPERTY_SCORE,
                self::PROPERTY_STATUS
            )
        );
    }

    public function get_start_time()
    {
        return $this->get_default_property(self::PROPERTY_START_TIME);
    }

    public function set_start_time($start_time)
    {
        $this->set_default_property(self::PROPERTY_START_TIME, $start_time);
    }

    public function getTreeNodeDataId()
    {
        return $this->get_default_property(self::PROPERTY_TREE_NODE_DATA_ID);
    }

    public function setTreeNodeDataId($learning_path_item_id)
    {
        $this->set_default_property(self::PROPERTY_TREE_NODE_DATA_ID, $learning_path_item_id);
    }

    public function get_total_time()
    {
        return $this->get_default_property(self::PROPERTY_TOTAL_TIME);
    }

    public function set_total_time($total_time)
    {
        $this->set_default_property(self::PROPERTY_TOTAL_TIME, $total_time);
    }

    public function get_score()
    {
        return $this->get_default_property(self::PROPERTY_SCORE);
    }

    public function set_score($score)
    {
        $this->set_default_property(self::PROPERTY_SCORE, $score);
    }

    public function get_status()
    {
        return $this->get_default_property(self::PROPERTY_STATUS);
    }

    public function set_status($status)
    {
        $this->set_default_property(self::PROPERTY_STATUS, $status);
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
     */
    public function setLearningPathId($learningPathId)
    {
        $this->set_default_property(self::PROPERTY_LEARNING_PATH_ID, $learningPathId);
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
     */
    public function setUserId($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }

    /**
     * @return bool
     */
    public function isFinished()
    {
        return $this->get_status() == self::STATUS_COMPLETED || $this->get_status() == self::STATUS_PASSED;
    }

    /**
     * Calculates and sets the total time
     */
    public function calculateAndSetTotalTime()
    {
        $this->set_total_time($this->get_total_time() + (time() - $this->get_start_time()));
    }
}
