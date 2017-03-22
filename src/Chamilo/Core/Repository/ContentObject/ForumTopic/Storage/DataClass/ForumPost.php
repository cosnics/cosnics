<?php
namespace Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Forum\EmailNotification\PostEmailNotificator;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Describes a Forum post.
 *
 * @package repository\forum_topic\dataclass;
 * @author Mattias De Pauw - Hogeschool Gent
 * @author Maarten Volckaert - Hogeschool Gent
 */
class ForumPost extends DataClass implements AttachmentSupport
{
    /**
     * **************************************************************************************************************
     * Table Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_REPLY_ON_POST_ID = 'reply_on_post_id';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_CONTENT = 'content';
    const PROPERTY_FORUM_TOPIC_ID = 'forum_topic_id';
    const PROPERTY_CREATION_DATE = 'created';
    const PROPERTY_MODIFICATION_DATE = 'modified';
    const ATTACHMENT_ALL = 'all';
    const ATTACHMENT_NORMAL = 'normal';
    const PROPERTIES_ADDITIONAL = 'additional_properties';

    /**
     * Learning objects attached to this learning object.
     */
    private $attachments = array();

    /**
     * **************************************************************************************************************
     * Getters *
     * **************************************************************************************************************
     */
    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);;
    }

    /**
     * Inherited
     */
    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * Returns the default properties of this dataclass
     *
     * @return String[] - The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_TITLE,
                self::PROPERTY_FORUM_TOPIC_ID,
                self::PROPERTY_CONTENT,
                self::PROPERTY_USER_ID,
                self::PROPERTY_REPLY_ON_POST_ID,
                self::PROPERTY_CREATION_DATE,
                self::PROPERTY_MODIFICATION_DATE
            )
        );
    }

    /**
     * Returns a integer representation if its a reply on another post.
     *
     * @return int The id of the post its a reply on.
     */
    public function get_reply_on_post_id()
    {
        return $this->get_default_property(self::PROPERTY_REPLY_ON_POST_ID);
    }

    /**
     * Returns a user object of the creator of this post
     *
     * @return User
     */
    public function get_user()
    {
        if (!isset($this->user))
        {
            $this->user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                User::class_name(),
                (int) $this->get_user_id()
            );
        }

        return $this->user;
    }

    /**
     * Returns the ID of this object's owner.
     *
     * @return int The ID.
     */
    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    /**
     * Returns the title of this object
     *
     * @return string The title of the post
     */
    public function get_title()
    {
        return $this->get_default_property(self::PROPERTY_TITLE);
    }

    /**
     * Returns the content of this object.
     *
     * @return string The content of the post.
     */
    public function get_content()
    {
        return $this->get_default_property(self::PROPERTY_CONTENT);
    }

    /**
     * Returns the numeric identifier of the object's parent.
     *
     * @return int The identifier.
     */
    public function get_forum_topic_id()
    {
        return $this->get_default_property(self::PROPERTY_FORUM_TOPIC_ID);
    }

    /**
     * Returns the date when this object was created, as returned by PHP's time() function.
     *
     * @return int The creation date.
     */
    public function get_creation_date()
    {
        return $this->get_default_property(self::PROPERTY_CREATION_DATE);
    }

    /**
     * Returns the date when this object was last modified, as returned by PHP's time() function.
     *
     * @return int The modification time.
     */
    public function get_modification_date()
    {
        return $this->get_default_property(self::PROPERTY_MODIFICATION_DATE);
    }

    /**
     * **************************************************************************************************************
     * Setters *
     * **************************************************************************************************************
     */

    /**
     * Sets the ID of this object's owner.
     *
     * @param int $user The user id.
     */
    public function set_user_id($user)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user);
    }

    /**
     * Sets the ID of the reply on a post .
     *
     * @param int $forum_post_id
     *
     */
    public function set_reply_on_post_id($forum_post_id)
    {
        $this->set_default_property(self::PROPERTY_REPLY_ON_POST_ID, $forum_post_id);
    }

    /**
     * Sets the title of this post.
     *
     * @param string $title The title of this post object.
     */
    public function set_title($title)
    {
        $this->set_default_property(self::PROPERTY_TITLE, $title);
    }

    /**
     * Sets the content of this object.
     *
     * @param string $content The content of the post.
     */
    public function set_content($content)
    {
        $this->set_default_property(self::PROPERTY_CONTENT, $content);
    }

    /**
     * Sets the ID of this object's parent object.
     *
     * @param int $forum_topic_id The ID of the forum topic in which this post can be found.
     */
    public function set_forum_topic_id($forum_topic_id)
    {
        $this->set_default_property(self::PROPERTY_FORUM_TOPIC_ID, $forum_topic_id);
    }

    /**
     * Sets the date when this object was created.
     *
     * @param int $created The creation date of this post object.
     */
    public function set_creation_date($created)
    {
        $this->set_default_property(self::PROPERTY_CREATION_DATE, $created);
    }

    /**
     * Sets the date when this object was modified.
     *
     * @param int $modified The modification date of this post object.
     */
    public function set_modification_date($modified)
    {
        $this->set_default_property(self::PROPERTY_MODIFICATION_DATE, $modified);
    }

    /**
     * **************************************************************************************************************
     * CRUD *
     * **************************************************************************************************************
     */

    /**
     * When making a new post set the creation date and modification date to current time, expect when it's the first
     * post.
     *
     * @param boolean $first_post Boolean that indicates whether the post we want to create is the first post.
     *
     * @return boolean Returns whether the create was succesfull.
     */
    public function create($first_post = false)
    {
        $now = time();

        if (!$first_post)
        {
            $this->set_creation_date($now);
            $this->set_modification_date($now);

            $forum_topic = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(),
                $this->get_forum_topic_id()
            );
            $email_notificator = new PostEmailNotificator();
            $email_notificator->set_post($this);

            $text =
                Translation::get("PostAddedEmailTitle", null, 'Chamilo\Core\Repository\ContentObject\Forum\Display');
            $email_notificator->set_action_title($text);

            $text = Translation::get("PostAddedEmailBody", null, 'Chamilo\Core\Repository\ContentObject\Forum\Display');
            $email_notificator->set_action_body($text);

            $email_notificator->set_action_user(
                \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                    \Chamilo\Core\User\Storage\DataClass\User::class_name(),
                    (int) Session::get_user_id()
                )
            );
            $succes = parent::create($this);
            $forum_topic->add_post(1, $this->get_id(), $email_notificator);
            $email_notificator->send_emails();

            return $succes;
        }

        return parent::create($this);
    }

    /**
     * Update a post object and its content
     *
     * @return boolean returns true when post is updated succesfull.
     */
    public function update($request_from_topic)
    {
        if (!$request_from_topic)
        {

            $now = time();
            $this->set_modification_date($now);

            $forum_topic = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(),
                $this->get_forum_topic_id()
            );
            $first_post = $forum_topic->is_first_post($this);
            if ($first_post)
            {
                $forum_topic->set_title($this->get_title());
                $forum_topic->set_description($this->get_content());
                $forum_topic->set_modification_date($now);
                $forum_topic->update(true);
            }

            $email_notificator = new PostEmailNotificator();
            $email_notificator->set_post($this);
            $email_notificator->set_action_user(
                \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                    \Chamilo\Core\User\Storage\DataClass\User::class_name(),
                    (int) Session::get_user_id()
                )
            );

            // $emailnotificator->set_action_user($this->get_user());

            if ($first_post)
            {
                $email_notificator->set_first_post_text(
                    Translation::get("PostFirstPostComment", null, 'Chamilo\Core\Repository\ContentObject\Forum')
                );
            }

            $text = Translation::get(
                "PostEditedEmailTitle",
                null,
                'Chamilo\Core\Repository\ContentObject\Forum'
            );
            $email_notificator->set_action_title($text);

            $text = Translation::get(
                "PostEditedEmailBody",
                null,
                'Chamilo\Core\Repository\ContentObject\Forum'
            );
            $email_notificator->set_action_body($text);

            $forum_topic->notify_subscribed_users_edited_post_topic($email_notificator);
            $email_notificator->send_emails();
        }

        return parent::update($this);
    }

    /**
     * Delete 1 individual post and his attachements.
     *
     * @param boolean $all_posts Boolean that indicated whether we want to delete all the posts or just one single post.
     *
     * @return boolean
     */
    public function delete($all_posts = false)
    {
        $delete_attachments = DataManager::retrieve_attached_objects($this->get_id())->as_array();
        $forum_topic = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(),
            $this->get_forum_topic_id()
        );
        $first_post = $forum_topic->is_first_post($this);

        if ($all_posts)
        {
            $first_post = false;
        }
        if ($first_post)
        {
            $success = false;
        }
        else
        {
            $counter = count($delete_attachments);
            $succes_attachment = 0;

            foreach ($delete_attachments as $attachment)
            {
                if ($attachment->delete())
                {
                    $succes_attachment ++;
                }
            }

            if ($counter == $succes_attachment)
            {
                $success = parent::delete();
            }
            else
            {
                $success = false;
            }

            if ($success && !$all_posts)
            {
                $forum_topic->remove_post(1);
            }
        }

        return $success;
    }

    /**
     * **************************************************************************************************************
     * Attachments *
     * **************************************************************************************************************
     */

    /**
     * Returns the learning objects attached to this learning object.
     *
     * @param type $type
     *
     * @return array The learning objects.
     */
    public function get_attached_content_objects($type = self :: ATTACHMENT_NORMAL)
    {
        $this->attachments[$type] = DataManager::retrieve_attached_object_from_forum_post($this->get_id())->as_array();

        return $this->attachments[$type];
    }

    /**
     * Attaches the learning object with the given ID to this learning object.
     *
     * @param int $id The ID of the learning object to attach.
     */
    public function attach_content_object($id, $type = self :: ATTACHMENT_NORMAL)
    {
        $forum_post_attachment = new ForumPostAttachment();
        $forum_post_attachment->set_forum_post_id($this->get_id());
        $forum_post_attachment->set_attachment_id($id);
        $succes = $forum_post_attachment->create();

        return $succes;
    }

    /**
     * This method is used to attach serveral content objects.
     *
     * @param type $ids array of ID's
     * @param type $type
     *
     * @return boolean
     */
    public function attach_content_objects($ids = array(), $type = self :: ATTACHMENT_NORMAL)
    {
        if (!is_array($ids))
        {
            $ids = array($ids);
        }

        foreach ($ids as $id)
        {
            if (!$this->attach_content_object($id, $type))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Removes the learning object with the given ID from this learning object's attachment list.
     *
     * @param int $id The ID of the learning object to remove from the attachment list.
     *
     * @return boolean True if the attachment was removed, false if it did not exist.
     */
    public function detach_content_object($id)
    {
        return DataManager::detach_content_object($this, $id);
    }
}
