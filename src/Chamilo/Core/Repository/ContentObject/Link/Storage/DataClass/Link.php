<?php
namespace Chamilo\Core\Repository\ContentObject\Link\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\Includeable;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 * @package Chamilo\Core\Repository\ContentObject\Link\Storage\DataClass
 */
class Link extends ContentObject implements Versionable, Includeable
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\Link';

    public const PROPERTY_SHOW_IN_IFRAME = 'show_in_iframe';
    public const PROPERTY_URL = 'url';

    /**
     * Validates the url, URL beginning with / are internal URL's and considered complete, URLS that contain :// are
     * considered complete as well.
     * In any other case the URL is appended with 'http://' at the beginning.
     *
     * @param String $url
     *
     * @return String completed url
     */
    public static function complete_url($url)
    {
        if (substr($url, 0, 1) == '/' || strstr($url, '://'))
        {
            return $url;
        }
        else
        {
            return 'http://' . $url;
        }
    }

    public static function getAdditionalPropertyNames(): array
    {
        return [self::PROPERTY_URL, self::PROPERTY_SHOW_IN_IFRAME];
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_link';
    }

    public static function get_searchable_property_names()
    {
        return [self::PROPERTY_URL];
    }

    public function get_show_in_iframe()
    {
        return $this->getAdditionalProperty(self::PROPERTY_SHOW_IN_IFRAME);
    }

    public function get_url()
    {
        return $this->getAdditionalProperty(self::PROPERTY_URL);
    }

    public function set_show_in_iframe($status)
    {
        return $this->setAdditionalProperty(self::PROPERTY_SHOW_IN_IFRAME, $status);
    }

    public function set_url($url)
    {
        $url = self::complete_url($url);

        return $this->setAdditionalProperty(self::PROPERTY_URL, $url);
    }
}
