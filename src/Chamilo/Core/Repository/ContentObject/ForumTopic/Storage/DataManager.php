<?php
namespace Chamilo\Core\Repository\ContentObject\ForumTopic\Storage;

use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPostAttachment;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumTopicSubscribe;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass as DataClass2;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Head class of the datamanager, it receives data calls and delegates it to the mdb2 class.
 *
 * @author Mattias De Pauw - Hogeschool Gent
 * @author Maarten Volckaert - Hogeschool Gent
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'repository_';

    /**
     * **************************************************************************************************************
     * Posts *
     * **************************************************************************************************************
     */

    /**
     * Retrieve the last post of a particular topic.
     *
     * @param $forum_topic_id int The id of the topic of which this function retrieves the last post.
     * @return DataClass2
     */
    public static function retrieve_last_post($forum_topic_id)
    {
        return self::retrieve_forum_post_by_creation_date_order_from_forum_topic($forum_topic_id, SORT_DESC);
    }

    /**
     * Retrieve the first post of a topic.
     *
     * @param $forum_topic_id int The id of the topic of which this function retrieves the first post.
     * @return DataClass2
     */
    public static function retrieve_first_post($forum_topic_id)
    {
        return self::retrieve_forum_post_by_creation_date_order_from_forum_topic($forum_topic_id, SORT_ASC);
    }

    /**
     * Retrieves the first forum post from a given forum topic ordered by the creation date - given order
     *
     * @param int $forum_topic_id
     * @param int $creation_date_order
     *
     * @return DataClass
     */
    protected static function retrieve_forum_post_by_creation_date_order_from_forum_topic($forum_topic_id,
        $creation_date_order)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ForumPost::class, ForumPost::PROPERTY_FORUM_TOPIC_ID),
            new StaticConditionVariable($forum_topic_id));

        $orderBy = OrderBy::generate(ForumPost::class, ForumPost::PROPERTY_CREATION_DATE,
            $creation_date_order);

        $parameters = new DataClassRetrieveParameters($condition,  $orderBy);

        return self::retrieve(ForumPost::class, $parameters);
    }

    /**
     * Retrieves modification date of a particular post.
     *
     * @param int $forum_post_id
     *
     * @throws ObjectNotExistException
     *
     * @return int
     */
    public static function retrieve_forum_post_date($forum_post_id)
    {
        $post = self::retrieve_by_id(ForumPost::class, $forum_post_id);

        if (! $post)
        {
            throw new ObjectNotExistException(Translation::get('ForumPost'), $forum_post_id);
        }

        return $post->get_modification_date();
    }

    /**
     * Retrieve all the posts of a topic based on the topic id.
     *
     * @param $forum_topic_id int The id of the topic of which this function retrieves all posts.
     * @param Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator Returns a result set of posts.
     */
    public static function retrieve_forum_posts($forum_topic_id, $condition = null)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ForumPost::class, ForumPost::PROPERTY_FORUM_TOPIC_ID),
            new StaticConditionVariable($forum_topic_id));

        if ($condition)
        {
            $conditions[] = $condition;
        }

        $condition = new AndCondition($conditions);

        return self::retrieves(ForumPost::class, new DataClassRetrievesParameters($condition));
    }

    /**
     * Return 1 forum post by his id.
     *
     * @param int $forum_post_id
     *
     * @return ForumPost
     *
     */
    public static function retrieve_forum_post($forum_post_id)
    {
        return self::retrieve_by_id(ForumPost::class, $forum_post_id);
    }

    /**
     * Retrieve all the posts of a topic based on the topic id.
     *
     * @param $forum_topic_id int The id of the topic of which this function retrieves all posts.
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator Returns a result set of posts.
     */
    public static function is_attached_to_forum_topic($forum_topic_id, $attachment_id)
    {
        $join = new Join(
            ForumPost::class,
            new EqualityCondition(
                new PropertyConditionVariable(
                    ForumPostAttachment::class,
                    ForumPostAttachment::PROPERTY_FORUM_POST_ID),
                new PropertyConditionVariable(ForumPost::class, ForumPost::PROPERTY_ID)));

        $joins = new Joins(array($join));

        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ForumPost::class, ForumPost::PROPERTY_FORUM_TOPIC_ID),
            new StaticConditionVariable($forum_topic_id));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ForumPostAttachment::class, ForumPostAttachment::PROPERTY_ATTACHMENT_ID),
            new StaticConditionVariable($attachment_id));

        $condition = new AndCondition($conditions);

        $parameters = new DataClassCountParameters($condition, $joins);

        return self::count(ForumPostAttachment::class, $parameters) > 0;
    }

    /**
     * Gets the number of post a topic has.
     *
     * @param $forum_topic_id int The id of a forum topic of which this function counts the posts.
     * @return int Returns the number of post of a topic.
     */
    public static function count_forum_topic_posts($forum_topic_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ForumPost::class, ForumPost::PROPERTY_FORUM_TOPIC_ID),
            new StaticConditionVariable($forum_topic_id));

        return self::count(ForumPost::class, new DataClassCountParameters($condition));
    }

    /**
     * Retrieves a forum post of a given topic
     *
     * @param int $topic_id
     * @param int $post_id
     *
     * @return ForumPost
     */
    public static function retrieve_forum_post_of_topic($topic_id, $post_id)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ForumPost::class, ForumPost::PROPERTY_FORUM_TOPIC_ID),
            new StaticConditionVariable($topic_id));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ForumPost::class, ForumPost::PROPERTY_ID),
            new StaticConditionVariable($post_id));

        $condition = new AndCondition($conditions);

        return self::retrieve(ForumPost::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * **************************************************************************************************************
     * Attachments *
     * **************************************************************************************************************
     */

    /**
     * This method is used to detach a content object from a particular forum post.
     *
     * @param $object ForumPost
     * @param $attachment_id int
     * @param $type int
     *
     * @return boolean returns true when successive delete
     */
    public static function detach_content_object($object, $attachment_id, $type = null)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ForumPostAttachment::class, ForumPostAttachment::PROPERTY_FORUM_POST_ID),
            new StaticConditionVariable($object->get_id()));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ForumPostAttachment::class, ForumPostAttachment::PROPERTY_ATTACHMENT_ID),
            new StaticConditionVariable($attachment_id));

        $condition = new AndCondition($conditions);

        return self::deletes(ForumPostAttachment::class, $condition);
    }

    /*
     * This function returns all attachment objects of a particular forum post. @param Condition $condition @return
     * Dataclass
     */
    public static function retrieve_attached_objects($forum_post_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ForumPostAttachment::class, ForumPostAttachment::PROPERTY_FORUM_POST_ID),
            new StaticConditionVariable($forum_post_id));

        return self::retrieves(ForumPostAttachment::class, new DataClassRetrievesParameters($condition));
    }

    /*
     * This function returns a ContentObject that is attached to a forum post. @param int $forum_post_id The id of a
     * forum post that has an attached ContentObject. @param int $attachment_id The id of a ContentObject that is
     * attached to a forum post. @return ContentObject
     */
    public static function retrieve_attached_object($forum_post_id, $attachment_id)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ForumPostAttachment::class, ForumPostAttachment::PROPERTY_FORUM_POST_ID),
            new StaticConditionVariable($forum_post_id));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ForumPostAttachment::class, ForumPostAttachment::PROPERTY_ATTACHMENT_ID),
            new StaticConditionVariable($attachment_id));

        $condition = new AndCondition($conditions);

        return self::retrieve(ForumPostAttachment::class, new DataClassRetrieveParameters($condition));
    }

    /*
     * This function counts the number of attachments objects of particular forum post. @Param Condition $condition
     * @Return int
     */
    public static function count_forum_post_attachments($forum_post_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ForumPostAttachment::class, ForumPostAttachment::PROPERTY_FORUM_POST_ID),
            new StaticConditionVariable($forum_post_id));

        return self::count(ForumPostAttachment::class, new DataClassCountParameters($condition));
    }

    /*
     * This method retrieves an array of the attached content objects of a particular post. @param int $forum_post_id
     * the id of the forum post. @param int $offset @param int $max_objects @param int $order_by @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator A
     * resultset of content objects.
     */
    public static function retrieve_attached_object_from_forum_post($forum_post_id, $offset = null, $max_objects = null,
        $order_by = null)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ForumPostAttachment::class, ForumPostAttachment::PROPERTY_FORUM_POST_ID),
            new StaticConditionVariable($forum_post_id));

        $joins = new Joins();

        $joins->add(
            new Join(
                ForumPostAttachment::class,
                new EqualityCondition(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                    new PropertyConditionVariable(
                        ForumPostAttachment::class,
                        ForumPostAttachment::PROPERTY_ATTACHMENT_ID))));

        $parameters = new DataClassRetrievesParameters($condition, $max_objects, $offset, $order_by, $joins);

        return self::retrieves(ContentObject::class, $parameters);
    }

    /**
     * **************************************************************************************************************
     * Subscribes *
     * **************************************************************************************************************
     */

    /**
     * This function is used to retrieve a single subscribe based on its Forum Topic id and its User ID.
     *
     * @param $forum_topic_id int The id of the forum topic.
     * @param $user_id int The id of the user.
     * @return ForumTopicSubscribe
     */
    public static function retrieve_subscribe($forum_topic_id, $user_id)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ForumTopicSubscribe::class,
                ForumTopicSubscribe::PROPERTY_FORUM_TOPIC_ID),
            new StaticConditionVariable($forum_topic_id));

        if ($user_id)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(ForumTopicSubscribe::class, ForumTopicSubscribe::PROPERTY_USER_ID),
                new StaticConditionVariable($user_id));
        }

        $condition = new AndCondition($conditions);

        return self::retrieve(ForumTopicSubscribe::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * Retrieve a subscribtion by its user id.
     *
     * @param $forum_topic_id int The id of the forum topic of which the function will retrieve the subscribtions.
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     *
     */
    public static function retrieve_subscribes($forum_topic_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ForumTopicSubscribe::class,
                ForumTopicSubscribe::PROPERTY_FORUM_TOPIC_ID),
            new StaticConditionVariable($forum_topic_id));

        return self::retrieves(ForumTopicSubscribe::class, new DataClassRetrievesParameters($condition));
    }

    /**
     * Gets the list of users who are subscribed to a forum topic.
     *
     * @param $forum_topic_id int
     *
     * @return List of subscribed users
     */
    public static function retrieve_subscribed_forum_topic_users($forum_topic_id)
    {
        $subscribeds = DataManager::retrieve_subscribes($forum_topic_id);
        $users = [];
        foreach ($subscribeds as $subscribe)
        {
            $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                User::class,
                (int) $subscribe->get_user_id());
            $users[$user->get_id()] = $user;
        }

        return $users;
    }

    /**
     * Create a subscribe based on a user id and topic id.
     *
     * @param $user_id int
     * @param $forum_topic_id int
     *
     * @return boolean Returns whether the create was succesfull.
     */
    public static function create_subscribe($user_id, $forum_topic_id)
    {
        $forum_subscribe = new ForumTopicSubscribe();
        $forum_subscribe->set_forum_topic_id($forum_topic_id);
        $forum_subscribe->set_user_id($user_id);

        return $forum_subscribe->create();
    }
}
