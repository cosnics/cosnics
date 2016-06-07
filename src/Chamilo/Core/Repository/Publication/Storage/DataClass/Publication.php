<?php

namespace Chamilo\Core\Repository\Publication\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
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
     * @var ContentObject
     */
    protected $contentObject;

    /**
     * @return ContentObject
     */
    public function getContentObject()
    {
        if (is_null($this->contentObject))
        {
            $this->contentObject = \Chamilo\Core\Repository\Storage\DataManager:: retrieve_by_id(
                ContentObject:: class_name(),
                $this->get_content_object_id()
            );
        }

        return $this->contentObject;
    }

    /**
     * @param ContentObject $contentObject
     */
    public function setContentObject($contentObject)
    {
        $this->contentObject = $contentObject;
    }

    /**
     * @return ContentObject
     *
     * @deprecated
     */
    public function get_content_object()
    {
        return $this->getContentObject();
    }

    /**
     * @param ContentObject $contentObject
     *
     * @deprecated
     */
    public function set_content_object($contentObject)
    {
        $this->setContentObject($contentObject);
    }

    /**
     * Get the default properties of all Publications.
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent:: get_default_property_names(
            array(self :: PROPERTY_CONTENT_OBJECT_ID)
        );
    }

    /**
     * Gets the content object id.
     *
     * @return int
     */
    public function get_content_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * Sets the content object id.
     *
     * @param int $content_object_id
     */
    public function set_content_object_id($content_object_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }
}