<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * $Id: assessment_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_display.assessment
 */

/**
 * This tool allows a user to publish assessments in his or her course.
 */
abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
{
    const ACTION_VIEW_FORUM = 'forum_viewer';
    const ACTION_VIEW_TOPIC = 'topic_viewer';
    const ACTION_PREVIEW_TOPIC = 'topic_previewer';
    const ACTION_PUBLISH_FORUM = 'publisher';
    const ACTION_VIEW_ATTACHMENT = 'attachment_viewer';
    const ACTION_CREATE_FORUM_POST = 'forum_post_creator';
    const ACTION_EDIT_FORUM_POST = 'forum_post_editor';
    const ACTION_DELETE_FORUM_POST = 'forum_post_deleter';
    const ACTION_QUOTE_FORUM_POST = 'forum_post_quoter';
    const ACTION_CREATE_TOPIC = 'forum_topic_creator';
    const ACTION_DELETE_TOPIC = 'forum_topic_deleter';
    const ACTION_CREATE_SUBFORUM = 'forum_subforum_creator';
    const ACTION_EDIT_SUBFORUM = 'forum_subforum_editor';
    const ACTION_DELETE_SUBFORUM = 'forum_subforum_deleter';
    const ACTION_MAKE_IMPORTANT = 'important';
    const ACTION_MAKE_STICKY = 'sticky';
    const ACTION_CHANGE_LOCK = 'change_lock';
    const ACTION_FORUM_SUBSCRIBE = 'forum_subscribe';
    const ACTION_FORUM_UNSUBSCRIBE = 'forum_unsubscribe';
    const ACTION_TOPIC_SUBSCRIBE = 'topic_subscribe';
    const ACTION_TOPIC_UNSUBSCRIBE = 'topic_unsubscribe';
    const PARAM_SELECTED_FORUM_POST = 'selected_forum_post';
    const PARAM_SUBSCRIBE_ID = 'subscribe_id';
    const PARAM_ATTACHMENT_ID = 'attachment_id';
    const PARAM_FORUM_TOPIC_ID = 'topic_id';
    const PARAM_CURRENT_SESSION_PARENT_CLOI = 'parent_cloi';
    const PARAM_LAST_POST = 'last_post';
    const DEFAULT_ACTION = self :: ACTION_VIEW_FORUM;

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param unknown $user
     * @param unknown $parent
     * @throws \Exception
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request, $user = null, $parent = null)
    {
        if ($parent instanceof ForumDisplaySupport)
        {
            parent :: __construct($request, $user, $parent);
        }
        else
        {
            throw new \Exception(get_class($parent) . ' must implement ForumDisplaySupport ');
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

        $children = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_items(
            ComplexContentObjectItem :: class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem :: class_name(),
                    ComplexContentObjectItem :: PROPERTY_PARENT),
                new StaticConditionVariable($root_complex_content_ref),
                ComplexContentObjectItem :: get_table_name()));

        while ($child = $children->next_result())
        {
            $copy_children[$child->get_id()] = $child->get_ref();

            if ($child->get_id() == $complex_content_item_id)
            {
                $content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object(
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
                $content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object($value);

                $wrappers[$key] = $content_object;

                foreach ($wrap_child as $key_child => $value_child)
                {
                    $wrappers[$key_child] = $value_child;
                }

                return $wrappers;
            }
        }
    }
}
