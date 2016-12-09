<?php
namespace Chamilo\Configuration\Package\Properties\Dependencies\Dependency;

use Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: settings.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * 
 * @package admin.lib.package_installer.dependency
 */
class SettingsDependency extends Dependency
{
    const PROPERTY_VALUE = 'value';

    private $value;

    /**
     *
     * @return the $value
     */
    public function get_value()
    {
        return $this->value;
    }

    /**
     *
     * @param $value the $value to set
     */
    public function set_value($value)
    {
        $this->value = $value;
    }

    public function check()
    {
        $setting = ini_get($this->get_id());
        $message = Translation::get('DependencyCheckSetting') . ': ' . $this->as_html() . ' ' . Translation::get(
            'Found', 
            array(), 
            Utilities::COMMON_LIBRARIES) . ': ' . $setting;
        $value = $this->get_value();
        $this->logger->add_message($message);
        return $this->compare($value['type'], $value['_content'], $setting);
    }

    /**
     *
     * @param string $context
     * @return boolean
     */
    public function needs($context)
    {
        return false;
    }

    public function as_html()
    {
        $value = $this->get_value();
        return $this->get_id() . '. ' . Translation::get('Expecting', array(), Utilities::COMMON_LIBRARIES) . ': ' .
             $value['_content'];
    }

    public static function dom_node($dom_xpath, $dom_node)
    {
        $dependency = parent::dom_node($dom_xpath, $dom_node);
        $dependency->set_value($dom_xpath->query('value', $dom_node)->item(0)->nodeValue);
        return $dependency;
    }
}
