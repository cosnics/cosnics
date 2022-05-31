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
class Option extends DataClass
{
    const PROPERTY_DISPLAY_ORDER = 'display_order';

    const PROPERTY_DYNAMIC_FORM_ELEMENT_ID = 'dynamic_form_element_id';

    const PROPERTY_NAME = 'name';

    public function create(): bool
    {
        $this->set_display_order(
            DataManager::select_next_dynamic_form_element_option_order($this->get_dynamic_form_element_id())
        );

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
            array(self::PROPERTY_DYNAMIC_FORM_ELEMENT_ID, self::PROPERTY_NAME, self::PROPERTY_DISPLAY_ORDER)
        );
    }

    public function get_display_order()
    {
        return $this->getDefaultProperty(self::PROPERTY_DISPLAY_ORDER);
    }

    public function get_dynamic_form_element_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_DYNAMIC_FORM_ELEMENT_ID);
    }

    public function get_name()
    {
        return $this->getDefaultProperty(self::PROPERTY_NAME);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'configuration_form_option';
    }

    public function set_display_order($display_order)
    {
        $this->setDefaultProperty(self::PROPERTY_DISPLAY_ORDER, $display_order);
    }

    public function set_dynamic_form_element_id($dynamic_form_element_id)
    {
        $this->setDefaultProperty(self::PROPERTY_DYNAMIC_FORM_ELEMENT_ID, $dynamic_form_element_id);
    }

    public function set_name($name)
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);
    }
}
