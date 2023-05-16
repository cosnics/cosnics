<?php
namespace Chamilo\Configuration\Form\Storage\DataClass;

use Chamilo\Configuration\Form\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package configuration\form
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Value extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_DYNAMIC_FORM_ELEMENT_ID = 'dynamic_form_element_id';
    public const PROPERTY_TIME = 'time';
    public const PROPERTY_USER_ID = 'user_id';
    public const PROPERTY_VALUE = 'value';

    public function create(): bool
    {
        $this->set_time(time());

        return parent::create();
    }

    /**
     * Get the default properties of all user course categories.
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_DYNAMIC_FORM_ELEMENT_ID,
                self::PROPERTY_VALUE,
                self::PROPERTY_USER_ID,
                self::PROPERTY_TIME
            ]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'configuration_form_value';
    }

    public function get_dynamic_form_element_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_DYNAMIC_FORM_ELEMENT_ID);
    }

    public function get_time()
    {
        return $this->getDefaultProperty(self::PROPERTY_TIME);
    }

    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function get_value()
    {
        return $this->getDefaultProperty(self::PROPERTY_VALUE);
    }

    public function set_dynamic_form_element_id($dynamic_form_element_id)
    {
        $this->setDefaultProperty(self::PROPERTY_DYNAMIC_FORM_ELEMENT_ID, $dynamic_form_element_id);
    }

    public function set_time($time)
    {
        $this->setDefaultProperty(self::PROPERTY_TIME, $time);
    }

    public function set_user_id($user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }

    public function set_value($value)
    {
        $this->setDefaultProperty(self::PROPERTY_VALUE, $value);
    }
}
