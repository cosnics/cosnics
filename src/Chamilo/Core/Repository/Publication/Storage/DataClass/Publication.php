<?php
namespace Chamilo\Core\Repository\Publication\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Abstract class for publications to inherit
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Publication extends DataClass
{
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';

    /**
     * The content object
     *
     * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    protected $contentObject;

    /**
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function getContentObject()
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
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     *
     * @deprecated
     *
     */
    public function get_content_object()
    {
        return $this->getContentObject();
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @deprecated
     *
     */
    public function set_content_object($contentObject)
    {
        $this->setContentObject($contentObject);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     */
    public function setContentObject($contentObject)
    {
        $this->contentObject = $contentObject;
    }

    /**
     * Gets the content object id.
     *
     * @return int
     */
    public function get_content_object_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * Get the default properties of all Publications.
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_CONTENT_OBJECT_ID;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * Sets the content object id.
     *
     * @param int $content_object_id
     */
    public function set_content_object_id($content_object_id)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }
}