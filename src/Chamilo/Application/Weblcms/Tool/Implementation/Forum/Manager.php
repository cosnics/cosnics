<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Forum;

use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\Forum;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * $Id: forum_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.forum
 */

/**
 * This tool allows a user to publish forums in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements Categorizable
{
    const ACTION_BROWSE_FORUMS = 'browser';
    const ACTION_VIEW_FORUM = 'viewer';
    const ACTION_PUBLISH_FORUM = 'publisher';
    const ACTION_MANAGE_CATEGORIES = 'category_manager';
    const ACTION_CHANGE_LOCK = 'change_lock';
    const ACTION_FORUM_SUBSCRIBE = 'forum_subscribe';
    const ACTION_FORUM_UNSUBSCRIBE = 'forum_unsubscribe';
    const PARAM_SUBSCRIBE_ID = 'subscribe';
    const PARAM_FORUM_ID = 'forum_id';

    public static function get_allowed_types()
    {
        return array(Forum :: CLASS_NAME);
    }

    public static function get_subforum_parents($subforum_id)
    {
        $parent = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_item($subforum_id);

        while (! empty($parent))
        {
            $parents[] = $parent;
            $parent = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_items(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        ComplexContentObjectItem :: class_name(),
                        ComplexContentObjectItem :: PROPERTY_REF),
                    new StaticConditionVariable($parent->get_parent())))->as_array();
            $parent = $parent[0];
        }
        $parents = array_reverse($parents);

        return $parents;
    }
}
