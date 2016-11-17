<?php
namespace Chamilo\Core\Tracking\Storage\DataClass;

/**
 *
 * @author Hans De Bisschop
 */
abstract class ChangesTracker extends SimpleTracker
{
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_REFERENCE_ID = 'reference_id';
    const PROPERTY_ACTION = 'action';
    const PROPERTY_DATE = 'date';

    /**
     * Get the default properties of all aggregate trackers.
     * 
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(self::PROPERTY_USER_ID, self::PROPERTY_REFERENCE_ID, self::PROPERTY_ACTION, self::PROPERTY_DATE));
    }

    /**
     *
     * @return the $user_id
     */
    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    /**
     *
     * @param $user_id the $user_id to set
     */
    public function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }

    /**
     *
     * @return the $reference_id
     */
    public function get_reference_id()
    {
        return $this->get_default_property(self::PROPERTY_REFERENCE_ID);
    }

    /**
     *
     * @param $reference_id the $reference_id to set
     */
    public function set_reference_id($reference_id)
    {
        $this->set_default_property(self::PROPERTY_REFERENCE_ID, $reference_id);
    }

    /**
     *
     * @return the $action
     */
    public function get_action()
    {
        return $this->get_default_property(self::PROPERTY_ACTION);
    }

    /**
     *
     * @param $action the $action to set
     */
    public function set_action($action)
    {
        $this->set_default_property(self::PROPERTY_ACTION, $action);
    }

    /**
     *
     * @return the $date
     */
    public function get_date()
    {
        return $this->get_default_property(self::PROPERTY_DATE);
    }

    /**
     *
     * @param $date the $date to set
     */
    public function set_date($date)
    {
        $this->set_default_property(self::PROPERTY_DATE, $date);
    }

    /**
     * Implemented
     * 
     * @param array $parameters
     */
    public function validate_parameters(array $parameters = array())
    {
        $this->set_user_id($parameters[self::PROPERTY_USER_ID]);
        $this->set_reference_id($parameters[self::PROPERTY_REFERENCE_ID]);
        $this->set_action($this->get_event()->get_name());
        $this->set_date(time());
    }
}
