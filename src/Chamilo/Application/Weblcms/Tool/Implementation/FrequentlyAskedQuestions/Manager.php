<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\FrequentlyAskedQuestions;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Storage\DataClass\FrequentlyAskedQuestions;

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
    const ACTION_MANAGE_CATEGORIES = 'CategoryManager';
    const PARAM_SUBSCRIBE_ID = 'subscribe';
    const PARAM_FAQ_ID = 'faq_id';

    public static function get_allowed_types()
    {
        return array(FrequentlyAskedQuestions :: class_name());
    }

    // public static function get_subforum_parents($subforum_id)
    // {
    // $parent = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
    // ComplexContentObjectItem :: class_name(),
    // $subforum_id);

    // while (! empty($parent))
    // {
    // $parents[] = $parent;
    // $parent = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_items(
    // new EqualityCondition(
    // new PropertyConditionVariable(
    // ComplexContentObjectItem :: class_name(),
    // ComplexContentObjectItem :: PROPERTY_REF),
    // new StaticConditionVariable($parent->get_parent())))->as_array();
    // $parent = $parent[0];
    // }
    // $parents = array_reverse($parents);

    // return $parents;
    // }
}
