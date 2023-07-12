<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;

/**
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass
 */
class Visit extends SimpleTracker
{
    use DependencyInjectionContainerTrait;

    public const CONTEXT = 'Chamilo\Core\User\Integration\Chamilo\Core\Tracking';

    public const PROPERTY_ENTER_DATE = 'enter_date';
    public const PROPERTY_LEAVE_DATE = 'leave_date';
    public const PROPERTY_LOCATION = 'location';
    public const PROPERTY_USER_ID = 'user_id';
    public const TYPE_ENTER = 'enter';
    public const TYPE_LEAVE = 'leave';

    public function run(array $parameters = [])
    {
        $this->validate_parameters($parameters);
        $type = $this->get_event()->getType();

        switch ($type)
        {
            default :
                break;
            case self::TYPE_ENTER :
                $this->track_enter($parameters);
                break;
            case self::TYPE_LEAVE :
                $this->track_leave($parameters);
                break;
        }
    }

    /**
     * Inherited
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_USER_ID,
                self::PROPERTY_ENTER_DATE,
                self::PROPERTY_LEAVE_DATE,
                self::PROPERTY_LOCATION
            ]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'tracking_user_visit';
    }

    /**
     * Get's the enter date of the visit tracker
     *
     * @return int $date the date
     */
    public function get_enter_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTER_DATE);
    }

    /**
     * Get's the leave date of the visit tracker
     *
     * @return int $date the date
     */
    public function get_leave_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_LEAVE_DATE);
    }

    /**
     * Get's the location of the visit tracker
     *
     * @return int $ip the ip
     */
    public function get_location()
    {
        return $this->getDefaultProperty(self::PROPERTY_LOCATION);
    }

    /**
     * Get's the userid of the visit tracker
     *
     * @return int $userid the userid
     */
    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    private function init_enter()
    {
        $this->set_enter_date(time());
        $this->set_leave_date(time());
    }

    private function init_leave()
    {
        $this->set_leave_date(time());
    }

    /**
     * Sets the enter date of the visit tracker
     *
     * @param int $date the date
     */
    public function set_enter_date($value)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTER_DATE, $value);
    }

    /**
     * Sets the leave date of the visit tracker
     *
     * @param int $date the date
     */
    public function set_leave_date($value)
    {
        $this->setDefaultProperty(self::PROPERTY_LEAVE_DATE, $value);
    }

    /**
     * Sets the location of the visit tracker
     *
     * @param int $ip the ip
     */
    public function set_location($value)
    {
        $this->setDefaultProperty(self::PROPERTY_LOCATION, $value);
    }

    /**
     * Sets the userid of the visit tracker
     *
     * @param int $userid the userid
     */
    public function set_user_id($userid)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $userid);
    }

    private function track_enter()
    {
        $this->init_enter();
        $success = $this->create();
        if ($success)
        {
            $tracker_id = $this->get_id();
            $html_header = "<script>var tracker={$tracker_id};</script>";

            $this->getPageConfiguration()->addHtmlHeader($html_header);
        }
    }

    private function track_leave()
    {
        $this->init_leave();

        return $this->update();
    }

    public function validate_parameters(array $parameters = [])
    {
        if (isset($parameters[self::PROPERTY_USER_ID]))
        {
            $this->set_user_id($parameters[self::PROPERTY_USER_ID]);
        }
        if (isset($parameters[self::PROPERTY_LOCATION]))
        {
            $this->set_location($parameters[self::PROPERTY_LOCATION]);
        }
        if (isset($parameters[self::PROPERTY_ID]))
        {
            $this->set_id($parameters[self::PROPERTY_ID]);
        }
    }
}
