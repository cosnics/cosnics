<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupDecoratorsManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.lib.weblcms.course_group
 */
class CourseGroupSubscriptionsForm extends FormValidator
{

    private $parent;

    /**
     * @var CourseGroup
     */
    private $course_group;

    /**
     * @var CourseGroupDecoratorsManager
     */
    protected $courseGroupDecoratorsManager;

    public function __construct($course_group, $action, $parent, CourseGroupDecoratorsManager $courseGroupDecoratorsManager)
    {
        parent::__construct('course_settings', 'post', $action);
        $this->course_group = $course_group;
        $this->parent = $parent;
        $this->courseGroupDecoratorsManager = $courseGroupDecoratorsManager;

        $this->build_basic_form();
    }

    public function build_basic_form()
    {
        $searchUrl = new Redirect(
            array(
                Application::PARAM_CONTEXT => 'Chamilo\Application\Weblcms\Ajax',
                \Chamilo\Application\Weblcms\Ajax\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Ajax\Manager::ACTION_XML_COURSE_USER_GROUP_FEED,
                \Chamilo\Application\Weblcms\Manager::PARAM_COURSE => $this->parent->get_course_id()));

        $url = Path::getInstance()->getBasePath(true) .
             'index.php?go=XmlCourseUserGroupFeed&application=Chamilo%5CApplication%5CWeblcms%5CAjax&course=' .
             $this->parent->get_course_id();

        // Path :: getInstance()->getBasePath(true) .
        // 'application/weblcms/php/xml_feeds/xml_course_user_group_feed.php?course=' . $this->parent->get_course_id();

        $course_group_users = DataManager::retrieve_course_group_users($this->course_group->get_id());
        $defaults = array();
        $current = array();

        if ($course_group_users)
        {
            while ($course_group_user = $course_group_users->next_result())
            {
                $current[$course_group_user->get_id()] = array(
                    'id' => 'user_' . $course_group_user->get_id(),
                    'title' => Utilities::htmlentities($course_group_user->get_fullname()),
                    'description' => Utilities::htmlentities($course_group_user->get_username()),
                    'classes' => 'type type_user');

                // $defaults[$course_group_user->get_id()] = array('title' =>
                // htmlspecialchars($course_group_user->get_fullname()),
                // 'description' =>
                // htmlspecialchars($course_group_user->get_username()), 'class' =>
                // 'user');
            }
        }

        $locale = array();
        $locale['Display'] = Translation::get('SelectGroupUsers');
        $locale['Searching'] = Translation::get('Searching', null, Utilities::COMMON_LIBRARIES);
        $locale['NoResults'] = Translation::get('NoResults', null, Utilities::COMMON_LIBRARIES);
        $locale['Error'] = Translation::get('Error', null, Utilities::COMMON_LIBRARIES);
        $locale['load_elements'] = true;

        $legend_items = array();

        $legend_items[] = new ToolbarItem(
            Translation::get('CourseUser'),
            Theme::getInstance()->getCommonImagePath('Treemenu/User'),
            null,
            ToolbarItem::DISPLAY_ICON_AND_LABEL,
            false,
            'legend');

        $legend_items[] = new ToolbarItem(
            Translation::get('LinkedUser'),
            Theme::getInstance()->getCommonImagePath('Treemenu/UserPlatform'),
            null,
            ToolbarItem::DISPLAY_ICON_AND_LABEL,
            false,
            'legend');

        $legend = new Toolbar();
        $legend->set_items($legend_items);
        $legend->set_type(Toolbar::TYPE_HORIZONTAL);

        $elem = $this->addElement(
            'user_group_finder',
            'users',
            Translation::get('SubscribeUsers'),
            $searchUrl->getUrl(),
            $locale,
            $current,
            array('load_elements' => true));
        $elem->setDefaults($defaults);
        $this->addElement('static', null, null, $legend->as_html());

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation::get('Subscribe'),
            null,
            null,
            'log-in');
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_creation_form()
    {
        $this->build_basic_form();
    }

    public function update_course_group_subscriptions()
    {
        $values = $this->exportValues();

        $current_members_set = $this->course_group->get_members(false, false, true);
        $current_members = array();

        foreach ($current_members_set as $current_member)
        {
            $current_members[] = $current_member->get_id();
        }
        $updated_members = array();

        foreach ($values['users']['user'] as $value)
        {
            $updated_members[] = $value;
        }

        $members_to_delete = array_diff($current_members, $updated_members);
        $members_to_add = array_diff($updated_members, $current_members);

        if (($this->course_group->get_max_number_of_members() > 0) &&
             (count($values['users']['user']) > $this->course_group->get_max_number_of_members()))
        {
            $this->course_group->add_error(Translation::get('MaximumAmountOfMembersReached'));
            return false;
        }

        // check for max group subscription per member

        $parent_course_group = $this->course_group->get_parent();
        $course_groups = $parent_course_group->get_children(false);

        $max_group_subscriptions = $parent_course_group->get_max_number_of_course_group_per_member();
        $user_number_of_subscriptions = array();
        $not_subscribed_users = array();

        if ($max_group_subscriptions > 0)
        {
            // only when it is another course_group than the current one
            while ($course_group = $course_groups->next_result())
            {
                // check for each user how many times is he/she subscribed int
                // he course_group
                $user_ids = DataManager::retrieve_course_group_user_ids($course_group->get_id());

                $counter = 0;
                foreach ($members_to_add as $member)
                {
                    if (array_search($member, $user_ids) !== false)
                    {
                        if (count($user_number_of_subscriptions) > 0)
                        {
                            $counter2 = 0;
                            foreach ($user_number_of_subscriptions as $entry)
                            {
                                if (array_search($member, $entry) !== false)
                                {
                                    $number = $user_number_of_subscriptions[$counter][1];
                                    $user_number_of_subscriptions[$counter][0] = $member;
                                    $user_number_of_subscriptions[$counter][1] = $number + 1;

                                    $number_after_subscription = $user_number_of_subscriptions[$counter][1] + 1;

                                    if ($number_after_subscription > $max_group_subscriptions)
                                    {
                                        unset($members_to_add[array_search($member, $members_to_add)]);

                                        $not_subscribed_users[] = $member;
                                        $counter ++;
                                        break;
                                    }
                                }
                                else
                                {
                                    $next = $counter2 + 1;
                                    if ($next == count($user_number_of_subscriptions))
                                    {
                                        $number_after_subscription = 2;
                                        if ($number_after_subscription > $max_group_subscriptions)
                                        {
                                            unset($members_to_add[array_search($member, $members_to_add)]);

                                            $not_subscribed_users[] = $member;
                                        }
                                        else
                                        {
                                            $user_number_of_subscriptions[$counter] = array($member, 1);
                                        }
                                        $counter ++;
                                    }
                                    else
                                    {
                                        next($user_number_of_subscriptions);
                                    }
                                    $counter2 ++;
                                }
                            }
                        }
                        else
                        {
                            $number_after_subscription = 2;
                            if ($number_after_subscription > $max_group_subscriptions)
                            {
                                unset($members_to_add[array_search($member, $members_to_add)]);

                                $not_subscribed_users[] = $member;
                            }
                            else
                            {
                                $user_number_of_subscriptions[] = array($member, 1);
                            }
                            $counter ++;
                        }
                    }
                }
            }
        }

        if (count($not_subscribed_users) > 0)
        {
            foreach ($not_subscribed_users as $user_id)
            {
                $user_fullname = \Chamilo\Core\User\Storage\DataManager::get_fullname_from_user($user_id);
                $this->course_group->add_error($user_fullname . ' maximum number of group subscriptions is reached');
            }
            if (count($members_to_add) == 0)
            {
                return false;
            }
        }
        $succes = true;
        if (count($members_to_delete) > 0)
        {
            $succes = $this->course_group->unsubscribe_users($members_to_delete);

            foreach($members_to_delete as $userId)
            {
                $user = new User();
                $user->setId($userId);
                $this->courseGroupDecoratorsManager->unsubscribeUser($this->course_group, $user);
            }
        }

        if (count($members_to_add) > 0)
        {
            $condition = new InCondition(
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID),
                $members_to_add);
            $parameters = new DataClassRetrievesParameters($condition);
            $users_to_add = \Chamilo\Core\User\Storage\DataManager::retrieves(
                \Chamilo\Core\User\Storage\DataClass\User::class_name(),
                $parameters)->as_array();
            $succes &= $this->course_group->subscribe_users($users_to_add);

            foreach($users_to_add as $user)
            {
                $this->courseGroupDecoratorsManager->subscribeUser($this->course_group, $user);
            }
        }

        return $succes;
    }
}
