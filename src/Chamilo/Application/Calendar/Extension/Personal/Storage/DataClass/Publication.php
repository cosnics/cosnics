<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * @package Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Dieter De Neef
 */
class Publication extends \Chamilo\Core\Repository\Publication\Storage\DataClass\Publication
{

    public const PROPERTY_PUBLISHED = 'published';
    public const PROPERTY_PUBLISHER = 'publisher_id';

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames([self::PROPERTY_PUBLISHER, self::PROPERTY_PUBLISHED]);
    }

    public static function getStorageUnitName(): string
    {
        return 'calendar_personal_publication';
    }

    /**
     * @throws \ReflectionException
     */
    public function get_publication_object(): ?ContentObject
    {
        return parent::getContentObject();
    }

    public function get_published(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLISHED);
    }

    public function get_publisher(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLISHER);
    }

    public function set_published(int $published)
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLISHED, $published);
    }

    public function set_publisher(int $publisher)
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLISHER, $publisher);
    }
}
