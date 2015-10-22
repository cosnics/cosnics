<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Includeable;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 * $Id: rss_feed.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.content_object.rss_feed
 */
class RssFeed extends ContentObject implements Versionable, Includeable
{
    const PROPERTY_URL = 'url';

    public static function get_type_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: class_name(), true);
    }

    public function get_url()
    {
        return $this->get_additional_property(self :: PROPERTY_URL);
    }

    public function set_url($url)
    {
        return $this->set_additional_property(self :: PROPERTY_URL, $url);
    }

    public static function get_additional_property_names()
    {
        return array(self :: PROPERTY_URL);
    }

    public static function get_searchable_property_names()
    {
        return array(self :: PROPERTY_URL);
    }
}
