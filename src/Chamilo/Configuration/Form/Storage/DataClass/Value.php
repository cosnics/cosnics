<?php
namespace Chamilo\Configuration\Form\Storage\DataClass;

use Chamilo\Configuration\Form\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package configuration\form
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Value extends DataClass
{
    const PROPERTY_DYNAMIC_FORM_ELEMENT_ID = 'dynamic_form_element_id';

    const PROPERTY_TIME = 'time';

    const PROPERTY_USER_ID = 'user_id';

    const PROPERTY_VALUE = 'value';

    public function create()
    {
        $this->set_time(time());

        return parent::create();
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * Get the default properties of all user course categories.
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array(
                self::PROPERTY_DYNAMIC_FORM_ELEMENT_ID,
                self::PROPERTY_VALUE,
                self::PROPERTY_USER_ID,
                self::PROPERTY_TIME
            )
        );
    }

    public function get_dynamic_form_element_id()
    {
        return $this->get_default_property(self::PROPERTY_DYNAMIC_FORM_ELEMENT_ID);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'configuration_form_value';
    }

    public function get_time()
    {
        return $this->get_default_property(self::PROPERTY_TIME);
    }

    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    public function get_value()
    {
        return $this->get_default_property(self::PROPERTY_VALUE);
    }

    public function set_dynamic_form_element_id($dynamic_form_element_id)
    {
        $this->set_default_property(self::PROPERTY_DYNAMIC_FORM_ELEMENT_ID, $dynamic_form_element_id);
    }

    public function set_time($time)
    {
        $this->set_default_property(self::PROPERTY_TIME, $time);
    }

    public function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }

    public function set_value($value)
    {
        $this->set_default_property(self::PROPERTY_VALUE, $value);
    }
}
