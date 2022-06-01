<?php
namespace Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Dataclass that describes a Forum Post Attachments.
 * @author Maarten Volckaert - Hogeschool Gent - copied class content of ContentObjectAttachment.
 */
class ForumPostAttachment extends DataClass
{
    const PROPERTY_ATTACHMENT_ID = 'attachment_id';
    const PROPERTY_FORUM_POST_ID = 'forum_post_id';

    /**
     * Refers to the attached content object.
     *
     * @var ContentObject
     */
    private $content_object;

    /**
     * **************************************************************************************************************
     * Getters *
     * **************************************************************************************************************
     */

    /**
     * Converts a class name to the corresponding learning object type name.
     *
     * @param string $class The class name.
     *
     * @return string The type name.
     */
    public static function class_to_type($class)
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace($class, true);
    }

    /**
     * Gets the id of the ContentObject that is attached to a forum post.
     *
     * @return int The id of the ContentObject that is attached to a forum post.
     */
    public function get_attachment_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_ATTACHMENT_ID);
    }

    /*
     * (non-PHPdoc) @see common/database/DataClass#get_data_manager()
     */

    /**
     * Gets the content object of the attachment base on the attachment id.
     *
     * @return ContentObject
     */
    public function get_content_object()
    {
        if ($this->content_object == null)
        {
            $this->content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class, $this->get_attachment_id()
            );
        }

        return $this->content_object;
    }

    /**
     * Get the default properties of all content object attachments.
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(array(self::PROPERTY_FORUM_POST_ID, self::PROPERTY_ATTACHMENT_ID));
    }

    /**
     * Gets the id of the forum post where a ContentObject is attached to.
     *
     * @return int The id of the forum post where a ContentObject is attached to.
     */
    public function get_forum_post_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_FORUM_POST_ID);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_forum_post_attachment';
    }

    /**
     * **************************************************************************************************************
     * Setters *
     * **************************************************************************************************************
     */

    /**
     * Gets the title of the attached ContentObject.
     *
     * @return string The title of the attached ContentObject.
     */
    public function get_title()
    {
        return $this->get_content_object()->get_title();
    }

    /**
     * Gets the type of this object.
     *
     * @return ForumPostAttachment
     */
    public function get_type()
    {
        return self::class_to_type(
            get_class(
                \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                    ContentObject::class, $this->get_attachment_id()
                )
            )
        );
    }

    /**
     * Sets the id of the attached ContentObject.
     *
     * @param int $attachment_id The id of the attached ContentObject.
     */
    public function set_attachment_id($attachment_id)
    {
        $this->setDefaultProperty(self::PROPERTY_ATTACHMENT_ID, $attachment_id);
    }

    /**
     * Sets the id of the forum post.
     *
     * @param int $forum_post_id The id of the forum post.
     */
    public function set_forum_post_id($forum_post_id)
    {
        $this->setDefaultProperty(self::PROPERTY_FORUM_POST_ID, $forum_post_id);
    }
}
