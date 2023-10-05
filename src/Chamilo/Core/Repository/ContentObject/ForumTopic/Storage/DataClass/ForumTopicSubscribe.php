<?php
namespace Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Dataclass that describes a Forum Topic Subsribe.
 *
 * @author Maarten Volckaert - Hogeschool Gent
 */
class ForumTopicSubscribe extends DataClass
{
    public const CONTEXT = ForumTopic::CONTEXT;

    public const PROPERTY_FORUM_TOPIC_ID = 'forum_topic_id';
    /*
     * Represents the id of the forum topic which is subscribed.
     */

    /**
     * Represents the id of the user who is subscribing.
     */
    public const PROPERTY_USER_ID = 'user_id';

    /**
     * Get the default properties of all content object attachments.
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames([self::PROPERTY_FORUM_TOPIC_ID, self::PROPERTY_USER_ID]);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_forum_topic_subscribe';
    }

    /**
     * Gets the id of the forum topic.
     *
     * @return int The id of the forum topic
     */
    public function get_forum_topic_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_FORUM_TOPIC_ID);
    }

    /**
     * Returns a user object of this subscribe
     *
     * @return User
     */
    public function get_user()
    {
        if (!isset($this->user))
        {
            $this->user = DataManager::retrieve_by_id(
                User::class, (int) $this->get_user_id()
            );
        }

        return $this->user;
    }

    /**
     * **************************************************************************************************************
     * Setters *
     * **************************************************************************************************************
     */

    /**
     * Gets the id of the user that is subscribing.
     *
     * @return int Returns the id of the user that is subscribing.
     */
    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    /**
     * Sets the ID of this object's forum topic.
     *
     * @param int $forum_topic_id The forum topic id.
     */
    public function set_forum_topic_id($forum_topic_id)
    {
        $this->setDefaultProperty(self::PROPERTY_FORUM_TOPIC_ID, $forum_topic_id);
    }

    /**
     * Sets the ID of this object's user.
     *
     * @param int $user_id The user id.
     */
    public function set_user_id($user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }
}
