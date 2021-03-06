<?php
namespace Chamilo\Core\Repository\ContentObject\Bookmark\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\Includeable;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 *
 * @package repository.lib.content_object.bookmark
 */
class Bookmark extends ContentObject implements Versionable, Includeable
{
    const PROPERTY_URL = 'url';
    const PROPERTY_APPLICATION = 'application';

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    /**
     * If the content object "accept" the properties passed as argument it returns a new instance of itself based on
     * properties passed as argument.
     * Otherwise returns false.
     *
     * @param array $properties
     * @return ContentObject | array | false
     */
    public static function accept($properties)
    {
        $url = isset($properties[self::PROPERTY_URL]) ? $properties[self::PROPERTY_URL] : '';
        $application = isset($properties[self::PROPERTY_APPLICATION]) ? $properties[self::PROPERTY_APPLICATION] : '';
        if ($url && $application)
        {
            $result = new self();
            $result->set_url($url);
            $result->set_application($application);
            $result = array(1000000 => $result);
            return $result;
        }
        else
        {
            return false;
        }
    }

    public function get_url()
    {
        return $this->get_additional_property(self::PROPERTY_URL);
    }

    public function set_url($url)
    {
        return $this->set_additional_property(self::PROPERTY_URL, $url);
    }

    public function get_application()
    {
        return $this->get_additional_property(self::PROPERTY_APPLICATION);
    }

    public function set_application($application)
    {
        return $this->set_additional_property(self::PROPERTY_APPLICATION, $application);
    }

    public static function get_additional_property_names()
    {
        return array(self::PROPERTY_URL, self::PROPERTY_APPLICATION);
    }
}
