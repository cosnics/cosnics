<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class EntryAttachment extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_ATTACHMENT_ID = 'attachment_id';
    public const PROPERTY_CREATED = 'created';
    public const PROPERTY_ENTRY_ID = 'entry_id';

    /**
     * @return int
     */
    public function getAttachmentId()
    {
        return $this->getDefaultProperty(self::PROPERTY_ATTACHMENT_ID);
    }

    /**
     * @return int
     */
    public function getCreated()
    {
        return $this->getDefaultProperty(self::PROPERTY_CREATED);
    }

    /**
     * Get the default properties of all feedback
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_ENTRY_ID,
                self::PROPERTY_ATTACHMENT_ID,
                self::PROPERTY_CREATED
            ]
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
     * @param int $attachmentId
     */
    public function setAttachmentId($attachmentId)
    {
        $this->setDefaultProperty(self::PROPERTY_ATTACHMENT_ID, $attachmentId);
    }

    /**
     * @param int $created
     */
    public function setCreated($created)
    {
        $this->setDefaultProperty(self::PROPERTY_CREATED, $created);
    }

    /**
     * @param int $entryId
     */
    public function setEntryId($entryId)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTRY_ID, $entryId);
    }

}