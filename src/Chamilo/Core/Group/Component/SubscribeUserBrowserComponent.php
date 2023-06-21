<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Table\NonSubscribedUserTableRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Group\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SubscribeUserBrowserComponent extends Manager
{
    private ButtonToolBarRenderer $buttonToolbarRenderer;

    private ?Group $group;

    /**
     * Runs this component and displays its output.
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function run()
    {
        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        $output = $this->get_user_subscribe_html();

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->buttonToolbarRenderer->render() . '<br />';
        $html[] = $output;
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $translator = $this->getTranslator();

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS]),
                $translator->trans('BrowserComponent', [], Manager::CONTEXT)
            )
        );
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    [
                        Application::PARAM_ACTION => self::ACTION_VIEW_GROUP,
                        self::PARAM_GROUP_ID => $this->getGroupIdentifier()
                    ]
                ), $translator->trans('ViewerComponent', [], Manager::CONTEXT)
            )
        );
    }

    public function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        $group = $this->getGroup();

        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url([self::PARAM_GROUP_ID => $group->getId()]));
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    $this->getTranslator()->trans('ShowAll', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('folder'), $this->get_url([self::PARAM_GROUP_ID => $group->getId()]),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    protected function getGroup(): Group
    {
        if (!isset($this->group))
        {
            $this->group = $this->getGroupService()->findGroupByIdentifier($this->getGroupIdentifier());
        }

        return $this->group;
    }

    protected function getGroupIdentifier(): int
    {
        return $this->getRequest()->query->get(self::PARAM_GROUP_ID);
    }

    protected function getGroupMembershipService(): GroupMembershipService
    {
        return $this->getService(GroupMembershipService::class);
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function getNonSubscribedUserCondition(): AndCondition
    {
        $conditions = [];

        $userIdentifiers = $this->getGroupMembershipService()->findSubscribedUserIdentifiersForGroupIdentifier(
            $this->getRequest()->query->get(Manager::PARAM_GROUP_ID)
        );

        $conditions[] = new NotCondition(
            new InCondition(new PropertyConditionVariable(User::class, DataClass::PROPERTY_ID), $userIdentifiers)
        );

        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        if (isset($query) && $query != '')
        {
            $or_conditions[] = new ContainsCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME), $query
            );
            $or_conditions[] = new ContainsCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME), $query
            );
            $or_conditions[] = new ContainsCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_USERNAME), $query
            );
            $conditions[] = new OrCondition($or_conditions);
        }

        return new AndCondition($conditions);
    }

    public function getNonSubscribedUserTableRenderer(): NonSubscribedUserTableRenderer
    {
        return $this->getService(NonSubscribedUserTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function get_user_subscribe_html(): string
    {
        $totalNumberOfItems = $this->getUserService()->countUsers($this->getNonSubscribedUserCondition());
        $nonSubscribedUserTableRenderer = $this->getNonSubscribedUserTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $nonSubscribedUserTableRenderer->getParameterNames(),
            $nonSubscribedUserTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $users = $this->getUserService()->findUsers(
            $this->getNonSubscribedUserCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $nonSubscribedUserTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $nonSubscribedUserTableRenderer->render($tableParameterValues, $users);
    }
}