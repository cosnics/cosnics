<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Storage;

use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\Forum;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\ForumSubscribe;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumTopic;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Parameters\DataClassParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Class for the specific Forum data management, it receives data calls and delegates it to the mdb2 class.
 *
 * @author Mattias De Pauw - Hogeschool Gent
 * @author Maarten Volckaert - Hogeschool Gent
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    public const PREFIX = 'repository_';

    /**
     * Gets the number of subscribers a forum has
     *
     * @param $forum_id int The id of a forum of which this function counts the subscribers.
     *
     * @return int Returns the number of subscribers of a forum.
     */
    public static function count_forum_subscribers($forum_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ForumSubscribe::class, ForumSubscribe::PROPERTY_FORUM_ID),
            new StaticConditionVariable($forum_id)
        );

        return self::count(ForumSubscribe::class, new DataClassParameters(condition: $condition));
    }

    /**
     * Creates a new subscribe based on a user id and a forum id.
     *
     * @param $user_id  int
     * @param $forum_id int
     *
     * @return bool Returns whether the creation was succesfull.
     */
    public static function create_subscribe($user_id, $forum_id)
    {
        $forum_subscribe = new ForumSubscribe();
        $forum_subscribe->set_forum_id($forum_id);
        $forum_subscribe->set_user_id($user_id);

        return $forum_subscribe->create();
    }

    /**
     * Calculates the last post from the subforums in a forum
     *
     * @param int $forum_id
     *
     * @return ComplexContentObjectItem
     */
    public static function retrieve_forum_last_post_forum_subforums($forum_id)
    {
        $properties = new RetrieveProperties();

        $properties->add(
            new PropertyConditionVariable(ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_ID)
        );

        $properties->add(new PropertyConditionVariable(Forum::class, Forum::PROPERTY_LAST_POST));

        $properties->add(new PropertyConditionVariable(Forum::class, Forum::PROPERTY_LAST_TOPIC_CHANGED_CLOI));

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
            ), new StaticConditionVariable($forum_id)
        );

        $order_by = OrderBy::generate(ForumPost::class, ForumPost::PROPERTY_CREATION_DATE, SORT_DESC);

        $joins = new Joins();

        $joins->add(
            new Join(
                Forum::class, new EqualityCondition(
                    new PropertyConditionVariable(
                        ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_REF
                    ), new PropertyConditionVariable(Forum::class, Forum::PROPERTY_ID)
                )
            )
        );

        $joins->add(
            new Join(
                ForumPost::class, new EqualityCondition(
                    new PropertyConditionVariable(Forum::class, Forum::PROPERTY_LAST_POST),
                    new PropertyConditionVariable(ForumPost::class, ForumPost::PROPERTY_ID)
                )
            )
        );

        $parameters = new DataClassParameters(
            condition: $condition, orderBy: $order_by, joins: $joins, retrieveProperties: $properties
        );

        return self::record(ComplexContentObjectItem::class, $parameters);
    }

    /**
     * Calculates the last post from the forum topics in a forum
     *
     * @param int $forum_id
     *
     * @return ComplexContentObjectItem
     */
    public static function retrieve_last_post_forum_topics($forum_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
            ), new StaticConditionVariable($forum_id)
        );

        $joins = new Joins();

        $joins->add(
            new Join(
                ForumTopic::class, new EqualityCondition(
                    new PropertyConditionVariable(ForumPost::class, ForumPost::PROPERTY_FORUM_TOPIC_ID),
                    new PropertyConditionVariable(ForumTopic::class, ForumTopic::PROPERTY_ID)
                )
            )
        );

        $joins->add(
            new Join(
                ComplexContentObjectItem::class, new EqualityCondition(
                    new PropertyConditionVariable(ForumTopic::class, ForumTopic::PROPERTY_ID),
                    new PropertyConditionVariable(
                        ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_REF
                    )
                )
            )
        );

        $joins->add(
            new Join(
                Forum::class, new EqualityCondition(
                    new PropertyConditionVariable(
                        ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
                    ), new PropertyConditionVariable(Forum::class, Forum::PROPERTY_ID)
                )
            )
        );

        $order_by = [];
        $order_by[] = new OrderProperty(
            new PropertyConditionVariable(ForumPost::class, ForumPost::PROPERTY_CREATION_DATE), SORT_DESC
        );

        $parameters = new DataClassParameters(condition: $condition, orderBy: new OrderBy($order_by), joins: $joins);

        return self::retrieve(ForumPost::class, $parameters);
    }

    /**
     * This function retrieves a ForumSubscribe object based on its forum id and user id.
     *
     * @param int $forum_id
     * @param int $user_id
     *
     * @return ForumSubscribe
     */
    public static function retrieve_subscribe($forum_id, $user_id)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ForumSubscribe::class, ForumSubscribe::PROPERTY_FORUM_ID),
            new StaticConditionVariable($forum_id)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ForumSubscribe::class, ForumSubscribe::PROPERTY_USER_ID),
            new StaticConditionVariable($user_id)
        );

        $condition = new AndCondition($conditions);

        return self::retrieve(ForumSubscribe::class, new DataClassParameters(condition: $condition));
    }

    /**
     * Gets the list of users who are subscribed to a forum.
     *
     * @param $forum_id int
     *
     * @return List of subscribed users
     */
    public static function retrieve_subscribed_forum_users($forum_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ForumSubscribe::class, ForumSubscribe::PROPERTY_FORUM_ID),
            new StaticConditionVariable($forum_id)
        );

        $subscriptions = DataManager::retrieves(
            ForumSubscribe::class, new DataClassParameters(condition: $condition)
        );

        $users = [];

        foreach ($subscriptions as $subscription)
        {
            $user = DataManager::retrieve_by_id(
                User::class, (string) $subscription->get_user_id()
            );
            $users[$user->get_id()] = $user;
        }

        return $users;
    }
}
