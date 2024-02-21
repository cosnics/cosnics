<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\IncludeableInterface;
use Chamilo\Libraries\Architecture\Interfaces\VersionableInterface;
use Chamilo\Libraries\Storage\DataClass\Interfaces\CompositeDataClassExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\RssFeed\Storage\DataClass
 */
class RssFeed extends ContentObject
    implements VersionableInterface, IncludeableInterface, CompositeDataClassExtensionInterface
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\RssFeed';

    public const PROPERTY_NUMBER_OF_ENTRIES = 'number_of_entries';
    public const PROPERTY_URL = 'url';

    public static function getAdditionalPropertyNames(): array
    {
        return parent::getAdditionalPropertyNames([self::PROPERTY_URL, self::PROPERTY_NUMBER_OF_ENTRIES]);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_rss_feed';
    }

    public function get_number_of_entries()
    {
        return $this->getAdditionalProperty(self::PROPERTY_NUMBER_OF_ENTRIES);
    }

    public static function get_searchable_property_names()
    {
        return [self::PROPERTY_URL];
    }

    public function get_url()
    {
        return $this->getAdditionalProperty(self::PROPERTY_URL);
    }

    public function set_number_of_entries($numberOfEntries)
    {
        $this->setAdditionalProperty(self::PROPERTY_NUMBER_OF_ENTRIES, $numberOfEntries);
    }

    public function set_url($url)
    {
        return $this->setAdditionalProperty(self::PROPERTY_URL, $url);
    }
}
