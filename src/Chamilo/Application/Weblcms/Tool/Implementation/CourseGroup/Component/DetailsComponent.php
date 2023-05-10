<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\SubscribedUserTableRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
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
class DetailsComponent extends TabComponent
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function getGroupButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        $translator = $this->getTranslator();

        $courseGroup = $this->getCurrentCourseGroup();

        $buttonToolbar = new ButtonToolBar();
        $managementButtonGroup = new ButtonGroup();

        if ($courseGroup->is_self_registration_allowed() && !$courseGroup->is_member($this->getUser()))
        {
            $buttonToolbar->addItem(
                new Button(
                    $translator->trans('SubscribeToGroup', [], Manager::CONTEXT), null,
                    $this->get_url([self::PARAM_ACTION => self::ACTION_USER_SELF_SUBSCRIBE]),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL, null, ['btn-success']
                )
            );
        }

        if ($courseGroup->is_self_unregistration_allowed() && $courseGroup->is_member($this->getUser()))
        {
            $buttonToolbar->addItem(
                new Button(
                    $translator->trans('UnSubscribeFromGroup', [], Manager::CONTEXT), null,
                    $this->get_url([self::PARAM_ACTION => self::ACTION_USER_SELF_UNSUBSCRIBE]),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL, null, ['btn-danger']
                )
            );
        }

        if ($this->is_allowed(WeblcmsRights::DELETE_RIGHT))
        {
            $managementButtonGroup->addButton(
                new Button(
                    $translator->trans('Export', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('download'),
                    $this->get_url([self::PARAM_ACTION => self::ACTION_EXPORT_SUBSCRIPTIONS_OVERVIEW]),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $managementButtonGroup->addButton(
                new Button(
                    $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $this->get_url([self::PARAM_ACTION => self::ACTION_DELETE_COURSE_GROUP]),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL, $translator->trans(
                    'DeleteConfirm', ['NAME' => $courseGroup->get_name()], Manager::CONTEXT
                )
                )
            );
        }

        $buttonToolbar->addButtonGroup($managementButtonGroup);

        if ($courseGroup->is_member($this->getUser()) || $this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $navigateToOptions = new DropdownButton(
                $translator->trans('NavigateTo', [], Manager::CONTEXT)
            );

            if ($navigateToOptions->hasButtons())
            {
                $buttonToolbar->addItem($navigateToOptions);
            }
        }

        return new ButtonToolBarRenderer($buttonToolbar);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \QuickformException
     */
    public function getSubscribedUserCondition(): ?OrCondition
    {
        $query = $this->getButtonToolbarRenderer()->getSearchForm()->getQuery();

        if (isset($query) && $query != '')
        {
            $conditions[] =
                new ContainsCondition(new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME), $query);

            $conditions[] =
                new ContainsCondition(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME), $query);

            $conditions[] =
                new ContainsCondition(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME), $query);

            return new OrCondition($conditions);
        }

        return null;
    }

    public function getSubscribedUserTableRenderer(): SubscribedUserTableRenderer
    {
        return $this->getService(SubscribedUserTableRenderer::class);
    }

    protected function handleUnsubscribeAction(CourseGroup $courseGroup)
    {
        $users = $this->getRequest()->getFromPostOrUrl(\Chamilo\Application\Weblcms\Manager::PARAM_USERS);

        if ($users)
        {
            if (!is_array($users))
            {
                $users = [$users];
            }

            foreach ($users as $user)
            {
                $courseGroup->unsubscribe_users($user);

                $userObject = new User();
                $userObject->setId($user);
                $this->getCourseGroupDecoratorsManager()->unsubscribeUser($courseGroup, $userObject);
            }

            $message = $this->getTranslator()->trans(count($users) > 1 ? 'UsersUnsubscribed' : 'UserUnsubscribed', [],
                Manager::CONTEXT);
            $this->redirectWithMessage(
                $message, false, [
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_GROUP_DETAILS,
                    self::PARAM_COURSE_GROUP => $courseGroup->get_id()
                ]
            );
        }
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \QuickformException
     */
    protected function renderDetails(CourseGroup $currentCourseGroup): string
    {
        $translator = $this->getTranslator();

        $html = [];

        $html[] = '<div class="container-fluid">';

        $html[] = '<div class="row">';
        $html[] = '<div class="col-sm-12">';
        $html[] = $currentCourseGroup->get_description();
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="row">';
        $html[] = '<div class="col-sm-12">';

        $html[] = '<b>' . $translator->trans('NumberOfMembers', [], Manager::CONTEXT) . ':</b> ';
        $html[] = $currentCourseGroup->count_members();
        $html[] = '<br />';

        $html[] = '<b>' . $translator->trans('MaximumMembers', [], Manager::CONTEXT) . ':</b> ';
        $html[] = $currentCourseGroup->get_max_number_of_members();
        $html[] = '<br />';

        $html[] = '<b>' . $translator->trans('SelfRegistrationAllowed', [], Manager::CONTEXT) . ':</b> ';
        $html[] =
            $translator->trans($currentCourseGroup->is_self_registration_allowed() ? 'ConfirmYes' : 'ConfirmNo', [],
                StringUtilities::LIBRARIES);
        $html[] = '<br />';

        $html[] = '<b>' . $translator->trans('SelfUnRegistrationAllowed', [], Manager::CONTEXT) . ':</b> ';
        $html[] =
            $translator->trans($currentCourseGroup->is_self_unregistration_allowed() ? 'ConfirmYes' : 'ConfirmNo', [],
                StringUtilities::LIBRARIES);
        $html[] = '<br />';

        $html[] = '<b>' . $translator->trans('RandomlySubscribed', [], Manager::CONTEXT) . ':</b> ';
        $html[] =
            $translator->trans($currentCourseGroup->is_random_registration_done() ? 'ConfirmYes' : 'ConfirmNo', [],
                StringUtilities::LIBRARIES);

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '<div class="row">';
        $html[] = '<div class="col-sm-12">';
        $html[] = '<div style="margin-top: 20px;">';
        $html[] = $this->getGroupButtonToolbarRenderer()->render();
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \QuickformException
     */
    protected function renderIntegrations(CourseGroup $courseGroup): string
    {
        $html = [];

        $integrationLinksButtonToolbar = new ButtonToolBar();
        $renderer = new ButtonToolBarRenderer($integrationLinksButtonToolbar);

        $this->getCourseGroupDecoratorsManager()->addCourseGroupActions(
            $integrationLinksButtonToolbar, $courseGroup, $this->getUser(), $this->is_allowed(WeblcmsRights::EDIT_RIGHT)
        );

        if ($integrationLinksButtonToolbar->hasItems())
        {
            $html[] = '<div class="tab-content-header">';
            $html[] = '<h5>' . $this->getTranslator()->trans('Integrations', [], Manager::CONTEXT) . '</h5>';
            $html[] = '</div>';
            $html[] = $renderer->render();
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderTabContent(): string
    {
        if ($this->isCurrentGroupRoot())
        {
            return '';
        }

        $currentCourseGroup = $this->getCurrentCourseGroup();
        $this->handleUnsubscribeAction($currentCourseGroup);

        $html = [];

        $html[] = $this->renderDetails($currentCourseGroup);
        $html[] = $this->renderIntegrations($currentCourseGroup);
        $html[] = $this->renderUsersTable();

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
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

        return $adminUserTableRenderer->render($tableParameterValues, $users);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    protected function renderUsersTable(): string
    {
        $courseGroup = $this->getCurrentCourseGroup();

        if (!$courseGroup->is_member($this->getUser()) && !$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            return '';
        }

        $html = [];

        $html[] = '<div class="tab-content-header">';
        $html[] = '<h5>' . $this->getTranslator()->trans('Users', [], Manager::CONTEXT) . '</h5>';
        $html[] = '</div>';
        $html[] = $this->renderTable();

        return implode(PHP_EOL, $html);
    }
}
