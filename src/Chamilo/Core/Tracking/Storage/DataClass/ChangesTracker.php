<?php
namespace Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Manager;

/**
 * @package Chamilo\Core\Tracking\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class ChangesTracker extends SimpleTracker
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_ACTION = 'action';

    public const PROPERTY_DATE = 'date';

    public const PROPERTY_REFERENCE_ID = 'reference_id';

    public const PROPERTY_USER_ID = 'user_id';

    /**
     * Get the default properties of all aggregate trackers.
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [self::PROPERTY_USER_ID, self::PROPERTY_REFERENCE_ID, self::PROPERTY_ACTION, self::PROPERTY_DATE]
        );
    }

    /**
     * @return the $action
     */
    public function get_action()
    {
        return $this->getDefaultProperty(self::PROPERTY_ACTION);
    }

    /**
     * @return the $date
     */
    public function get_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_DATE);
    }

    /**
     * @return the $reference_id
     */
    public function get_reference_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_REFERENCE_ID);
    }

    /**
     * @return the $user_id
     */
    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    /**
     * @param $action the $action to set
     */
    public function set_action($action)
    {
        $this->setDefaultProperty(self::PROPERTY_ACTION, $action);
    }

    /**
     * @param $date the $date to set
     */
    public function set_date($date)
    {
        $this->setDefaultProperty(self::PROPERTY_DATE, $date);
    }

    /**
     * @param $reference_id the $reference_id to set
     */
    public function set_reference_id($reference_id)
    {
        $this->setDefaultProperty(self::PROPERTY_REFERENCE_ID, $reference_id);
    }

    /**
     * @param $user_id the $user_id to set
     */
    public function set_user_id($user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }

    /**
     * Implemented
     *
     * @param array $parameters
     */
    public function validate_parameters(array $parameters = [])
    {
        $this->set_user_id($parameters[self::PROPERTY_USER_ID]);
        $this->set_reference_id($parameters[self::PROPERTY_REFERENCE_ID]);
        $this->set_action($this->get_event()->get_name());
        $this->set_date(time());
    }
}
