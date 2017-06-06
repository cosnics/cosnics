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
    const PROPERTY_LEARNING_PATH_ATTEMPT_ID = 'learning_path_attempt_id';
    const PROPERTY_LEARNING_PATH_ITEM_ID = 'learning_path_item_id';
    const PROPERTY_START_TIME = 'start_time';
    const PROPERTY_TOTAL_TIME = 'total_time';
    const PROPERTY_SCORE = 'score';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_LESSON_LOCATION = 'lesson_location';
    const PROPERTY_SUSPEND_DATA = 'suspend_data';
    const PROPERTY_MAX_SCORE = 'max_score';
    const PROPERTY_MIN_SCORE = 'min_score';
    
    // Status
    const STATUS_COMPLETED = 'completed';
    const STATUS_NOT_ATTEMPTED = 'not attempted';
    const STATUS_PASSED = 'passed';
    const STATUS_FAILED = 'failed';

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_LEARNING_PATH_ATTEMPT_ID, 
                self::PROPERTY_LEARNING_PATH_ITEM_ID, 
                self::PROPERTY_START_TIME, 
                self::PROPERTY_TOTAL_TIME, 
                self::PROPERTY_SCORE, 
                self::PROPERTY_STATUS, 
                self::PROPERTY_LESSON_LOCATION, 
                self::PROPERTY_SUSPEND_DATA, 
                self::PROPERTY_MAX_SCORE, 
                self::PROPERTY_MIN_SCORE));
    }

    public function get_learning_path_attempt_id()
    {
        return $this->get_default_property(self::PROPERTY_LEARNING_PATH_ATTEMPT_ID);
    }

    public function set_learning_path_attempt_id($learning_path_attempt_id)
    {
        $this->set_default_property(self::PROPERTY_LEARNING_PATH_ATTEMPT_ID, $learning_path_attempt_id);
    }

    public function get_start_time()
    {
        return $this->get_default_property(self::PROPERTY_START_TIME);
    }

    public function set_start_time($start_time)
    {
        $this->set_default_property(self::PROPERTY_START_TIME, $start_time);
    }

    public function get_learning_path_item_id()
    {
        return $this->get_default_property(self::PROPERTY_LEARNING_PATH_ITEM_ID);
    }

    public function set_learning_path_item_id($learning_path_item_id)
    {
        $this->set_default_property(self::PROPERTY_LEARNING_PATH_ITEM_ID, $learning_path_item_id);
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

    public function get_lesson_location()
    {
        return $this->get_default_property(self::PROPERTY_LESSON_LOCATION);
    }

    public function set_lesson_location($lesson_location)
    {
        $this->set_default_property(self::PROPERTY_LESSON_LOCATION, $lesson_location);
    }

    public function get_suspend_data()
    {
        return $this->get_default_property(self::PROPERTY_SUSPEND_DATA);
    }

    public function set_suspend_data($suspend_data)
    {
        $this->set_default_property(self::PROPERTY_SUSPEND_DATA, $suspend_data);
    }

    public function get_min_score()
    {
        return $this->get_default_property(self::PROPERTY_MIN_SCORE);
    }

    public function set_min_score($min_score)
    {
        $this->set_default_property(self::PROPERTY_MIN_SCORE, $min_score);
    }

    public function get_max_score()
    {
        return $this->get_default_property(self::PROPERTY_MAX_SCORE);
    }

    public function set_max_score($max_score)
    {
        $this->set_default_property(self::PROPERTY_MAX_SCORE, $max_score);
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
