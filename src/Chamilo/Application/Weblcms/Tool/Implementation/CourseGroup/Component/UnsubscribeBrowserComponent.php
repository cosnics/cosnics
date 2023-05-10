<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\SubscribedUserTableRenderer;
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
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UnsubscribeBrowserComponent extends Manager implements DelegateComponent
{

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    private CourseGroup $course_group;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function run()
    {
        $translator = $this->getTranslator();
        $course_group_id = $this->getRequest()->query->get(self::PARAM_COURSE_GROUP);
        $this->set_parameter(self::PARAM_COURSE_GROUP, $course_group_id);

        $course_group = DataManager::retrieve_by_id(CourseGroup::class, $course_group_id);

        if (!$course_group)
        {
            throw new ObjectNotExistException(
                $translator->trans('CourseGroup', [], Manager::CONTEXT), $course_group_id
            );
        }

        $this->course_group = $course_group;
        BreadcrumbTrail::getInstance()->add(
            new Breadcrumb(
                $this->get_url(),
                $translator->trans('UnsubscribeBrowserComponent', ['GROUPNAME' => $course_group->get_name()],
                    Manager::CONTEXT)
            )
        );

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = '<div style="clear: both;">&nbsp;</div>';

        $users = $this->getRequest()->getFromPostOrUrl(\Chamilo\Application\Weblcms\Manager::PARAM_USERS);

        if ($users)
        {
            if (!is_array($users))
            {
                $users = [$users];
            }

            foreach ($users as $user)
            {
                $course_group->unsubscribe_users($user);

                $userObject = new User();
                $userObject->setId($user);
                $this->getCourseGroupDecoratorsManager()->unsubscribeUser($course_group, $userObject);
            }

            $message =
                $translator->trans(count($users) > 1 ? 'UsersUnsubscribed' : 'UserUnsubscribed', [], Manager::CONTEXT);
            $this->redirectWithMessage(
                $message, false, [
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_UNSUBSCRIBE,
                    self::PARAM_COURSE_GROUP => $course_group_id
                ]
            );
        }

        $html[] = $this->getButtonToolbarRenderer()->render();

        // Details

        $html[] = '<div class="panel panel-default">';

        $glyph = new FontAwesomeGlyph('info-circle', ['fa-lg'], null, 'fas');

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">';
        $html[] = $glyph->render() . ' ' . $course_group->get_name();
        $html[] = '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';
        $html[] = $course_group->get_description();
        $html[] = '<div class="clearfix"></div>';

        $html[] = '<b>' . $translator->trans('NumberOfMembers', [], Manager::CONTEXT) . ':</b> ';
        $html[] = $course_group->count_members();
        $html[] = '<br />';

        $html[] = '<b>' . $translator->trans('MaximumMembers', [], Manager::CONTEXT) . ':</b> ';
        $html[] = $course_group->get_max_number_of_members();
        $html[] = '<br />';

        $html[] = '<b>' . $translator->trans('SelfRegistrationAllowed', [], Manager::CONTEXT) . ':</b> ';
        $html[] = $translator->trans($course_group->is_self_registration_allowed() ? 'ConfirmYes' : 'ConfirmNo', [],
            StringUtilities::LIBRARIES);
        $html[] = '<br />';

        $html[] = '<b>' . $translator->trans('SelfUnRegistrationAllowed', [], Manager::CONTEXT) . ':</b> ';
        $html[] = $translator->trans($course_group->is_self_unregistration_allowed() ? 'ConfirmYes' : 'ConfirmNo', [],
            StringUtilities::LIBRARIES);
        $html[] = '<br />';

        $html[] = '<b>' . $translator->trans('RandomlySubscribed', [], Manager::CONTEXT) . ':</b> ';
        $html[] = $translator->trans($course_group->is_random_registration_done() ? 'ConfirmYes' : 'ConfirmNo', [],
            StringUtilities::LIBRARIES);

        $html[] = '</div>';
        $html[] = '</div>';

        // Users

        $html[] = '<div class="panel panel-default">';

        $glyph = new FontAwesomeGlyph('users', ['fa-lg'], null, 'fas');

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">';
        $html[] = $glyph->render() . ' ' . $translator->trans('Users', [], \Chamilo\Core\User\Manager::CONTEXT);
        $html[] = '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';
        $html[] = $this->renderTable();
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $course_group = $this->course_group;
            $translator = $this->getTranslator();

            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();

            $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP] = $course_group->get_id();

            $commonActions->addButton(
                new Button(
                    $translator->trans('ShowAll', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('folder'),
                    $this->get_url($parameters), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $user = $this->get_application()->get_user();

            if (!$this->get_application()->is_teacher())
            {
                if ($course_group->is_self_registration_allowed() && !$course_group->is_member($user))
                {
                    $parameters = [];
                    $parameters[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE_GROUP] = $course_group->get_id();
                    $parameters[self::PARAM_COURSE_GROUP_ACTION] = self::ACTION_USER_SELF_SUBSCRIBE;
                    $subscribe_url = $this->get_url($parameters);

                    $commonActions->addButton(
                        new Button(
                            $translator->trans('SubscribeToGroup', [], Manager::CONTEXT),
                            new FontAwesomeGlyph('plus-circle'), $subscribe_url, ToolbarItem::DISPLAY_ICON_AND_LABEL
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
                            $translator->trans('UnSubscribeFromGroup', [], Manager::CONTEXT),
                            new FontAwesomeGlyph('minus-square'), $unsubscribe_url, ToolbarItem::DISPLAY_ICON_AND_LABEL
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
                        $translator->trans('SubscribeUsers', [], Manager::CONTEXT), new FontAwesomeGlyph('plus-circle'),
                        $subscribe_url, ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );

                // export the list to a spreadsheet
                $parameters_export_subscriptions_overview = [];
                $parameters_export_subscriptions_overview[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] =
                    self::ACTION_EXPORT_SUBSCRIPTIONS_OVERVIEW;
                $parameters_export_subscriptions_overview[self::PARAM_COURSE_GROUP] = $course_group->get_id();
                $commonActions->addButton(
                    new Button(
                        $translator->trans('Export', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('download'),
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

    public function getCurrentCourseGroup(): CourseGroup
    {
        return $this->course_group;
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \QuickformException
     */
    public function getSubscribedUserCondition(): ?OrCondition
    {
        $query = $this->getButtonToolbarRenderer()->getSearchForm()->getQuery();

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

        return null;
    }

    public function getSubscribedUserTableRenderer(): SubscribedUserTableRenderer
    {
        return $this->getService(SubscribedUserTableRenderer::class);
    }

    public function get_course_group(): CourseGroup
    {
        return $this->course_group;
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = DataManager::count_course_group_users(
            $this->getCurrentCourseGroup()->get_id(), $this->getSubscribedUserCondition()
        );
        $adminUserTableRenderer = $this->getSubscribedUserTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $adminUserTableRenderer->getParameterNames(), $adminUserTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $users = DataManager::retrieve_course_group_users_with_subscription_time(
            $this->getCurrentCourseGroup()->get_id(), $this->getSubscribedUserCondition(),
            $tableParameterValues->getOffset(), $tableParameterValues->getNumberOfItemsPerPage(),
            $adminUserTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $adminUserTableRenderer->legacyRender($this, $tableParameterValues, $users);
    }
}
