<?php
namespace Chamilo\Core\Repository\Implementation\Wikipedia;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;

class ExternalObject extends \Chamilo\Core\Repository\External\ExternalObject
{
    const OBJECT_TYPE = 'wikipedia';
    const PROPERTY_URLS = 'urls';

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(array(self::PROPERTY_URLS));
    }

    public function get_urls()
    {
        return $this->get_default_property(self::PROPERTY_URLS);
    }

    public function set_urls($urls)
    {
        return $this->set_default_property(self::PROPERTY_URLS, $urls);
    }

    public static function get_object_type()
    {
        return self::OBJECT_TYPE;
    }

    public function get_content_data($external_object)
    {
        $external_repository = \Chamilo\Core\Repository\Instance\Storage\DataManager::retrieve_by_id(
            Instance::class_name(), 
            $this->get_external_repository_id());
        return DataConnector::getInstance($external_repository)->download_external_repository_object($external_object);
    }

    public function get_render_url()
    {
        return $this->get_urls() . '&action=render';
    }
}
