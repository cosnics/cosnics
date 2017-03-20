<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\PeerAssessment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\PeerAssessmentAttempt as PeerAssessmentAttemptTracker;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\PeerAssessmentAttemptStatus as PeerAssessmentAttemptStatusTracker;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\PeerAssessmentFeedback;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\PeerAssessmentGroup as PeerAssessmentGroupTracker;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\PeerAssessmentGroupSubscription as PeerAssessmentGroupSubscriptionTracker;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\PeerAssessmentPublicationSetting as PeerAssessmentPublicationSettingTracker;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\PeerAssessmentScore as PeerAssessmentScoreTracker;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager as WeblcmsDataManager;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Storage\DataClass\PeerAssessment;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager as RepositoryDataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This tool allows a user to publish peer assessments in a course.
 *
 * @author Renaat De Muynck
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements IntroductionTextSupportInterface
{

    /**
     * Reference to the publication
     *
     * @var ContentObjectPublication
     */
    private $publication;

    static function get_allowed_types()
    {
        return array(PeerAssessment::class_name());
    }

    function get_application_component_path()
    {
        return __DIR__ . '/component/';
    }

    /**
     *
     * @todo fix reference to peerassessmentdisplay
     * @param type $toolbar
     * @param type $publication
     * @return type
     */

    /**
     * gets publication
     */
    public function get_publication()
    {
        if (! isset($this->publication))
        {
            $publication_id = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
            $this->publication = WeblcmsDataManager::retrieve_by_id(
                ContentObjectPublication::class_name(),
                $publication_id);
        }

        return $this->publication;
    }

    /**
     * gets publication id
     */
    public function get_publication_id()
    {
        return $this->get_publication()->get_id();
    }

    function get_root_content_object()
    {
        return $this->get_publication()->get_content_object();
    }

    public function get_groups($publication_id)
    {
        $tracker = new PeerAssessmentGroupTracker();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                PeerAssessmentGroupTracker::class_name(),
                PeerAssessmentGroupTracker::PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($publication_id));

        return DataManager::retrieves(
            PeerAssessmentGroupTracker::class_name(),
            new DataClassRetrievesParameters($condition))->as_array();
    }

    public function delete_group($id)
    {
        $tracker = new PeerAssessmentGroupTracker();
        return $tracker->remove(
            new EqualityCondition(
                new PropertyConditionVariable(
                    PeerAssessmentGroupTracker::class_name(),
                    PeerAssessmentGroupTracker::PROPERTY_ID),
                new StaticConditionVariable($id)));
    }

    public function get_group_users($group_id)
    {
        $tracker = new PeerAssessmentGroupSubscriptionTracker();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                PeerAssessmentGroupSubscriptionTracker::class_name(),
                PeerAssessmentGroupSubscriptionTracker::PROPERTY_GROUP_ID),
            new StaticConditionVariable($group_id));

        $items = DataManager::retrieves(
            PeerAssessmentGroupSubscriptionTracker::class_name(),
            new DataClassRetrievesParameters($condition))->as_array();

        $users = array();

        // loop through
        foreach ($items as $item)
        {
            $users[] = $this->get_user_info($item->get_user_id());
        }

        return $users;
    }

    public function count_group_users($group_id)
    {
        return count($this->get_group_users($group_id));
    }

    public function get_group($id)
    {
        $tracker = new PeerAssessmentGroupTracker();

        if (! $id)
            return $tracker;

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                PeerAssessmentGroupTracker::class_name(),
                PeerAssessmentGroupTracker::PROPERTY_ID),
            new StaticConditionVariable($id));

        return DataManager::retrieve(
            PeerAssessmentGroupTracker::class_name(),
            new DataClassRetrieveParameters($condition));
    }

    public function add_user_to_group($user_id, $group_id)
    {
        $tracker = new PeerAssessmentGroupSubscriptionTracker();

        $tracker->set_user_id($user_id);
        $tracker->set_group_id($group_id);

        if (! $this->user_is_enrolled_in_group($user_id))
        {
            return $tracker->create();
        }
        else
        {
            return false;
        }
    }

    public function get_user_group($user_id = null)
    {
        $tracker = new PeerAssessmentGroupSubscriptionTracker();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                PeerAssessmentGroupSubscriptionTracker::class_name(),
                PeerAssessmentGroupSubscriptionTracker::PROPERTY_USER_ID),
            new StaticConditionVariable($user_id));

        $items = DataManager::retrieves(
            PeerAssessmentGroupSubscriptionTracker::class_name(),
            new DataClassRetrievesParameters($condition))->as_array();

        $group_tracker = new PeerAssessmentGroupTracker();

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                PeerAssessmentGroupTracker::class_name(),
                PeerAssessmentGroupTracker::PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($this->get_publication_id()));

        $groups = DataManager::retrieves(
            PeerAssessmentGroupTracker::class_name(),
            new DataClassRetrievesParameters($condition))->as_array();

        // loop through
        foreach ($items as $item)
        {

            foreach ($groups as $group)
            {
                if ($group->get_id() == $item->get_group_id())
                {
                    return $group;
                }
            }
        }
        return;
    }

    public function get_group_feed_path()
    {
        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Ajax\Manager::context(),
                \Chamilo\Application\Weblcms\Ajax\Manager::PARAM_ACTION => 'XmlCourseUserGroupFeed',
                \Chamilo\Application\Weblcms\Manager::PARAM_COURSE => Request::get(
                    \Chamilo\Application\Weblcms\Manager::PARAM_COURSE),
                'show_groups' => 1));
        return $redirect->getUrl();
    }

    /**
     * checks if user is already enrolled in any peer_assessment_group belonging to this publication
     *
     * @param int $user_id
     * @return bool
     */
    function user_is_enrolled_in_group($user_id)
    {
        // get all peerassessmentgroups for this publication
        $publication_id = $this->get_publication_id();

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                PeerAssessmentGroupTracker::class_name(),
                PeerAssessmentGroupTracker::PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($publication_id));
        $groups = new PeerAssessmentGroupTracker();

        $groups_array = DataManager::retrieves(
            PeerAssessmentGroupTracker::class_name(),
            new DataClassRetrievesParameters($condition))->as_array();

        // get_all peerassessmentgroup subscriptions for user
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                PeerAssessmentGroupSubscriptionTracker::class_name(),
                PeerAssessmentGroupSubscriptionTracker::PROPERTY_USER_ID),
            new StaticConditionVariable($user_id));
        $users = new PeerAssessmentGroupSubscriptionTracker();

        $user_subscription_array = DataManager::retrieves(
            PeerAssessmentGroupSubscriptionTracker::class_name(),
            new DataClassRetrievesParameters($condition))->as_array();

        foreach ($user_subscription_array as $user_subscription)
        {
            $user_groups[] = $user_subscription->get_group_id();
        }

        // return true if there is a match
        foreach ($groups_array as $group)
        {
            if (in_array($group->get_id(), $user_groups))
                return true;
        }

        return false;
    }

    /**
     *
     * @todo make separate method for deleting all users
     * @param type $user_id use null if all users should be deleted
     * @param type $group_id
     * @return type
     */
    public function remove_user_from_group($user_id = null, $group_id)
    {
        $tracker = new PeerAssessmentGroupSubscriptionTracker();
        if (! is_null($user_id))
        {
            return $tracker->remove(
                new AndCondition(
                    array(
                        new EqualityCondition(
                            new PropertyConditionVariable(
                                PeerAssessmentGroupSubscriptionTracker::class_name(),
                                PeerAssessmentGroupSubscriptionTracker::PROPERTY_USER_ID),
                            new StaticConditionVariable($user_id)),
                        new EqualityCondition(
                            new PropertyConditionVariable(
                                PeerAssessmentGroupSubscriptionTracker::class_name(),
                                PeerAssessmentGroupSubscriptionTracker::PROPERTY_GROUP_ID),
                            new StaticConditionVariable($group_id)))));
        }
        else
        {
            return $tracker->remove(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        PeerAssessmentGroupSubscriptionTracker::class_name(),
                        PeerAssessmentGroupSubscriptionTracker::PROPERTY_GROUP_ID),
                    new StaticConditionVariable($group_id)));
        }
    }

    public function get_attempts($publication_id)
    {
        if ($publication_id)
        {
            $tracker = new PeerAssessmentAttemptTracker();
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    PeerAssessmentAttemptTracker::class_name(),
                    PeerAssessmentAttemptTracker::PROPERTY_PUBLICATION_ID),
                new StaticConditionVariable($publication_id));

            return DataManager::retrieves(
                PeerAssessmentAttemptTracker::class_name(),
                new DataClassRetrievesParameters($condition))->as_array();
        }
        else
        {
            throw new \ErrorException('NoPublicationId');
        }
    }

    public function get_attempt($id = null)
    {
        $tracker = new PeerAssessmentAttemptTracker();

        if (! $id)
            return $tracker;

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                PeerAssessmentAttemptTracker::class_name(),
                PeerAssessmentAttemptTracker::PROPERTY_ID),
            new StaticConditionVariable($id));

        return DataManager::retrieve(
            PeerAssessmentAttemptTracker::class_name(),
            new DataClassRetrieveParameters($condition));
    }

    public function delete_attempt($id)
    {
        $tracker = new PeerAssessmentAttemptTracker();
        return $tracker->remove(
            new EqualityCondition(
                new PropertyConditionVariable(
                    PeerAssessmentAttemptTracker::class_name(),
                    PeerAssessmentAttemptTracker::PROPERTY_ID),
                new StaticConditionVariable($id)));
    }

    public function delete_user_attempt_statuses($attempt_id, $user_id)
    {
        $tracker = new PeerAssessmentAttemptStatusTracker();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                PeerAssessmentAttemptStatusTracker::class_name(),
                PeerAssessmentAttemptStatusTracker::PROPERTY_ATTEMPT_ID),
            new StaticConditionVariable($attempt_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                PeerAssessmentAttemptStatusTracker::class_name(),
                PeerAssessmentAttemptStatusTracker::PROPERTY_USER_ID),
            new StaticConditionVariable($user_id));

        return $tracker->remove(new AndCondition($conditions));
    }

    public function get_user_attempt_status($user_id, $attempt_id)
    {
        $tracker = new PeerAssessmentAttemptStatusTracker();
        $condition = new AndCondition(
            new EqualityCondition(
                new PropertyConditionVariable(
                    PeerAssessmentAttemptStatusTracker::class_name(),
                    PeerAssessmentAttemptStatusTracker::PROPERTY_ATTEMPT_ID),
                new StaticConditionVariable($attempt_id)),
            new EqualityCondition(
                new PropertyConditionVariable(
                    PeerAssessmentAttemptStatusTracker::class_name(),
                    PeerAssessmentAttemptStatusTracker::PROPERTY_USER_ID),
                new StaticConditionVariable($user_id)));

        $items = DataManager::retrieves(
            PeerAssessmentAttemptStatusTracker::class_name(),
            new DataClassRetrievesParameters($condition))->as_array();

        return count($items) > 0 ? $items[0] : new PeerAssessmentAttemptStatusTracker(
            array(
                PeerAssessmentAttemptStatusTracker::PROPERTY_ID => null,
                PeerAssessmentAttemptStatusTracker::PROPERTY_ATTEMPT_ID => $attempt_id,
                PeerAssessmentAttemptStatusTracker::PROPERTY_USER_ID => $user_id,
                PeerAssessmentAttemptStatusTracker::PROPERTY_FACTOR => null,
                PeerAssessmentAttemptStatusTracker::PROPERTY_PROGRESS => null,
                PeerAssessmentAttemptStatusTracker::PROPERTY_CLOSED => null,
                PeerAssessmentAttemptStatusTracker::PROPERTY_CREATED => time(),
                PeerAssessmentAttemptStatusTracker::PROPERTY_MODIFIED => null));
    }

    /**
     * checks if scores are already given for an attempt or for entire publication
     *
     * @param int $attempt_id
     * @return boolean
     */
    public function has_scores($attempt_id = null)
    {
        if (is_null($attempt_id))
        {
            $attempts = $this->get_attempts($this->get_publication_id());
        }
        else
        {
            $attempts[] = $this->get_attempt($attempt_id);
        }
        foreach ($attempts as $attempt)
        {
            $status_object = new PeerAssessmentAttemptStatusTracker();

            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    PeerAssessmentAttemptStatusTracker::class_name(),
                    PeerAssessmentAttemptStatusTracker::PROPERTY_ATTEMPT_ID),
                new StaticConditionVariable($attempt->get_id()));

            $statuses = DataManager::retrieves(
                PeerAssessmentAttemptStatusTracker::class_name(),
                new DataClassRetrievesParameters($condition))->as_array();

            foreach ($statuses as $status)
            {
                $tracker = new PeerAssessmentScoreTracker();
                $condition = new EqualityCondition(
                    new PropertyConditionVariable(
                        PeerAssessmentAttemptStatusTracker::class_name(),
                        PeerAssessmentScoreTracker::PROPERTY_ATTEMPT_STATUS_ID),
                    new StaticConditionVariable($status->get_id()));

                $items = DataManager::retrieves(
                    PeerAssessmentScoreTracker::class_name(),
                    new DataClassRetrievesParameters($condition))->as_array();

                if (count($items) > 0)
                    return true;
            }
        }
        return false;
    }

    public function delete_status_scores($status_id)
    {
        $tracker = new PeerAssessmentScoreTracker();

        return $tracker->remove(
            new EqualityCondition(
                new PropertyConditionVariable(
                    PeerAssessmentScoreTracker::class_name(),
                    PeerAssessmentScoreTracker::PROPERTY_ATTEMPT_STATUS_ID),
                new StaticConditionVariable($status_id)));
    }

    public function delete_status_feedback($status_id)
    {
        $tracker = new PeerAssessmentFeedback();

        return $tracker->remove(
            new EqualityCondition(
                new PropertyConditionVariable(
                    PeerAssessmentFeedback::class_name(),
                    PeerAssessmentFeedback::PROPERTY_ATTEMPT_STATUS_ID),
                new StaticConditionVariable($status_id)));
    }

    public function get_indicators()
    {
        if (! isset($this->indicators))
        {
            $this->indicators = array();

            $object = $this->get_root_content_object($this);

            $children = RepositoryDataManager::retrieve_complex_content_object_items(
                ComplexContentObjectItem::class_name(),
                new EqualityCondition(
                    new PropertyConditionVariable(
                        ComplexContentObjectItem::class_name(),
                        ComplexContentObjectItem::PROPERTY_PARENT),
                    new StaticConditionVariable($object->get_id())));

            while ($child = $children->next_result())
            {
                // TODO check this ???
                // $this->indicators[] = $child;
                $this->indicators[] = RepositoryDataManager::retrieve_by_id(
                    ContentObject::class_name(),
                    $child->get_ref());
            }
        }

        return $this->indicators;
    }

    public function count_indicators()
    {
        return count($this->get_indicators());
    }

    public function get_builder_params()
    {
        return array(self::PARAM_ACTION => self::ACTION_BUILD_COMPLEX_CONTENT_OBJECT);
    }

    public function get_complex_display_params()
    {
        return array(self::PARAM_ACTION => self::ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT);
    }

    public function get_settings($publication_id)
    {
        $tracker = new PeerAssessmentPublicationSettingTracker();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                PeerAssessmentPublicationSettingTracker::class_name(),
                PeerAssessmentPublicationSettingTracker::PROPERTY_PUBLICATION_ID),
            new StaticConditionVariable($publication_id));

        $items = DataManager::retrieves(
            PeerAssessmentPublicationSettingTracker::class_name(),
            new DataClassRetrievesParameters($condition))->as_array();

        return count($items) > 0 ? $items[0] : $tracker;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $debug = "ok";
        // $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)),
        // Translation :: get('PeerAssessmentToolBrowserComponent')));
    }

    function get_context_group($context_group_id)
    {
        return WeblcmsDataManager::retrieve_course_group($context_group_id);
    }

    function get_context_group_users($context_group_id)
    {
        if ($users = \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager::retrieve_course_group_users(
            $context_group_id)->as_array())
        {
            return $users;
        }
    }

    /**
     * checks if a pa group has scores
     *
     * @param int $group_id
     * @return boolean
     */
    function group_has_scores($group_id)
    {
        $publication_id = $this->get_publication_id();
        $attempts = $this->get_attempts($publication_id);

        if (! is_null($group_id))
        {
            $users = $this->get_group_users($group_id);

            // checks for groups that have already started to give scores
            // check status for each attempt if status is there
            foreach ($users as $user)
            {
                foreach ($attempts as $attempt)
                {
                    $status = $this->get_user_attempt_status($user->get_id(), $attempt->get_id());
                    if (! is_null($status->get_id()))
                    {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function get_course_setting($setting)
    {
        return $this->get_course()->get_course_setting($setting);
    }
}