<?php
namespace Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Dataclass that describes a Forum Topic Subsribe.
 * 
 * @author Maarten Volckaert - Hogeschool Gent
 */
class ForumTopicSubscribe extends DataClass
{
    
    /**
     * Represents the id of the user who is subscribing.
     */
    const PROPERTY_USER_ID = 'user_id';
    /*
     * Represents the id of the forum topic which is subscribed.
     */
    const PROPERTY_FORUM_TOPIC_ID = 'forum_topic_id';

    /**
     * **************************************************************************************************************
     * Getters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the type name of this class.
     * 
     * @return ForumTopicSubscribe
     *
     */
    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class, true);
    }

    /**
     * Get the default properties of all content object attachments.
     * 
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = [])
    {
        return parent::get_default_property_names(array(self::PROPERTY_FORUM_TOPIC_ID, self::PROPERTY_USER_ID));
    }

    /**
     * Gets the data manager of the forum topic subscribe object.
     * 
     * @return \core\repository\content_object\forum_topic\DataManager Returns a data manager object for database
     *         communication
     */
    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * Gets the id of the forum topic.
     * 
     * @return int The id of the forum topic
     */
    public function get_forum_topic_id()
    {
        return $this->get_default_property(self::PROPERTY_FORUM_TOPIC_ID);
    }

    /**
     * Gets the id of the user that is subscribing.
     * 
     * @return int Returns the id of the user that is subscribing.
     */
    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    /**
     * Returns a user object of this subscribe
     * 
     * @return User
     */
    public function get_user()
    {
        if (! isset($this->user))
        {
            $this->user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                User::class,
                (int) $this->get_user_id());
        }
        return $this->user;
    }

    /**
     * **************************************************************************************************************
     * Setters *
     * **************************************************************************************************************
     */
    /**
     * Sets the ID of this object's forum topic.
     * 
     * @param int $forum_topic_id The forum topic id.
     */
    public function set_forum_topic_id($forum_topic_id)
    {
        $this->set_default_property(self::PROPERTY_FORUM_TOPIC_ID, $forum_topic_id);
    }

    /**
     * Sets the ID of this object's user.
     * 
     * @param int $user_id The user id.
     */
    public function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'repository_forum_topic_subscribe';
    }
}
