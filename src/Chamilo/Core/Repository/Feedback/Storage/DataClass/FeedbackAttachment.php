<?php
namespace Chamilo\Core\Repository\Feedback\Storage\DataClass;

use Chamilo\Core\Repository\Feedback\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Attachment for a feedback
 *
 * @package Chamilo\Core\Repository\Feedback\Storage\DataClass
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class FeedbackAttachment extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_ATTACHMENT_ID = 'attachment_id';
    public const PROPERTY_FEEDBACK_ID = 'feedback_id';

    /**
     * @return int
     */
    public function getAttachmentId()
    {
        return $this->getDefaultProperty(self::PROPERTY_ATTACHMENT_ID);
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
                self::PROPERTY_FEEDBACK_ID,
                self::PROPERTY_ATTACHMENT_ID
            ]
        );
    }

    /**
     * @return int
     */
    public function getFeedbackId()
    {
        return $this->getDefaultProperty(self::PROPERTY_FEEDBACK_ID);
    }

    /**
     * @param int $attachmentId
     */
    public function setAttachmentId($attachmentId)
    {
        $this->setDefaultProperty(self::PROPERTY_ATTACHMENT_ID, $attachmentId);
    }

    /**
     * @param int $feedbackId
     */
    public function setFeedbackId($feedbackId)
    {
        $this->setDefaultProperty(self::PROPERTY_FEEDBACK_ID, $feedbackId);
    }
}