<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class EntryAttachment extends DataClass
{
    const PROPERTY_ENTRY_ID = 'entry_id';
    const PROPERTY_ATTACHMENT_ID = 'attachment_id';
    const PROPERTY_CREATED = 'created';

    /**
     * Get the default properties of all feedback
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array(
                self::PROPERTY_ENTRY_ID,
                self::PROPERTY_ATTACHMENT_ID,
                self::PROPERTY_CREATED
            )
        );
    }

    /**
     * @return int
     */
    public function getEntryId()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTRY_ID);
    }

    /**
     * @param int $entryId
     */
    public function setEntryId($entryId)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTRY_ID, $entryId);
    }

    /**
     * @return int
     */
    public function getAttachmentId()
    {
        return $this->getDefaultProperty(self::PROPERTY_ATTACHMENT_ID);
    }

    /**
     * @param int $attachmentId
     */
    public function setAttachmentId($attachmentId)
    {
        $this->setDefaultProperty(self::PROPERTY_ATTACHMENT_ID, $attachmentId);
    }

    /**
     *
     * @return integer
     */
    public function getCreated()
    {
        return $this->getDefaultProperty(self::PROPERTY_CREATED);
    }

    /**
     *
     * @param integer $created
     */
    public function setCreated($created)
    {
        $this->setDefaultProperty(self::PROPERTY_CREATED, $created);
    }

}