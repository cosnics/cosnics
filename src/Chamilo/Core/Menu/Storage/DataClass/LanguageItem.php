<?php
namespace Chamilo\Core\Menu\Storage\DataClass;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;

/**
 *
 * @package Chamilo\Core\Menu\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class LanguageItem extends Item
{
    const PROPERTY_LANGUAGE = 'language';
    const CLASS_NAME = __CLASS__;

    public function __construct($default_properties = array(), $additional_properties = null)
    {
        parent :: __construct($default_properties, $additional_properties);
        $this->set_type(__CLASS__);
    }

    public static function get_type_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: CLASS_NAME);
    }

    public function get_language()
    {
        return $this->get_additional_property(self :: PROPERTY_LANGUAGE);
    }

    public function set_language($language)
    {
        return $this->set_additional_property(self :: PROPERTY_LANGUAGE, $language);
    }

    public static function get_additional_property_names()
    {
        return array(self :: PROPERTY_LANGUAGE);
    }

    public function getCurrentUrl()
    {
        return $this->currentUrl;
    }

    public function setCurrentUrl($currentUrl)
    {
        $this->currentUrl = $currentUrl;
    }

    /**
     *
     * @see \Chamilo\Core\Menu\Storage\DataClass\Item::is_selected()
     */
    public function is_selected()
    {
        $current_language = LocalSetting :: get('platform_language');

        if ($current_language == $this->get_language())
        {
            return true;
        }

        return false;
    }
}
