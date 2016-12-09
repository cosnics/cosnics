<?php
namespace Chamilo\Core\Repository\Implementation\Scribd;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;

class ExternalObject extends \Chamilo\Core\Repository\External\ExternalObject
{
    const OBJECT_TYPE = 'scribd';
    const PROPERTY_LICENSE = 'license';
    const PROPERTY_TAGS = 'tags';
    const PROPERTY_URL = 'url';
    const PROPERTY_DOWNLOAD_FORMATS = 'download_formats';

    public function __construct($default_properties)
    {
        parent::__construct($default_properties);
    }

    public function get_license()
    {
        return $this->get_default_property(self::PROPERTY_LICENSE);
    }

    public static function get_object_type()
    {
        return self::OBJECT_TYPE;
    }

    public function set_license($license)
    {
        return $this->set_default_property(self::PROPERTY_LICENSE, $license);
    }

    public function get_license_name()
    {
        switch ($this->get_license())
        {
            case 'pd' :
                'Public Domain';
                break;
            case 'by' :
                'Attribution';
                break;
            case 'by-nc' :
                return 'Attribution-NonCommercial';
                break;
            case 'by-nc-nd' :
                return 'Attribution-NonCommercial No-derivs';
                break;
            case 'by-nc-sa' :
                return 'Attribution-NonCommercial Share Alike';
                break;
            case 'by-sa' :
                'Attribution Share Alike';
                break;
            case 'by-nd' :
                'Attribution No Derivatives';
                break;
            case 'c' :
                'All Rights Reserved';
                break;
            default :
                return 'Unknown';
        }
    }

    public function get_license_icon()
    {
        $icon = new ToolbarItem(
            $this->get_license_name(), 
            Theme::getInstance()->getImagePath(
                'Chamilo\Core\Repository\Implementation\Scribd', 
                'Licenses/' . $this->get_license()), 
            null, 
            ToolbarItem::DISPLAY_ICON);
        return $icon->as_html();
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

    public function get_url()
    {
        return $this->get_default_property(self::PROPERTY_URL);
    }

    public function set_url($url)
    {
        return $this->set_default_property(self::PROPERTY_URL, $url);
    }

    public function get_download_formats()
    {
        return $this->get_default_property(self::PROPERTY_DOWNLOAD_FORMATS);
    }

    public function get_download_formats_string()
    {
        $toolbar = new Toolbar();
        foreach ($this->get_download_formats() as $format)
        {
            $icon = new ToolbarItem(
                $format, 
                Theme::getInstance()->getImagePath(
                    'Chamilo\Core\Repository\Implementation\Scribd', 
                    'Download/' . $format), 
                null, 
                ToolbarItem::DISPLAY_ICON);
            $toolbar->add_item($icon);
        }
        
        return $toolbar->as_html();
    }

    public function set_download_formats($download_formats)
    {
        return $this->set_default_property(self::PROPERTY_DOWNLOAD_FORMATS, $download_formats);
    }

    public function get_document($download_format)
    {
        $external_repository = \Chamilo\Core\Repository\Instance\Storage\DataManager::retrieve_by_id(
            Instance::class_name(), 
            $this->get_external_repository_id());
        return DataConnector::getInstance($external_repository)->download_external_repository_object(
            $this, 
            $download_format);
    }
}
