<?php
namespace Chamilo\Core\Repository\Publication\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Abstract class for publications to inherit
 *
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Publication extends DataClass
{
    public const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';

    protected ?ContentObject $contentObject;

    /**
     * @throws \ReflectionException
     */
    public function getContentObject(): ContentObject
    {
        if (is_null($this->contentObject))
        {
            $this->contentObject = DataManager::retrieve_by_id(
                ContentObject::class, $this->get_content_object_id()
            );
        }

        return $this->contentObject;
    }

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_CONTENT_OBJECT_ID;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * @throws \ReflectionException
     * @deprecated Use Publciation::getContentObject()
     */
    public function get_content_object(): ?ContentObject
    {
        return $this->getContentObject();
    }

    public function get_content_object_id(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    public function setContentObject(ContentObject $contentObject)
    {
        $this->contentObject = $contentObject;
    }

    /**
     * @deprecated Use Publication::setContentObject()
     */
    public function set_content_object(ContentObject $contentObject)
    {
        $this->setContentObject($contentObject);
    }

    public function set_content_object_id(int $content_object_id)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }
}