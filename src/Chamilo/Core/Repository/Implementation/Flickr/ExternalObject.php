<?php
namespace Chamilo\Core\Repository\Implementation\Flickr;

use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class ExternalObject extends \Chamilo\Core\Repository\External\ExternalObject
{
    const OBJECT_TYPE = 'flickr';

    const PROPERTY_LICENSE = 'license';

    const PROPERTY_TAGS = 'tags';

    const PROPERTY_URLS = 'urls';

    const SIZE_LARGE = 'large';

    const SIZE_MEDIUM = 'medium';

    const SIZE_ORIGINAL = 'original';

    const SIZE_SMALL = 'small';

    const SIZE_SQUARE = 'square';

    const SIZE_THUMBNAIL = 'thumbnail';

    public function get_available_size_dimensions($size = self::SIZE_MEDIUM)
    {
        if (!in_array($size, self::get_default_sizes()))
        {
            $size = self::SIZE_MEDIUM;
        }

        if (!in_array($size, $this->get_available_sizes()))
        {
            $sizes = $this->get_available_sizes();
            $size = $sizes[0];
        }

        $urls = $this->get_urls();

        return array('width' => $urls[$size]['width'], 'height' => $urls[$size]['height']);
    }

    public function get_available_size_dimensions_string($size = self::SIZE_MEDIUM)
    {
        $available_size_dimensions = $this->get_available_size_dimensions($size);

        return $available_size_dimensions['width'] . ' x ' . $available_size_dimensions['height'];
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
                    (string) StringUtilities::getInstance()->createString($available_size)->upperCamelize()
                ) . ' (' . $this->get_available_size_dimensions_string($available_size) . ')</a>';
        }

        return implode('<br />' . PHP_EOL, $html);
    }

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(self::PROPERTY_URLS, self::PROPERTY_LICENSE, self::PROPERTY_TAGS)
        );
    }

    public static function get_default_sizes()
    {
        return array(
            self::SIZE_SQUARE, self::SIZE_THUMBNAIL, self::SIZE_SMALL, self::SIZE_MEDIUM, self::SIZE_LARGE,
            self::SIZE_ORIGINAL
        );
    }

    public function get_license()
    {
        return $this->get_default_property(self::PROPERTY_LICENSE);
    }

    public function get_license_icon()
    {
        $html = array();

        switch ($this->get_license_id())
        {
            case 0:
                $glyph = new FontAwesomeGlyph('copyright', array(), $this->get_license_name(), 'far');
                $html[] = $glyph->render();
                break;
            case 1:
                $glyph = new FontAwesomeGlyph('creative-commons', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                $glyph = new FontAwesomeGlyph('creative-commons-by', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                $glyph = new FontAwesomeGlyph('creative-commons-nc', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                $glyph = new FontAwesomeGlyph('creative-commons-sa', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                break;
            case 2:
                $glyph = new FontAwesomeGlyph('creative-commons', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                $glyph = new FontAwesomeGlyph('creative-commons-by', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                $glyph = new FontAwesomeGlyph('creative-commons-nc', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                break;
            case 3:
                $glyph = new FontAwesomeGlyph('creative-commons', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                $glyph = new FontAwesomeGlyph('creative-commons-by', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                $glyph = new FontAwesomeGlyph('creative-commons-nc', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                $glyph = new FontAwesomeGlyph('creative-commons-nd', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                break;
            case 4:
                $glyph = new FontAwesomeGlyph('creative-commons', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                $glyph = new FontAwesomeGlyph('creative-commons-by', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                break;
            case 5:
                $glyph = new FontAwesomeGlyph('creative-commons', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                $glyph = new FontAwesomeGlyph('creative-commons-by', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                $glyph = new FontAwesomeGlyph('creative-commons-sa', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                break;
            case 6:
                $glyph = new FontAwesomeGlyph('creative-commons', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                $glyph = new FontAwesomeGlyph('creative-commons-by', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                $glyph = new FontAwesomeGlyph('creative-commons-nd', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                break;
            case 7:
                $glyph = new FontAwesomeGlyph('creative-commons', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                break;
            case 8:
                $glyph = new FontAwesomeGlyph('landmark', array(), $this->get_license_name(), 'fas');
                $html[] = $glyph->render();
                break;
            case 9:
                $glyph = new FontAwesomeGlyph('creative-commons', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                $glyph = new FontAwesomeGlyph('creative-commons-zero', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                break;
            case 10:
                $glyph = new FontAwesomeGlyph('creative-commons-pd', array(), $this->get_license_name(), 'fab');
                $html[] = $glyph->render();
                break;
            default:
                return '';
        }

        return implode(PHP_EOL, $html);
    }

    public function get_license_id()
    {
        $license = $this->get_license();

        return $license['id'];
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

    public function get_license_url()
    {
        $license = $this->get_license();

        return $license['url'];
    }

    public static function get_object_type()
    {
        return self::OBJECT_TYPE;
    }

    public static function get_possible_licenses()
    {
        $licenses = array();
        $licenses[0] = array('id' => 0, 'name' => 'All Rights Reserved', 'url' => '');
        $licenses[1] = array(
            'id' => 1, 'name' => 'Attribution-NonCommercial-ShareAlike License',
            'url' => 'http://creativecommons.org/licenses/by-nc-sa/2.0/'
        );
        $licenses[2] = array(
            'id' => 2, 'name' => 'Attribution-NonCommercial License',
            'url' => 'http://creativecommons.org/licenses/by-nc/2.0/'
        );
        $licenses[3] = array(
            'id' => 3, 'name' => 'Attribution-NonCommercial-NoDerivs License',
            'url' => 'http://creativecommons.org/licenses/by-nc-nd/2.0/'
        );
        $licenses[4] = array(
            'id' => 4, 'name' => 'Attribution License', 'url' => 'http://creativecommons.org/licenses/by/2.0/'
        );
        $licenses[5] = array(
            'id' => 5, 'name' => 'Attribution-ShareAlike License',
            'url' => 'http://creativecommons.org/licenses/by-sa/2.0/'
        );
        $licenses[6] = array(
            'id' => 6, 'name' => 'Attribution-NoDerivs License',
            'url' => 'http://creativecommons.org/licenses/by-nd/2.0/'
        );
        $licenses[7] = array(
            'id' => 7, 'name' => 'No known copyright restrictions', 'url' => 'http://www.flickr.com/commons/usage/'
        );
        $licenses[8] = array(
            'id' => 8, 'name' => 'United States Government Work', 'url' => 'http://www.usa.gov/copyright.shtml'
        );

        return $licenses;
    }

    public static function get_possible_sizes()
    {
        return array(
            'sq' => self::SIZE_SQUARE, 't' => self::SIZE_THUMBNAIL, 's' => self::SIZE_SMALL, 'm' => self::SIZE_MEDIUM,
            'l' => self::SIZE_LARGE, 'o' => self::SIZE_ORIGINAL
        );
    }

    public function get_tags()
    {
        return $this->get_default_property(self::PROPERTY_TAGS);
    }

    public function get_tags_string($include_links = true)
    {
        $tags = array();
        foreach ($this->get_tags() as $tag)
        {
            if ($include_links)
            {
                $tags[] =
                    '<a href="http://www.flickr.com/photos/tags/' . $tag['text'] . '">' . $tag['display'] . '</a>';
            }
            else
            {
                $tags[] = $tag['display'];
            }
        }

        return implode(', ', $tags);
    }

    public function get_url($size = self::SIZE_MEDIUM)
    {
        if (!in_array($size, self::get_default_sizes()))
        {
            $size = self::SIZE_MEDIUM;
        }

        if (!in_array($size, $this->get_available_sizes()))
        {
            $sizes = $this->get_available_sizes();
            $size = $sizes[0];
        }

        $urls = $this->get_urls();

        return $urls[$size]['source'];
    }

    public function get_urls()
    {
        return $this->get_default_property(self::PROPERTY_URLS);
    }

    public function set_license($license)
    {
        return $this->set_default_property(self::PROPERTY_LICENSE, $license);
    }

    public function set_tags($tags)
    {
        return $this->set_default_property(self::PROPERTY_TAGS, $tags);
    }

    public function set_urls($urls)
    {
        return $this->set_default_property(self::PROPERTY_URLS, $urls);
    }
}
