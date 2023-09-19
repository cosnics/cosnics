<?php
namespace Chamilo\Core\Repository\ContentObject\Bookmark\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\IncludeableInterface;
use Chamilo\Libraries\Architecture\Interfaces\VersionableInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Bookmark\Storage\DataClass
 */
class Bookmark extends ContentObject implements VersionableInterface, IncludeableInterface
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\Bookmark';

    public const PROPERTY_APPLICATION = 'application';
    public const PROPERTY_URL = 'url';

    /**
     * If the content object "accept" the properties passed as argument it returns a new instance of itself based on
     * properties passed as argument.
     * Otherwise returns false.
     *
     * @param array $properties
     *
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
            $result = [1000000 => $result];

            return $result;
        }
        else
        {
            return false;
        }
    }

    public static function getAdditionalPropertyNames(): array
    {
        return [self::PROPERTY_URL, self::PROPERTY_APPLICATION];
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_bookmark';
    }

    public function get_application()
    {
        return $this->getAdditionalProperty(self::PROPERTY_APPLICATION);
    }

    public function get_url()
    {
        return $this->getAdditionalProperty(self::PROPERTY_URL);
    }

    public function set_application($application)
    {
        return $this->setAdditionalProperty(self::PROPERTY_APPLICATION, $application);
    }

    public function set_url($url)
    {
        return $this->setAdditionalProperty(self::PROPERTY_URL, $url);
    }
}
