<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Attempt;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package core\repository\content_object\assessment\display
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class AbstractAttempt extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_END_TIME = 'end_time';
    public const PROPERTY_START_TIME = 'start_time';
    public const PROPERTY_STATUS = 'status';
    public const PROPERTY_TOTAL_SCORE = 'total_score';
    public const PROPERTY_TOTAL_TIME = 'total_time';
    public const PROPERTY_USER_ID = 'user_id';

    public const STATUS_COMPLETED = 2;
    public const STATUS_NOT_COMPLETED = 1;

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_USER_ID;
        $extendedPropertyNames[] = self::PROPERTY_TOTAL_SCORE;
        $extendedPropertyNames[] = self::PROPERTY_STATUS;
        $extendedPropertyNames[] = self::PROPERTY_START_TIME;
        $extendedPropertyNames[] = self::PROPERTY_END_TIME;
        $extendedPropertyNames[] = self::PROPERTY_TOTAL_TIME;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * @return int
     */
    public function get_end_time()
    {
        return $this->getDefaultProperty(self::PROPERTY_END_TIME);
    }

    /**
     * @return int
     */
    public function get_start_time()
    {
        return $this->getDefaultProperty(self::PROPERTY_START_TIME);
    }

    /**
     * @return int
     */
    public function get_status()
    {
        return $this->getDefaultProperty(self::PROPERTY_STATUS);
    }

    /**
     * Returns the status as a string
     *
     * @return string
     */
    public function get_status_as_string()
    {
        return $this->get_status() == self::STATUS_COMPLETED ? Translation::get('Completed') : Translation::get(
            'NotCompleted'
        );
    }

    /**
     * @return int
     */
    public function get_total_score()
    {
        return $this->getDefaultProperty(self::PROPERTY_TOTAL_SCORE);
    }

    /**
     * @return int
     */
    public function get_total_time()
    {
        return $this->getDefaultProperty(self::PROPERTY_TOTAL_TIME);
    }

    /**
     * @return int
     */
    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    /**
     * @param int $end_time
     */
    public function set_end_time($end_time)
    {
        $this->setDefaultProperty(self::PROPERTY_END_TIME, $end_time);
    }

    /**
     * @param int $start_time
     */
    public function set_start_time($start_time)
    {
        $this->setDefaultProperty(self::PROPERTY_START_TIME, $start_time);
    }

    /**
     * @param int $status
     */
    public function set_status($status)
    {
        $this->setDefaultProperty(self::PROPERTY_STATUS, $status);
    }

    /**
     * @param int $total_score
     */
    public function set_total_score($total_score)
    {
        $this->setDefaultProperty(self::PROPERTY_TOTAL_SCORE, $total_score);
    }

    /**
     * @param int $total_time
     */
    public function set_total_time($total_time)
    {
        $this->setDefaultProperty(self::PROPERTY_TOTAL_TIME, $total_time);
    }

    /**
     * @param int $user_id
     */
    public function set_user_id($user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }
}
