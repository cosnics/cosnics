<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Attempt;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package core\repository\content_object\assessment\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class AbstractAttempt extends DataClass
{
    // Properties
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_TOTAL_SCORE = 'total_score';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_START_TIME = 'start_time';
    const PROPERTY_END_TIME = 'end_time';
    const PROPERTY_TOTAL_TIME = 'total_time';
    
    // Status
    const STATUS_NOT_COMPLETED = 1;
    const STATUS_COMPLETED = 2;

    /**
     *
     * @param string[] $extended_property_names
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_USER_ID;
        $extended_property_names[] = self :: PROPERTY_TOTAL_SCORE;
        $extended_property_names[] = self :: PROPERTY_STATUS;
        $extended_property_names[] = self :: PROPERTY_START_TIME;
        $extended_property_names[] = self :: PROPERTY_END_TIME;
        $extended_property_names[] = self :: PROPERTY_TOTAL_TIME;
        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     *
     * @return int
     */
    public function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     *
     * @param int $user_id
     */
    public function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    /**
     *
     * @return int
     */
    public function get_total_score()
    {
        return $this->get_default_property(self :: PROPERTY_TOTAL_SCORE);
    }

    /**
     *
     * @param int $total_score
     */
    public function set_total_score($total_score)
    {
        $this->set_default_property(self :: PROPERTY_TOTAL_SCORE, $total_score);
    }

    /**
     *
     * @return int
     */
    public function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    /**
     *
     * @param int $status
     */
    public function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    /**
     *
     * @return int
     */
    public function get_start_time()
    {
        return $this->get_default_property(self :: PROPERTY_START_TIME);
    }

    /**
     *
     * @param int $start_time
     */
    public function set_start_time($start_time)
    {
        $this->set_default_property(self :: PROPERTY_START_TIME, $start_time);
    }

    /**
     *
     * @return int
     */
    public function get_end_time()
    {
        return $this->get_default_property(self :: PROPERTY_END_TIME);
    }

    /**
     *
     * @param int $end_time
     */
    public function set_end_time($end_time)
    {
        $this->set_default_property(self :: PROPERTY_END_TIME, $end_time);
    }

    /**
     *
     * @return int
     */
    public function get_total_time()
    {
        return $this->get_default_property(self :: PROPERTY_TOTAL_TIME);
    }

    /**
     *
     * @param int $total_time
     */
    public function set_total_time($total_time)
    {
        $this->set_default_property(self :: PROPERTY_TOTAL_TIME, $total_time);
    }

    /**
     * Returns the status as a string
     * 
     * @return string
     */
    public function get_status_as_string()
    {
        return $this->get_status() == self :: STATUS_COMPLETED ? Translation :: get('Completed') : Translation :: get(
            'NotCompleted');
    }
}
