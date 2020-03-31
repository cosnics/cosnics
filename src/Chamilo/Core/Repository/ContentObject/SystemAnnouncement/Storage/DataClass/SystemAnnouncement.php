<?php
namespace Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Translation\Translation;

class SystemAnnouncement extends ContentObject implements Versionable
{
    const ICON_CONFIG = 6;
    const ICON_CONFIRMATION = 1;
    const ICON_ERROR = 2;
    const ICON_QUESTION = 5;
    const ICON_STOP = 4;
    const ICON_WARNING = 3;

    const PROPERTY_ICON = 'icon';

    /**
     * @return string
     */
    public function getDefaultGlyphNamespace()
    {
        return self::package() . '\Icon\\' . $this->get_icon();
    }

    public static function get_additional_property_names()
    {
        return array(self::PROPERTY_ICON);
    }

    public function get_icon()
    {
        return $this->get_additional_property(self::PROPERTY_ICON);
    }

    public static function get_possible_icons()
    {
        $icons = array();

        $icons[self::ICON_CONFIRMATION] = Translation::get('Confirmation');
        $icons[self::ICON_ERROR] = Translation::get('Error');
        $icons[self::ICON_WARNING] = Translation::get('Warning');
        $icons[self::ICON_STOP] = Translation::get('Stop');
        $icons[self::ICON_QUESTION] = Translation::get('Question');
        $icons[self::ICON_CONFIG] = Translation::get('Config');

        return $icons;
    }

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    public static function icon_name($icon, $size = IdentGlyph::SIZE_SMALL)
    {
        // if icon is empty: return size as icon apht, to prevent trailing underscore
        if ($icon == "")
        {
            return $size;
        }

        switch ($icon)
        {
            case self::ICON_CONFIRMATION :
                $icon = 'Confirmation';
                break;
            case self::ICON_ERROR :
                $icon = 'Error';
                break;
            case self::ICON_WARNING :
                $icon = 'Warning';
                break;
            case self::ICON_STOP :
                $icon = 'Stop';
                break;
            case self::ICON_QUESTION :
                $icon = 'Question';
                break;
            case self::ICON_CONFIG :
                $icon = 'Config';
                break;
        }

        return $size . $icon;
    }

    public function set_icon($icon)
    {
        return $this->set_additional_property(self::PROPERTY_ICON, $icon);
    }
}
