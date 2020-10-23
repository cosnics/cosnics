<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Exception;

/**
 *
 * @package repository.lib.complex_display.assessment
 */

/**
 * This tool allows a user to publish assessments in his or her course.
 */
abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
{
    const ACTION_VIEW_FORUM = 'ForumViewer';
    const ACTION_VIEW_TOPIC = 'TopicViewer';
    const ACTION_PREVIEW_TOPIC = 'TopicPreviewer';
    const ACTION_PUBLISH_FORUM = 'Publisher';
    const ACTION_VIEW_ATTACHMENT = 'AttachmentViewer';
    const ACTION_CREATE_FORUM_POST = 'ForumPostCreator';
    const ACTION_EDIT_FORUM_POST = 'ForumPostEditor';
    const ACTION_DELETE_FORUM_POST = 'ForumPostDeleter';
    const ACTION_QUOTE_FORUM_POST = 'ForumPostQuoter';
    const ACTION_CREATE_TOPIC = 'ForumTopicCreator';
    const ACTION_DELETE_TOPIC = 'ForumTopicDeleter';
    const ACTION_CREATE_SUBFORUM = 'ForumSubforumCreator';
    const ACTION_EDIT_SUBFORUM = 'ForumSubforumEditor';
    const ACTION_DELETE_SUBFORUM = 'ForumSubforumDeleter';
    const ACTION_MAKE_IMPORTANT = 'Important';
    const ACTION_MAKE_STICKY = 'Sticky';
    const ACTION_CHANGE_LOCK = 'ChangeLock';
    const ACTION_FORUM_SUBSCRIBE = 'ForumSubscribe';
    const ACTION_FORUM_UNSUBSCRIBE = 'ForumUnsubscribe';
    const ACTION_TOPIC_SUBSCRIBE = 'TopicSubscribe';
    const ACTION_TOPIC_UNSUBSCRIBE = 'TopicUnsubscribe';
    const PARAM_SELECTED_FORUM_POST = 'selected_forum_post';
    const PARAM_SUBSCRIBE_ID = 'subscribe_id';
    const PARAM_ATTACHMENT_ID = 'attachment_id';
    const PARAM_FORUM_TOPIC_ID = 'topic_id';
    const PARAM_CURRENT_SESSION_PARENT_CLOI = 'parent_cloi';
    const PARAM_LAST_POST = 'last_post';
    const DEFAULT_ACTION = self::ACTION_VIEW_FORUM;

    protected $forum;

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param unknown $user
     * @param unknown $parent
     * @throws \Exception
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        if ($applicationConfiguration->getApplication() instanceof ForumDisplaySupport)
        {
            parent::__construct($applicationConfiguration);
        }
        else
        {
            throw new Exception(
                get_class($applicationConfiguration->getApplication()) . ' must implement ForumDisplaySupport ');
        }
    }

    public function forum_topic_viewed($complex_topic_id)
    {
        return $this->get_parent()->forum_topic_viewed($complex_topic_id);
    }

    public function forum_count_topic_views($complex_topic_id)
    {
        return $this->get_parent()->forum_count_topic_views($complex_topic_id);
    }

    /**
     * ask the parent of the usee is a forum manager
     *
     * @param type $user return boolean
     */
    public function is_forum_manager($user)
    {
        $parent = $this->get_parent();
        return $parent->is_forum_manager($user);
    }

    /**
     * Gets an array of the first path found from a forum to his subforum
     *
     * @param type $children from the root (start) of the path
     * @param type $complex_content_item_id to which forum it must go
     * @param boolean $founded
     *
     * @return array;
     *
     * @author Mattias De Pauw
     */
    public function retrieve_children_from_root_to_cloi($root_complex_content_ref, $complex_content_item_id)
    {
        $copy_children = array();
        $wrappers = array();

        $children = DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class,
            new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class,
                    ComplexContentObjectItem::PROPERTY_PARENT),
                new StaticConditionVariable($root_complex_content_ref),
                ComplexContentObjectItem::get_table_name()));

        foreach($children as $child)
        {
            $copy_children[$child->get_id()] = $child->get_ref();

            if ($child->get_id() == $complex_content_item_id)
            {
                $content_object = DataManager::retrieve_by_id(
                    ContentObject::class,
                    $child->get_ref());

                $wrappers[$child->get_id()] = $content_object;

                return $wrappers;
            }
        }

        // if nothing is returned proceed method

        foreach ($copy_children as $key => $value)
        {
            $wrap_child = array();
            $wrap_child = $this->retrieve_children_from_root_to_cloi($value, $complex_content_item_id);

            if ($wrap_child)
            {
                $content_object = DataManager::retrieve_by_id(
                    ContentObject::class,
                    $value);

                $wrappers[$key] = $content_object;

                foreach ($wrap_child as $key_child => $value_child)
                {
                    $wrappers[$key_child] = $value_child;
                }

                return $wrappers;
            }
        }
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\Forum\Display\Manager::is_forum_manager()
     */
    public function isForumManager($user)
    {
        return $this->get_parent()->is_forum_manager($user);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\Forum;
     */
    public function getForum()
    {
        if (! isset($this->forum))
        {
            if (! $this->get_complex_content_object_item())
            {
                $this->forum = $this->get_root_content_object();
            }
            else
            {
                $this->forum = DataManager::retrieve_by_id(
                    ContentObject::class,
                    $this->get_complex_content_object_item()->get_ref());
            }
        }

        return $this->forum;
    }
}
