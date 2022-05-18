<?php
namespace Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Forum\EmailNotification\TopicEmailNotificator;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package repository.lib.content_object.forum_topic
 */

/**
 * This class represents a topic in a discussion forum.
 *
 * @author Mattias De Pauw - Hogeschool Gent
 * @author Maarten Volckaert - Hogeschool Gent
 */
class ForumTopic extends ContentObject implements Versionable, AttachmentSupport
{
    const PROPERTY_LAST_POST = 'last_post_id';
    const PROPERTY_LOCKED = 'locked';
    const PROPERTY_TOTAL_POSTS = 'total_posts';

    /**
     * Variable that contains the first post of a forum topic.
     *
     * @var ForumPost The first post of a forum topic.
     */
    private $first_post;

    /**
     * **************************************************************************************************************
     * Getters *
     * **************************************************************************************************************
     */

    /**
     * Add the last post to a topic and his parents
     *
     * @param int $last_post The id of the last post.
     */
    public function add_last_post($last_post)
    {
        $this->set_last_post($last_post);

        $this->update();

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_REF
            ), new StaticConditionVariable($this->get_id())
        );
        $wrappers = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class, $condition
        );

        foreach ($wrappers as $item)
        {
            $lo = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class, $item->get_parent()
            );
            $lo->add_last_post($last_post);
        }
    }

    /**
     * This function adds a number of posts to the property total posts of a topic.
     *
     * @param int $posts Number of posts that needs to be added to a topic.
     * @param int $last_post_id The id of the new last post.
     */
    public function add_post($posts, $last_post_id, $emailnotificator)
    {
        $this->set_total_posts($this->get_total_posts() + $posts);

        $this->set_last_post($last_post_id);
        $this->update();

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_REF
            ), new StaticConditionVariable($this->get_id())
        );
        $wrappers = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class, $condition
        );
        if ($emailnotificator)
        {
            $emailnotificator->add_users(DataManager::retrieve_subscribed_forum_topic_users($this->get_id()));
            $emailnotificator->set_topic($this);
        }
        foreach ($wrappers as $item)
        {
            $lo = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class, $item->get_parent()
            );
            $lo->add_post($posts, $emailnotificator, $item->get_id(), $last_post_id);
        }
    }

    public function attach_content_object($aid, $type = self::ATTACHMENT_NORMAL)
    {
        $success = parent::attach_content_object($aid, $type);

        if ($this->first_post)
        {
            $success &= $this->first_post->attach_content_object($aid, $type);
        }

        return $success;
    }


    public function create($create_in_batch = false)
    {
        $succes = parent::create($create_in_batch);

        if ($succes)
        {
            $forum_post = new ForumPost();

            $forum_post->set_title($this->get_title());
            $forum_post->set_content($this->get_description());
            $forum_post->set_user_id($this->get_owner_id());

            $forum_post->set_forum_topic_id($this->get_id());
            $forum_post->set_creation_date($this->get_creation_date());
            $forum_post->set_modification_date($this->get_modification_date());
            $forum_post->create(true);

            $this->first_post = $forum_post;
            $this->set_total_posts(1);
            $this->set_last_post(0);
            $this->update();
        }

        return $succes;
    }

    /**
     * Delete a Forum Topic and all its posts.
     *
     * @param bool $only_version
     *
     * @return boolean Returns whether the delete was succesfull.
     */
    public function delete($only_version = false)
    {
        if ($only_version)
        {
            $posts = DataManager::retrieve_forum_posts($this->get_id());
            $subscribes = DataManager::retrieve_subscribes($this->get_id());
            foreach ($posts as $post)
            {
                $post->delete(true);
            }
            foreach ($subscribes as $subscribe)
            {
                $subscribe->delete();
            }
        }

        return parent::delete($only_version);
    }

    public static function getAdditionalPropertyNames(): array
    {
        return array(self::PROPERTY_LOCKED, self::PROPERTY_TOTAL_POSTS, self::PROPERTY_LAST_POST);
    }

    /**
     * Gets the allowed types for a forum topic.
     *
     * @return Array Returns an array with allowed types.
     */
    public function get_allowed_types()
    {
        return array(ForumPost::class);
    }

    /**
     * **************************************************************************************************************
     * Setters *
     * **************************************************************************************************************
     */

    /**
     * Gets the id of the last post in this topic.
     *
     * @return int The id of the last post.
     */
    public function get_last_post()
    {
        return $this->getAdditionalProperty(self::PROPERTY_LAST_POST);
    }

    /**
     * Gets whether this object is locked.
     *
     * @return int
     */
    public function get_locked()
    {
        return $this->getAdditionalProperty(self::PROPERTY_LOCKED);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'repository_forum_topic';
    }

    /**
     * **************************************************************************************************************
     * CRUD *
     * **************************************************************************************************************
     *
     * @param bool $create_in_batch
     *
     * @return bool
     */

    /*
     * This function creates a forum topic object and if succesfull the first post in this topic aswell. @return boolean
     * $succes Returns whether the create was succesfull or not.
     */
    /**
     * Gets the total number of posts of this topic.
     *
     * @return int The number of posts of this topic.
     */
    public function get_total_posts()
    {
        return $this->getAdditionalProperty(self::PROPERTY_TOTAL_POSTS);
    }

    /**
     * Gets the type name by using an utility namespace function.
     *
     * @return string
     */
    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class, true);
    }

    /*
     * This function attaches a content object to a forum topic. @param int $aid The id of the content object that needs
     * to be attached to the forum topic. @param const $type @return boolean $success Returns true if content object is
     * attached succesful
     */

    public function invert_locked()
    {
        $this->set_locked(!$this->get_locked());

        return $this->update();
    }

    public function is_attached_to_or_included_in($attachment_id)
    {
        $regular_result = parent::is_attached_to_or_included_in($attachment_id);

        if (!$regular_result)
        {
            return DataManager::is_attached_to_forum_topic($this->get_id(), $attachment_id);
        }
        else
        {
            return true;
        }
    }

    /**
     * This function checks whether a given post object is the first post of its parent
     *
     * @param ForumPost $object
     *
     * @return boolean Returns true when a post is the first post of a topic.
     */
    public function is_first_post($object)
    {
        $first_post = DataManager::retrieve_first_post($this->get_id());
        if ($first_post->get_creation_date() == $object->get_creation_date())
        {
            return true;
        }

        return false;
    }

    public function is_locked()
    {
        if ($this->get_locked())
        {
            return true;
        }
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_REF
            ), new StaticConditionVariable($this->get_id())
        );
        $parents = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class, $condition
        );

        foreach ($parents as $parent)
        {
            $content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class, $parent->get_parent()
            );

            if ($content_object->is_locked())
            {
                return true;
            }
        }

        return false;
    }

    /**
     * The subscribed users to this topic and his parents notifieren
     *
     * @param type $emailnotificator
     */
    public function notify_subscribed_users_edited_post_topic($emailnotificator)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_REF
            ), new StaticConditionVariable($this->get_id())
        );
        $wrappers = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class, $condition
        );

        if ($emailnotificator)
        {
            $emailnotificator->add_users(DataManager::retrieve_subscribed_forum_topic_users($this->get_id()));
            $emailnotificator->set_topic($this);
        }

        foreach ($wrappers as $item)
        {
            $lo = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class, $item->get_parent()
            );
            $lo->notify_subscribed_users($emailnotificator);
        }
    }

    /*
     * This function subtracts a number of posts from the total post property of a topic and does this aswell for all
     * its parents. @param int $posts Number of posts that needs to be subtracted. @param int $forum_topic_id The id of
     * the topic of which we subtract a number of posts.
     */

    /**
     * This function is used to recalculate the last post of a specific forum topic.
     *
     * @param int $forum_topic_id The id of the forum topic of which this method will recalculate the last post.
     */
    public function recalculate_last_post()
    {
        $next_last_post = DataManager::retrieve_last_post($this->get_id());

        // after set id to O never update the forum post!!!!

        $first_post = $this->is_first_post($next_last_post);

        if ($this->get_last_post() != $next_last_post->get_id())
        {
            if (!$first_post)
            {

                $this->set_last_post($next_last_post->get_id());
            }
            else
            {
                $this->set_last_post(0);
            }
            $this->update();

            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_REF
                ), new StaticConditionVariable($this->get_id())
            );
            $wrappers = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
                ComplexContentObjectItem::class, $condition
            );

            foreach ($wrappers as $item)
            {
                $lo = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                    ContentObject::class, $item->get_parent()
                );
                $lo->recalculate_last_post();
            }
        }
    }

    public function remove_post($posts = 1)
    {
        $this->set_total_posts($this->get_total_posts() - $posts);
        $this->update();

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_REF
            ), new StaticConditionVariable($this->get_id())
        );
        $wrappers = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class, $condition
        );

        foreach ($wrappers as $item)
        {
            $lo = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class, $item->get_parent()
            );
            $lo->remove_post($posts);
        }
        $this->recalculate_last_post();
    }

    /**
     * Sets the last post of this topic by giving the id of the new last post.
     *
     * @param int $last_post The id of the last post.
     */
    public function set_last_post($last_post)
    {
        $this->setAdditionalProperty(self::PROPERTY_LAST_POST, $last_post);
    }

    /**
     * Sets whether this topic is locked.
     *
     * @param int $locked
     */
    public function set_locked($locked)
    {
        $this->setAdditionalProperty(self::PROPERTY_LOCKED, $locked);
    }

    /**
     * Sets the total number of posts of this topic.
     *
     * @param int $total_posts The total number of posts.
     */
    public function set_total_posts($total_posts)
    {
        $this->setAdditionalProperty(self::PROPERTY_TOTAL_POSTS, $total_posts);
    }

    /**
     * update if the firstpost is al ready updated then only do an update else make a new topicnotificator and send the
     * emails
     *
     * @param type $firstpostupdated
     *
     * @return bool
     */
    public function update($request_from_forum_postpost = false)
    {
        if (!$request_from_forum_postpost)
        {
            $first_post = DataManager::retrieve_first_post($this->get_id());
            if ($this->get_title() == $first_post->get_title() &&
                $this->get_description() == $first_post->get_content())
            {
                return parent::update();
            }
            else
            {
                $email_notificator = new TopicEmailNotificator();

                $text = Translation::get(
                    "TopicEditedEmailTitle", null, ContentObject::get_content_object_type_namespace('forum')
                );
                $email_notificator->set_action_title($text);
                $text = Translation::get(
                    "TopicEditedEmailBody", null, ContentObject::get_content_object_type_namespace('forum')
                );
                $email_notificator->set_action_body($text);
                $email_notificator->set_action_user(
                    \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                        User::class, (int) Session::get_user_id()
                    )
                );
                $email_notificator->set_is_topic_edited(true);

                $now = time();
                $this->set_modification_date($now);
                $first_post = DataManager::retrieve_first_post($this->get_id());
                $first_post->set_modification_date($now);

                $email_notificator->set_previous_title($first_post->get_title());

                $first_post->set_title($this->get_title());
                $first_post->set_content($this->get_description());

                parent::update();

                $email_notificator->set_topic($this);
                $this->notify_subscribed_users_edited_post_topic($email_notificator);
                $email_notificator->send_emails();

                return $first_post->update(true);
            }
        }
        else
        {
            return parent::update();
        }
    }
}
