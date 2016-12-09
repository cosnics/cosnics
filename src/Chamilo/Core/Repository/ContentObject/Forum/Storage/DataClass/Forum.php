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
 * $Id: forum.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.forum
 */
/**
 * This class represents a discussion forum.
 */
class Forum extends ContentObject implements ComplexContentObjectSupport
{
    /**
     * **************************************************************************************************************
     * Table Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_LOCKED = 'locked';
    const PROPERTY_TOTAL_TOPICS = 'total_topics';
    const PROPERTY_TOTAL_POSTS = 'total_posts';
    const PROPERTY_LAST_POST = 'last_post_id';
    const PROPERTY_LAST_TOPIC_CHANGED_CLOI = 'last_topic_changed_cloi';
    const NAME_SPACE = __NAMESPACE__;

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
        ;
    }

    public function get_locked()
    {
        return $this->get_additional_property(self::PROPERTY_LOCKED);
    }

    public function set_locked($locked)
    {
        return $this->set_additional_property(self::PROPERTY_LOCKED, $locked);
    }

    public function get_last_topic_changed_cloi()
    {
        return $this->get_additional_property(self::PROPERTY_LAST_TOPIC_CHANGED_CLOI);
    }

    public function get_total_topics()
    {
        return $this->get_additional_property(self::PROPERTY_TOTAL_TOPICS);
    }

    public function set_total_topics($total_topics)
    {
        $this->set_additional_property(self::PROPERTY_TOTAL_TOPICS, $total_topics);
    }

    public function set_last_topic_changed_cloi($last_cloi)
    {
        $this->set_additional_property(self::PROPERTY_LAST_TOPIC_CHANGED_CLOI, $last_cloi);
    }

    public function get_total_posts()
    {
        return $this->get_additional_property(self::PROPERTY_TOTAL_POSTS);
    }

    public function set_total_posts($total_posts)
    {
        $this->set_additional_property(self::PROPERTY_TOTAL_POSTS, $total_posts);
    }

    public function get_last_post()
    {
        return $this->get_additional_property(self::PROPERTY_LAST_POST);
    }

    public function set_last_post($last_post)
    {
        $this->set_additional_property(self::PROPERTY_LAST_POST, $last_post);
    }

    /**
     * **************************************************************************************************************
     * Main functionality *
     * **************************************************************************************************************
     */
    
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
                ComplexContentObjectItem::class_name(), 
                ComplexContentObjectItem::PROPERTY_REF), 
            new StaticConditionVariable($this->get_id()));
        $wrappers = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class_name(), 
            $condition);
        
        while ($item = $wrappers->next_result())
        {
            $lo = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $item->get_parent());
            $lo->add_last_post($last_post);
        }
    }

    /**
     * After deleting a forum post recalculate the last post.
     * 
     * @param type $is_parent_forum
     * @param type $last_topic_changed_cloi
     * @param type $last_topic_changed_id
     *
     */
    public function recalculate_last_post($is_parent_forum, $last_topic_changed_cloi, $last_topic_changed_id)
    {
        $hulp_last_changed = $last_topic_changed_cloi;
        
        $lastpostforumtopics = \Chamilo\Core\Repository\ContentObject\Forum\Storage\DataManager::retrieve_last_post_forum_topics(
            $this->get_id());
        $last_post_subforums = \Chamilo\Core\Repository\ContentObject\Forum\Storage\DataManager::retrieve_forum_last_post_forum_subforums(
            $this->get_id());
        
        $lastpostistopic = true;
        
        $last_post_subforums_date = null;
        if ($last_post_subforums)
        {
            $last_post_subforums_date = \Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager::retrieve_forum_post_date(
                $last_post_subforums[Forum::PROPERTY_LAST_POST]);
        }
        
        $lastpostid = 0;
        if (! ($last_post_subforums_date == null && $lastpostforumtopics == null))
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
            ;
        }
        {
            $this->set_last_post($lastpostid);
            if (! ($lastpostid == 0))
            {
                $lastpostt = DataManager::retrieve_by_id(ForumPost::class_name(), $lastpostid);
                
                $lasttop = DataManager::retrieve_by_id(ForumTopic::class_name(), $lastpostt->get_forum_topic_id());
                
                if ($is_parent_forum && $lastpostistopic == false)
                {
                    if ($lasttop->get_id() == $last_topic_changed_id)
                    {
                        $this->set_last_topic_changed_cloi($hulp_last_changed);
                    }
                    else
                    {
                        $this->set_last_topic_changed_cloi(
                            $last_post_subforums[Forum::PROPERTY_LAST_TOPIC_CHANGED_CLOI]);
                    }
                }
                else
                {
                    if ($lastpostistopic == true)
                    {
                        $cond = new EqualityCondition(
                            new PropertyConditionVariable(
                                ComplexContentObjectItem::class_name(), 
                                ComplexContentObjectItem::PROPERTY_REF), 
                            new StaticConditionVariable($lasttop->get_id()));
                        
                        $wrap = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
                            ComplexContentObjectItem::class_name(), 
                            $cond);
                        
                        while ($item = $wrap->next_result())
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
                            $last_post_subforums[Forum::PROPERTY_LAST_TOPIC_CHANGED_CLOI]);
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
                    ComplexContentObjectItem::class_name(), 
                    ComplexContentObjectItem::PROPERTY_REF), 
                new StaticConditionVariable($this->get_id()));
            $wrappers = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
                ComplexContentObjectItem::class_name(), 
                $condition);
            $lasttopid = 0;
            
            if ($lasttop)
            {
                $lasttopid = $lasttop->get_id();
            }
            while ($item = $wrappers->next_result())
            {
                $lo = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                    ContentObject::class_name(), 
                    $item->get_parent());
                $lo->recalculate_last_post(true, $hulp_last_changed, $lasttopid);
            }
        }
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
                ComplexContentObjectItem::class_name(), 
                ComplexContentObjectItem::PROPERTY_REF), 
            new StaticConditionVariable($this->get_id()));
        $wrappers = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class_name(), 
            $condition);
        if ($email_notificator)
        {
            $email_notificator->add_users(
                \Chamilo\Core\Repository\ContentObject\Forum\Storage\DataManager::retrieve_subscribed_forum_users(
                    $this->get_id()));
        }
        
        while ($item = $wrappers->next_result())
        {
            $lo = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $item->get_parent());
            $lo->notify_subscribed_users($email_notificator);
        }
        // here it calls the add_last_post who does the update
        // TODO : ask if there is an edit the edited post becomes the last one
    }

    public static function get_additional_property_names()
    {
        return array(
            self::PROPERTY_LOCKED, 
            self::PROPERTY_TOTAL_TOPICS, 
            self::PROPERTY_TOTAL_POSTS, 
            self::PROPERTY_LAST_POST, 
            self::PROPERTY_LAST_TOPIC_CHANGED_CLOI);
    }

    public function get_allowed_types()
    {
        return array(Forum::class_name(), ForumTopic::class_name());
    }

    /**
     * This function sets the total posts of a forum, the last topic which is change, the last post and sets his
     * subscribers on the emailnotification.
     * Then checks wether the forum has parents(here if the forum is a subforum)
     * and calls in a recursien function the add_post
     * 
     * @param type $posts
     * @param type $email_notificator
     * @param type $last_changed_cloi
     * @param type $last_post_id
     */
    public function add_post($posts, $email_notificator, $last_changed_cloi, $last_post_id)
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
                    $this->get_id()));
        }
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class_name(), 
                ComplexContentObjectItem::PROPERTY_REF), 
            new StaticConditionVariable($this->get_id()));
        $wrappers = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class_name(), 
            $condition);
        
        while ($item = $wrappers->next_result())
        {
            $lo = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $item->get_parent());
            $lo->add_post($posts, $email_notificator, $last_changed_cloi, $last_post_id);
        }
    }

    public function remove_post($posts = 1)
    {
        $this->set_total_posts($this->get_total_posts() - $posts);
        $this->update();
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class_name(), 
                ComplexContentObjectItem::PROPERTY_REF), 
            new StaticConditionVariable($this->get_id()));
        $wrappers = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class_name(), 
            $condition);
        
        while ($item = $wrappers->next_result())
        {
            $lo = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $item->get_parent());
            $lo->remove_post($posts);
        }
    }

    public function add_topic($topics = 1)
    {
        $this->set_total_topics($this->get_total_topics() + $topics);
        $this->update();
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class_name(), 
                ComplexContentObjectItem::PROPERTY_REF), 
            new StaticConditionVariable($this->get_id()));
        $wrappers = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class_name(), 
            $condition);
        
        while ($item = $wrappers->next_result())
        {
            $lo = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $item->get_parent());
            $lo->add_topic($topics);
        }
    }

    public function remove_topic($topics = 1)
    {
        $this->set_total_topics($this->get_total_topics() - $topics);
        $this->update();
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class_name(), 
                ComplexContentObjectItem::PROPERTY_REF), 
            new StaticConditionVariable($this->get_id()));
        $wrappers = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class_name(), 
            $condition);
        
        while ($item = $wrappers->next_result())
        {
            $lo = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $item->get_parent());
            $lo->remove_topic($topics);
        }
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

    public function delete_complex_wrapper($object_id, $link_ids)
    {
        $failures = 0;
        
        foreach ($link_ids as $link_id)
        {
            $item = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ComplexContentObjectItem::class_name(), 
                $link_id);
            $object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $item->get_ref());
            
            if ($object->get_type() == Forum::class_name())
            {
                $this->set_total_topics($this->get_total_topics() - $object->get_total_topics());
            }
            
            $this->set_total_posts($this->get_total_post() - $object->get_total_post());
            
            if (! $item->delete())
            {
                $failures ++;
                continue;
            }
        }
        
        if (! $this->update())
            $failures ++;
        
        $message = $this->get_result(
            $failures, 
            count($link_ids), 
            'ComplexContentObjectItemNotDeleted', 
            'ComplexContentObjectItemsNotDeleted', 
            'ComplexContentObjectItemDeleted', 
            'ComplexContentObjectItemsDeleted');
        
        return array($message, ($failures > 0));
    }

    public function is_locked()
    {
        if ($this->get_locked())
        {
            return true;
        }
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class_name(), 
                ComplexContentObjectItem::PROPERTY_REF), 
            new StaticConditionVariable($this->get_id()));
        $parents = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class_name(), 
            $condition);
        
        while ($parent = $parents->next_result())
        {
            $content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $parent->get_parent());
            
            if ($content_object->is_locked())
            {
                return true;
            }
        }
        
        return false;
    }

    public function invert_locked()
    {
        $this->set_locked(! $this->get_locked());
        return $this->update();
    }
}
