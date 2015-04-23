<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Forum\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Forum\Manager;
use Chamilo\Core\Repository\ContentObject\Forum\Display\ForumDisplaySupport;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\Forum;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * $Id: forum_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.forum.component
 */
class ViewerComponent extends Manager implements DelegateComponent, ForumDisplaySupport
{

    private $root_content_object;

    private $publication_id;

    public function run()
    {
        $this->publication_id = Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);

        $publication = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(),
            $this->publication_id);

        if (! $this->is_allowed(WeblcmsRights :: VIEW_RIGHT, $publication))
        {
            throw new NotAllowedException();
        }

        $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID, $this->publication_id);

        if (is_null(Request :: get(\Chamilo\Core\Repository\Display\Manager :: PARAM_ACTION)))
        {
            Request :: set_get(
                \Chamilo\Core\Repository\Display\Manager :: PARAM_ACTION,
                \Chamilo\Core\Repository\ContentObject\Forum\Display\Manager :: ACTION_VIEW_FORUM);
        }

        $this->root_content_object = $publication->get_content_object();

        $context = Forum :: context() . '\display';
        $factory = new ApplicationFactory($this->getRequest(), $context, $this->get_user(), $this);
        return $factory->run();
    }

    public function get_root_content_object()
    {
        return $this->root_content_object;
    }

    public function forum_topic_viewed($complex_topic_id)
    {
        $parameters = array();
        $parameters[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\ForumTopicView :: PROPERTY_USER_ID] = $this->get_user_id();
        $parameters[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\ForumTopicView :: PROPERTY_PUBLICATION_ID] = $this->publication_id;
        $parameters[\Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\ForumTopicView :: PROPERTY_FORUM_TOPIC_ID] = $complex_topic_id;

        Event :: trigger('view_forum_topic', \Chamilo\Application\Weblcms\Manager :: context(), $parameters);
    }

    public function forum_count_topic_views($complex_topic_id)
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\ForumTopicView :: class_name(),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\ForumTopicView :: PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($this->publication_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\ForumTopicView :: class_name(),
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\ForumTopicView :: PROPERTY_FORUM_TOPIC_ID),
            new StaticConditionVariable($complex_topic_id));
        $condition = new AndCondition($conditions);

        $dummy = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\ForumTopicView();
        return $dummy->count_tracker_items($condition);
    }

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
    }

    /**
     * function checks wether the user is forum manager
     *
     * @param $user type return bool;
     */
    public function is_forum_manager($user)
    {
        $is_forum_manager = false;

        if ($user->is_teacher())
        {

            $course = $this->get_course();

            $is_forum_manager = ($course->get_titular_id() == $user->get_id());

            if (! $is_forum_manager)
            {
                $is_forum_manager = $course->is_course_admin($user);
            }
        }

        return $is_forum_manager;
    }

    // METHODS FOR COMPLEX DISPLAY RIGHTS
    public function is_allowed_to_edit_content_object()
    {
        return $this->is_allowed(WeblcmsRights :: EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_view_content_object()
    {
        return $this->is_allowed(WeblcmsRights :: VIEW_RIGHT, $this->publication);
    }

    public function is_allowed_to_add_child()
    {
        return $this->is_allowed(WeblcmsRights :: EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_delete_child()
    {
        return $this->is_allowed(WeblcmsRights :: EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_delete_feedback()
    {
        return $this->is_allowed(WeblcmsRights :: EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_edit_feedback()
    {
        return $this->is_allowed(WeblcmsRights :: EDIT_RIGHT, $this->publication);
    }
}
