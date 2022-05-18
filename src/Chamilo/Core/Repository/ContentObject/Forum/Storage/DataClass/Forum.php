<?php

namespace Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumTopic;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package repository.lib.content_object.forum
 */

/**
 * This class represents a discussion forum.
 */
class Forum extends ContentObject implements ComplexContentObjectSupport
{
    const NAME_SPACE = __NAMESPACE__;

    const PROPERTY_LAST_POST = 'last_post_id';
    const PROPERTY_LAST_TOPIC_CHANGED_CLOI = 'last_topic_changed_cloi';
    const PROPERTY_LOCKED = 'locked';
    const PROPERTY_TOTAL_POSTS = 'total_posts';
    const PROPERTY_TOTAL_TOPICS = 'total_topics';

    /**
     *
     * @param ForumPost $last_post
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
     * This function sets the total posts of a forum, the last topic which is change, the last post and sets his
     * subscribers on the emailnotification.
     * Then checks wether the forum has parents(here if the forum is a subforum)
     * and calls in a recursien function the add_post
     *
     * @param int $posts
     * @param $email_notificator
     * @param $last_changed_cloi
     * @param $last_post_id
     */
    public function add_post($posts, $email_notificator, $last_changed_cloi = null, $last_post_id = null)
    {
        $this->set_total_posts($this->get_total_posts() + $posts);

        if ($last_changed_cloi)
        {
            $this->set_last_topic_changed_cloi($last_changed_cloi);
            $this->set_last_post($last_post_id);
        }

        $this->update();

        if ($email_notificator)
        {
            $email_notificator->add_users(
                \Chamilo\Core\Repository\ContentObject\Forum\Storage\DataManager::retrieve_subscribed_forum_users(
                    $this->get_id()
                )
            );
        }

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
            $lo->add_post($posts, $email_notificator, $last_changed_cloi, $last_post_id);
        }
    }

    public function add_topic($topics = 1)
    {
        $this->set_total_topics($this->get_total_topics() + $topics);
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
            $lo->add_topic($topics);
        }
    }

    public function delete_complex_wrapper($object_id, $link_ids)
    {
        $failures = 0;

        foreach ($link_ids as $link_id)
        {
            $item = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ComplexContentObjectItem::class, $link_id
            );
            $object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class, $item->get_ref()
            );

            if ($object->get_type() == Forum::class)
            {
                $this->set_total_topics($this->get_total_topics() - $object->get_total_topics());
            }

            $this->set_total_posts($this->get_total_post() - $object->get_total_post());

            if (!$item->delete())
            {
                $failures ++;
            }
        }

        if (!$this->update())
        {
            $failures ++;
        }

        $message = $this->get_result(
            $failures, count($link_ids), 'ComplexContentObjectItemNotDeleted', 'ComplexContentObjectItemsNotDeleted',
            'ComplexContentObjectItemDeleted', 'ComplexContentObjectItemsDeleted'
        );

        return array($message, ($failures > 0));
    }

    public function delete_links()
    {
        $success = parent::delete_links();
        if ($success)
        {
            $this->set_total_posts(0);
            $this->set_total_topics(0);
            $success = $this->update();
        }

        return $success;
    }

    public static function get_additional_property_names()
    {
        return array(
            self::PROPERTY_LOCKED,
            self::PROPERTY_TOTAL_TOPICS,
            self::PROPERTY_TOTAL_POSTS,
            self::PROPERTY_LAST_POST,
            self::PROPERTY_LAST_TOPIC_CHANGED_CLOI
        );
    }

    public function get_allowed_types()
    {
        return array(Forum::class, ForumTopic::class);
    }

    public function get_last_post()
    {
        return $this->get_additional_property(self::PROPERTY_LAST_POST);
    }

    public function get_last_topic_changed_cloi()
    {
        return $this->get_additional_property(self::PROPERTY_LAST_TOPIC_CHANGED_CLOI);
    }

    public function get_locked()
    {
        return $this->get_additional_property(self::PROPERTY_LOCKED);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'repository_forum';
    }

    /**
     * @return int
     */
    public function get_total_posts()
    {
        return $this->get_additional_property(self::PROPERTY_TOTAL_POSTS);
    }

    /**
     * **************************************************************************************************************
     * Main functionality *
     * **************************************************************************************************************
     */

    public function get_total_topics()
    {
        return $this->get_additional_property(self::PROPERTY_TOTAL_TOPICS);
    }

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class, true);
    }

    public function invert_locked()
    {
        $this->set_locked(!$this->get_locked());

        return $this->update();
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
     * @param type $email_notificator
     */
    public function notify_subscribed_users($email_notificator)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_REF
            ), new StaticConditionVariable($this->get_id())
        );
        $wrappers = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class, $condition
        );
        if ($email_notificator)
        {
            $email_notificator->add_users(
                \Chamilo\Core\Repository\ContentObject\Forum\Storage\DataManager::retrieve_subscribed_forum_users(
                    $this->get_id()
                )
            );
        }

        foreach ($wrappers as $item)
        {
            $lo = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class, $item->get_parent()
            );
            $lo->notify_subscribed_users($email_notificator);
        }
        // here it calls the add_last_post who does the update
        // TODO : ask if there is an edit the edited post becomes the last one
    }

    /**
     * After deleting a forum post recalculate the last post.
     *
     * @param type $is_parent_forum
     * @param type $last_topic_changed_cloi
     * @param type $last_topic_changed_id
     *
     */
    public function recalculate_last_post(
        $is_parent_forum = false, $last_topic_changed_cloi = null, $last_topic_changed_id = null
    )
    {
        $hulp_last_changed = $last_topic_changed_cloi;

        $lastpostforumtopics =
            \Chamilo\Core\Repository\ContentObject\Forum\Storage\DataManager::retrieve_last_post_forum_topics(
                $this->get_id()
            );
        $last_post_subforums =
            \Chamilo\Core\Repository\ContentObject\Forum\Storage\DataManager::retrieve_forum_last_post_forum_subforums(
                $this->get_id()
            );

        $lastpostistopic = true;

        $last_post_subforums_date = null;
        if ($last_post_subforums)
        {
            $last_post_subforums_date = DataManager::retrieve_forum_post_date(
                $last_post_subforums[Forum::PROPERTY_LAST_POST]
            );
        }

        $lastpostid = 0;
        if (!($last_post_subforums_date == null && $lastpostforumtopics == null))
        {
            if ($last_post_subforums_date == null)
            {
                $lastpostid = $lastpostforumtopics->get_id();
                $lastpostistopic = true;
            }
            else
            {
                if ($lastpostforumtopics == null)
                {
                    $lastpostid = $last_post_subforums[Forum::PROPERTY_LAST_POST];
                    $lastpostistopic = false;
                }
                else
                {
                    if ($last_post_subforums_date > $lastpostforumtopics->get_modification_date())
                    {
                        $lastpostid = $last_post_subforums[Forum::PROPERTY_LAST_POST];
                        $lastpostistopic = false;
                    }
                    else
                    {
                        $lastpostid = $lastpostforumtopics->get_id();
                        $lastpostistopic = true;
                    }
                }
            }
        }

        if ($this->get_last_post() != $lastpostid)
        {
        }
        {
            $this->set_last_post($lastpostid);
            if (!($lastpostid == 0))
            {
                $lastpostt = DataManager::retrieve_by_id(ForumPost::class, $lastpostid);

                $lasttop = DataManager::retrieve_by_id(ForumTopic::class, $lastpostt->get_forum_topic_id());

                if ($is_parent_forum && $lastpostistopic == false)
                {
                    if ($lasttop->get_id() == $last_topic_changed_id)
                    {
                        $this->set_last_topic_changed_cloi($hulp_last_changed);
                    }
                    else
                    {
                        $this->set_last_topic_changed_cloi(
                            $last_post_subforums[Forum::PROPERTY_LAST_TOPIC_CHANGED_CLOI]
                        );
                    }
                }
                else
                {
                    if ($lastpostistopic == true)
                    {
                        $cond = new EqualityCondition(
                            new PropertyConditionVariable(
                                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_REF
                            ), new StaticConditionVariable($lasttop->get_id())
                        );

                        $wrap = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
                            ComplexContentObjectItem::class, $cond
                        );

                        foreach ($wrap as $item)
                        {
                            if ($item->get_parent() == $this->get_id())
                            {
                                $this->set_last_topic_changed_cloi($item->get_id());
                            }
                        }
                    }
                    else
                    {
                        $this->set_last_topic_changed_cloi(
                            $last_post_subforums[Forum::PROPERTY_LAST_TOPIC_CHANGED_CLOI]
                        );
                    }
                }
            }
            else
            {
                $this->set_last_topic_changed_cloi(0);
            }

            $hulp_last_changed = $this->get_last_topic_changed_cloi();
            $this->update();

            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_REF
                ), new StaticConditionVariable($this->get_id())
            );
            $wrappers = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
                ComplexContentObjectItem::class, $condition
            );
            $lasttopid = 0;

            if ($lasttop)
            {
                $lasttopid = $lasttop->get_id();
            }
            foreach ($wrappers as $item)
            {
                $lo = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                    ContentObject::class, $item->get_parent()
                );
                $lo->recalculate_last_post(true, $hulp_last_changed, $lasttopid);
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
    }

    public function remove_topic($topics = 1)
    {
        $this->set_total_topics($this->get_total_topics() - $topics);
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
            $lo->remove_topic($topics);
        }
    }

    public function set_last_post($last_post)
    {
        $this->set_additional_property(self::PROPERTY_LAST_POST, $last_post);
    }

    public function set_last_topic_changed_cloi($last_cloi)
    {
        $this->set_additional_property(self::PROPERTY_LAST_TOPIC_CHANGED_CLOI, $last_cloi);
    }

    public function set_locked($locked)
    {
        return $this->set_additional_property(self::PROPERTY_LOCKED, $locked);
    }

    public function set_total_posts($total_posts)
    {
        $this->set_additional_property(self::PROPERTY_TOTAL_POSTS, $total_posts);
    }

    public function set_total_topics($total_topics)
    {
        $this->set_additional_property(self::PROPERTY_TOTAL_TOPICS, $total_topics);
    }
}
