<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Forum;

use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\Forum;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Interfaces\CategorizableInterface;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package application.lib.weblcms.tool.forum
 */

/**
 * This tool allows a user to publish forums in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
    implements CategorizableInterface, IntroductionTextSupportInterface
{
    public const ACTION_BROWSE_FORUMS = 'Browser';
    public const ACTION_CHANGE_LOCK = 'ChangeLock';
    public const ACTION_FORUM_SUBSCRIBE = 'ForumSubscribe';
    public const ACTION_FORUM_UNSUBSCRIBE = 'ForumUnsubscribe';
    public const ACTION_MANAGE_CATEGORIES = 'CategoryManager';
    public const ACTION_PUBLISH_FORUM = 'Publisher';
    public const ACTION_VIEW_FORUM = 'Viewer';

    public const CONTEXT = __NAMESPACE__;

    public const PARAM_FORUM_ID = 'forum_id';
    public const PARAM_SUBSCRIBE_ID = 'subscribe';

    public static function get_allowed_types()
    {
        return [Forum::class];
    }

    public static function get_subforum_parents($subforum_id)
    {
        $parent = DataManager::retrieve_by_id(
            ComplexContentObjectItem::class, $subforum_id
        );

        while (!empty($parent))
        {
            $parents[] = $parent;
            $parent = DataManager::retrieve_complex_content_object_items(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_REF
                    ), new StaticConditionVariable($parent->get_parent())
                )
            );
            $parent = $parent[0];
        }
        $parents = array_reverse($parents);

        return $parents;
    }
}
