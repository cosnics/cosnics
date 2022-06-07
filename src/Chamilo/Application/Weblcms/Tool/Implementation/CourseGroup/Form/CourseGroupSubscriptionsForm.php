<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupDecoratorsManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package application.lib.weblcms.course_group
 */
class CourseGroupSubscriptionsForm extends FormValidator
{

    /**
     * @var CourseGroupDecoratorsManager
     */
    protected $courseGroupDecoratorsManager;

    private $parent;

    /**
     * @var CourseGroup
     */
    private $course_group;

    public function __construct(
        $course_group, $action, $parent, CourseGroupDecoratorsManager $courseGroupDecoratorsManager
    )
    {
        parent::__construct('course_settings', self::FORM_METHOD_POST, $action);
        $this->course_group = $course_group;
        $this->parent = $parent;
        $this->courseGroupDecoratorsManager = $courseGroupDecoratorsManager;

        $this->build_basic_form();

        $this->setDefaults();
    }

    public function build_basic_form()
    {
        $legend_items = [];

        $legend_items[] = new ToolbarItem(
            Translation::get('CourseUser'), new FontAwesomeGlyph('user'), null, ToolbarItem::DISPLAY_ICON_AND_LABEL,
            false, 'legend'
        );

        $legend_items[] = new ToolbarItem(
            Translation::get('LinkedUser'), new FontAwesomeGlyph('link'), null, ToolbarItem::DISPLAY_ICON_AND_LABEL,
            false, 'legend'
        );

        $legend = new Toolbar();
        $legend->set_items($legend_items);
        $legend->setType(Toolbar::TYPE_HORIZONTAL);

        $types = new AdvancedElementFinderElementTypes();
        $types->add_element_type(
            new AdvancedElementFinderElementType(
                'user_group', Translation::get('UserGroup'), 'Chamilo\Application\Weblcms\Ajax', 'CourseGroupUserFeed',
                array(Manager::PARAM_COURSE => $this->parent->get_course_id())
            )
        );

        $this->addElement(
            'advanced_element_finder', 'users', Translation::get('SubscribeUsers'), $types
        );

        $this->addElement('static', null, null, $legend->as_html());

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit', Translation::get('Subscribe'), null, null,
            new FontAwesomeGlyph('sign-in-alt')
        );
        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, StringUtilities::LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    public function build_creation_form()
    {
        $this->build_basic_form();
    }

    function setDefaults($defaultValues = [], $filter = null)
    {
        $courseGroupUsers = DataManager::retrieve_course_group_users($this->course_group->get_id());

        usort(
            $courseGroupUsers, function ($courseGroupUser1, $courseGroupUser2) {
            return strcmp($courseGroupUser1->get_lastname(), $courseGroupUser2->get_lastname());
        }
        );

        $defaultUsers = new AdvancedElementFinderElements();

        foreach ($courseGroupUsers as $courseGroupUser)
        {
            $userGlyph = new FontAwesomeGlyph('user', [], null, 'fas');

            $defaultUsers->add_element(
                new AdvancedElementFinderElement(
                    'user_' . $courseGroupUser->getId(), $userGlyph->getClassNamesString(),
                    $courseGroupUser->get_fullname(), $courseGroupUser->get_username()
                )
            );
        }

        $element = $this->getElement('users');
        $element->setDefaultValues($defaultUsers);

        parent::setDefaults($defaultValues, $filter);
    }

    public function update_course_group_subscriptions()
    {
        $values = $this->exportValues();

        $current_members_set = $this->course_group->get_members(false, false, true);
        $current_members = [];

        foreach ($current_members_set as $current_member)
        {
            $current_members[] = $current_member->get_id();
        }
        $updated_members = [];

        foreach ($values['users']['user'] as $value)
        {
            $updated_members[] = $value;
        }

        $members_to_delete = array_diff($current_members, $updated_members);
        $members_to_add = array_diff($updated_members, $current_members);

        if (($this->course_group->get_max_number_of_members() > 0) &&
            (count($values['users']['user']) > $this->course_group->get_max_number_of_members()))
        {
            $this->course_group->addError(Translation::get('MaximumAmountOfMembersReached'));

            return false;
        }

        // check for max group subscription per member

        $parent_course_group = $this->course_group->get_parent();
        $course_groups = $parent_course_group->get_children();

        $max_group_subscriptions = $parent_course_group->get_max_number_of_course_group_per_member();
        $user_number_of_subscriptions = [];
        $not_subscribed_users = [];

        if ($max_group_subscriptions > 0)
        {
            // only when it is another course_group than the current one
            foreach($course_groups as $course_group)
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
                $this->course_group->addError($user_fullname . ' maximum number of group subscriptions is reached');
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

            foreach ($members_to_delete as $userId)
            {
                $user = new User();
                $user->setId($userId);
                $this->courseGroupDecoratorsManager->unsubscribeUser($this->course_group, $user);
            }
        }

        if (count($members_to_add) > 0)
        {
            $condition = new InCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_ID), $members_to_add
            );
            $parameters = new DataClassRetrievesParameters($condition);
            $users_to_add = \Chamilo\Core\User\Storage\DataManager::retrieves(
                User::class, $parameters
            );
            $succes &= $this->course_group->subscribe_users($users_to_add);

            foreach ($users_to_add as $user)
            {
                $this->courseGroupDecoratorsManager->subscribeUser($this->course_group, $user);
            }
        }

        return $succes;
    }
}
