<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\Subscribed\SubscribedUserTable;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package application.lib.weblcms.tool.course_group.component
 */
class UnsubscribeBrowserComponent extends Manager implements TableSupport, DelegateComponent
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    private $course_group;

    public function run()
    {
        $course_group_id = Request::get(self::PARAM_COURSE_GROUP);
        $this->set_parameter(self::PARAM_COURSE_GROUP, $course_group_id);

        /** @var CourseGroup $course_group */
        $course_group = DataManager::retrieve_by_id(CourseGroup::class, $course_group_id);
        if (!$course_group)
        {
            throw new ObjectNotExistException(Translation::get('CourseGroup'), $course_group_id);
        }

        $this->course_group = $course_group;
        BreadcrumbTrail::getInstance()->add(
            new Breadcrumb(
                $this->get_url(),
                Translation::get('UnsubscribeBrowserComponent', array('GROUPNAME' => $course_group->get_name()))
            )
        );

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $html = [];

        $html[] = $this->render_header();
        $html[] = '<div style="clear: both;">&nbsp;</div>';

        $users = $this->getRequest()->get(\Chamilo\Application\Weblcms\Manager::PARAM_USERS);

        if ($users)
        {
            if (!is_array($users))
            {
                $users = array($users);
            }

            foreach ($users as $user)
            {
                $course_group->unsubscribe_users($user);

                $userObject = new User();
                $userObject->setId($user);
                $this->getCourseGroupDecoratorsManager()->unsubscribeUser($course_group, $userObject);
            }

            $message = Translation::get(count($users) > 1 ? 'UsersUnsubscribed' : 'UserUnsubscribed');
            $this->redirect(
                $message, false, array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_UNSUBSCRIBE,
                    self::PARAM_COURSE_GROUP => $course_group_id
                )
            );
        }

        $table = new SubscribedUserTable($this);
        $html[] = $this->buttonToolbarRenderer->render();

        // Details

        $html[] = '<div class="panel panel-default">';

        $glyph = new FontAwesomeGlyph('info-circle', array('fa-lg'), null, 'fas');

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">';
        $html[] = $glyph->render() . ' ' . $course_group->get_name();
        $html[] = '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';
        $html[] = $course_group->get_description();
        $html[] = '<div class="clearfix"></div>';

        $html[] = '<b>' . Translation::get('NumberOfMembers') . ':</b> ' . $course_group->count_members();
        $html[] =
            '<br /><b>' . Translation::get('MaximumMembers') . ':</b> ' . $course_group->get_max_number_of_members();
        $html[] = '<br /><b>' . Translation::get('SelfRegistrationAllowed') . ':</b> ' .
            ($course_group->is_self_registration_allowed() ? Translation::get(
                'ConfirmYes', null, StringUtilities::LIBRARIES
            ) : Translation::get('ConfirmNo', null, StringUtilities::LIBRARIES));
        $html[] = '<br /><b>' . Translation::get('SelfUnRegistrationAllowed') . ':</b> ' .
            ($course_group->is_self_unregistration_allowed() ? Translation::get(
                'ConfirmYes', null, StringUtilities::LIBRARIES
            ) : Translation::get('ConfirmNo', null, StringUtilities::LIBRARIES));
        $html[] = '<br /><b>' . Translation::get('RandomlySubscribed') . ':</b> ' .
            ($course_group->is_random_registration_done() ? Translation::get(
                'ConfirmYes', null, StringUtilities::LIBRARIES
            ) : Translation::get('ConfirmNo', null, StringUtilities::LIBRARIES));

        $html[] = '</div>';
        $html[] = '</div>';

        // Users

        $html[] = '<div class="panel panel-default">';

        $glyph = new FontAwesomeGlyph('users', array('fa-lg'), null, 'fas');

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">';
        $html[] = $glyph->render() . ' ' . Translation::get('Users', null, \Chamilo\Core\User\Manager::context());
        $html[] = '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';
        $html[] = $table->as_html();
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_course_group_unsubscribe_browser');
    }

    public function getButtonToolbarRenderer()
    {
        $course_group = $this->course_group;
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();

            $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP] = $course_group->get_id();

            $commonActions->addButton(
                new Button(
                    Translation::get('ShowAll', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('folder'),
                    $this->get_url($parameters), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $user = $this->get_parent()->get_user();

            $parameters = [];
            $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP] = $course_group->get_id();

            if (!$this->get_parent()->is_teacher())
            {
                if ($course_group->is_self_registration_allowed() && !$course_group->is_member($user))
                {
                    $parameters = [];
                    $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP] = $course_group->get_id();
                    $parameters[self::PARAM_COURSE_GROUP_ACTION] = self::ACTION_USER_SELF_SUBSCRIBE;
                    $subscribe_url = $this->get_url($parameters);

                    $commonActions->addButton(
                        new Button(
                            Translation::get('SubscribeToGroup'), new FontAwesomeGlyph('plus-circle'), $subscribe_url,
                            ToolbarItem::DISPLAY_ICON_AND_LABEL
                        )
                    );
                }

                if ($course_group->is_self_unregistration_allowed() && $course_group->is_member($user))
                {
                    $parameters = [];
                    $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP] = $course_group->get_id();
                    $parameters[self::PARAM_COURSE_GROUP_ACTION] = self::ACTION_USER_SELF_UNSUBSCRIBE;
                    $unsubscribe_url = $this->get_url($parameters);

                    $commonActions->addButton(
                        new Button(
                            Translation::get('UnSubscribeFromGroup'), new FontAwesomeGlyph('minus-square'),
                            $unsubscribe_url, ToolbarItem::DISPLAY_ICON_AND_LABEL
                        )
                    );
                }
            }
            else
            {
                $parameters = [];
                $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP] = $course_group->get_id();
                $parameters[self::PARAM_COURSE_GROUP_ACTION] = self::ACTION_MANAGE_SUBSCRIPTIONS;
                $subscribe_url = $this->get_url($parameters);

                $commonActions->addButton(
                    new Button(
                        Translation::get('SubscribeUsers'), new FontAwesomeGlyph('plus-circle'), $subscribe_url,
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );

                // export the list to a spreadsheet
                $parameters_export_subscriptions_overview = [];
                $parameters_export_subscriptions_overview[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] =
                    self::ACTION_EXPORT_SUBSCRIPTIONS_OVERVIEW;
                $parameters_export_subscriptions_overview[self::PARAM_COURSE_GROUP] = $course_group->get_id();
                $commonActions->addButton(
                    new Button(
                        Translation::get('Export', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('download'),
                        $this->get_url($parameters_export_subscriptions_overview), ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function getCurrentCourseGroup()
    {
        return $this->course_group;
    }

    public function get_condition()
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        if (isset($query) && $query != '')
        {
            $conditions[] = new ContainsCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME), $query
            );
            $conditions[] = new ContainsCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME), $query
            );
            $conditions[] = new ContainsCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME), $query
            );

            return new OrCondition($conditions);
        }
    }

    public function get_course_group()
    {
        return $this->course_group;
    }

    public function get_table_condition($table_class_name)
    {
        return $this->get_condition();
    }
}
