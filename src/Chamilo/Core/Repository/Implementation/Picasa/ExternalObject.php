<?php
namespace Chamilo\Core\Repository\Implementation\Picasa;

use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class ExternalObject extends \Chamilo\Core\Repository\External\ExternalObject
{
    const OBJECT_TYPE = 'picasa';
    const PROPERTY_URLS = 'urls';
    const PROPERTY_LICENSE = 'license';
    const PROPERTY_OWNER = 'owner';
    const PROPERTY_TAGS = 'tags';
    const PROPERTY_ALBUM_ID = 'album_id';
    const SIZE_THUMBNAIL = 'thumbnail';
    const SIZE_MEDIUM = 'medium';
    const SIZE_ORIGINAL = 'original';

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_URLS, 
                self::PROPERTY_LICENSE, 
                self::PROPERTY_OWNER, 
                self::PROPERTY_TAGS, 
                self::PROPERTY_ALBUM_ID));
    }

    public static function get_default_sizes()
    {
        return array(self::SIZE_THUMBNAIL, self::SIZE_MEDIUM, self::SIZE_ORIGINAL);
    }

    public function get_available_sizes()
    {
        return array_keys($this->get_urls());
    }

    public function get_available_sizes_string()
    {
        $available_sizes = $this->get_available_sizes();
        $html = array();
        
        foreach ($available_sizes as $available_size)
        {
            $html[] = '<a href="' . $this->get_url($available_size) . '">' . Translation::get(
                (string) StringUtilities::getInstance()->createString($available_size)->upperCamelize()) . ' (' .
                 $this->get_available_size_dimensions_string($available_size) . ')</a>';
        }
        
        return implode('<br />' . "\n", $html);
    }

    public function get_available_size_dimensions($size = self :: SIZE_MEDIUM)
    {
        if (! in_array($size, self::get_default_sizes()))
        {
            $size = self::SIZE_MEDIUM;
        }
        
        if (! in_array($size, $this->get_available_sizes()))
        {
            $sizes = $this->get_available_sizes();
            $size = $sizes[0];
        }
        
        $urls = $this->get_urls();
        return array('width' => $urls[$size]['width'], 'height' => $urls[$size]['height']);
    }

    public function get_available_size_dimensions_string($size = self :: SIZE_MEDIUM)
    {
        $available_size_dimensions = $this->get_available_size_dimensions($size);
        
        return $available_size_dimensions['width'] . ' x ' . $available_size_dimensions['height'];
    }

    public function get_urls()
    {
        return $this->get_default_property(self::PROPERTY_URLS);
    }

    public function set_urls($urls)
    {
        return $this->set_default_property(self::PROPERTY_URLS, $urls);
    }

    public function get_url($size = self :: SIZE_MEDIUM)
    {
        if (! in_array($size, self::get_default_sizes()))
        {
            $size = self::SIZE_MEDIUM;
        }
        
        if (! in_array($size, $this->get_available_sizes()))
        {
            $sizes = $this->get_available_sizes();
            $size = $sizes[0];
        }
        
        $urls = $this->get_urls();
        return $urls[$size]['source'];
    }

    public function get_license()
    {
        return $this->get_default_property(self::PROPERTY_LICENSE);
    }

    public function get_license_id()
    {
        $license = $this->get_license();
        return $license['id'];
    }

    public function get_license_url()
    {
        $license = $this->get_license();
        return $license['url'];
    }

    public function get_license_name()
    {
        $license = $this->get_license();
        return $license['name'];
    }

    public function get_license_string()
    {
        if ($this->get_license_url())
        {
            return '<a href="' . $this->get_license_url() . '">' . $this->get_license_name() . '</a>';
        }
        else
        {
            return $this->get_license_name();
        }
    }

    public function get_license_icon()
    {
        return Theme::getInstance()->getCommonImage(
            'Chamilo/Core/Repository/Implementation/Picasa/Licenses/License' . $this->get_license_id(), 
            'png', 
            $this->get_license_name(), 
            $this->get_license_url(), 
            ToolbarItem::DISPLAY_ICON);
    }

    public function set_license($license)
    {
        return $this->set_default_property(self::PROPERTY_LICENSE, $license);
    }

    public function get_owner()
    {
        return $this->get_default_property(self::PROPERTY_OWNER);
    }

    public function set_owner($owner)
    {
        return $this->set_default_property(self::PROPERTY_OWNER, $owner);
    }

    public function get_owner_string()
    {
        $string = ($this->get_owner() && $this->get_owner() != $this->get_owner_id() ? $this->get_owner() . ' (' : '');
        $string .= $this->get_owner_id();
        $string .= ($this->get_owner() && $this->get_owner() != $this->get_owner_id() ? ')' : '');
        return $string;
    }

    public function get_tags()
    {
        return $this->get_default_property(self::PROPERTY_TAGS);
    }

    public function set_tags($tags)
    {
        return $this->set_default_property(self::PROPERTY_TAGS, $tags);
    }

    public function get_tags_string()
    {
        return implode(', ', $this->get_tags());
    }

    public function get_album_id()
    {
        return $this->get_default_property(self::PROPERTY_ALBUM_ID);
    }

    public function set_album_id($album_id)
    {
        return $this->set_default_property(self::PROPERTY_ALBUM_ID, $album_id);
    }

    public static function get_object_type()
    {
        return self::OBJECT_TYPE;
    }
}
