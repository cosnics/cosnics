<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\UserInterface\Table\NonSubscribedUserTableRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\Breadcrumb;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonGroup;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Interface\ButtonDisplayInterface;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Chamilo\Libraries\UserInterface\Table\Service\RequestTableParameterValuesCompiler;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Core\Group\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BrowseNonSubscribedUsersComponent extends Manager
{
    private ?Group $group;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exception\ClassNotExistException
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \TableException
     */
    public function run(): Response
    {
        if (!$this->getUser()->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $this->getBreadcrumbTrail()->add(
            new Breadcrumb(
                $this->getUrlGenerator()->fromParameters(
                    [
                        self::PARAM_CONTEXT => Manager::CONTEXT,
                        self::PARAM_ACTION => self::ACTION_VIEW,
                        self::PARAM_GROUP_ID => $this->getGroupIdentifier()
                    ]
                ), $this->getTranslator()->trans('ViewerComponent', [], Manager::CONTEXT)
            )
        );

        $output = $this->get_user_subscribe_html();

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->getButtonToolBarRenderer()->render($this->getButtonToolBar()) . '<br />';
        $html[] = $output;
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function getButtonToolBar(): ButtonToolBar
    {
        $group = $this->getGroup();

        $buttonToolBar = new ButtonToolBar(
            $this->getUrlGenerator()->fromParameters(
                [
                    self::PARAM_CONTEXT => Manager::CONTEXT,
                    self::PARAM_ACTION => self::ACTION_BROWSE_NON_SUBSCRIBED_USERS,
                    self::PARAM_GROUP_ID => $group->getId()
                ]
            )
        );
        $commonActions = new ButtonGroup();

        $commonActions->addButton(
            new Button(
                $this->getTranslator()->trans('ShowAll', [], StringUtilities::LIBRARIES),
                new FontAwesomeGlyph('folder'), $this->getUrlGenerator()->fromParameters(
                [
                    self::PARAM_CONTEXT => Manager::CONTEXT,
                    self::PARAM_ACTION => self::ACTION_BROWSE_NON_SUBSCRIBED_USERS,
                    self::PARAM_GROUP_ID => $group->getId()
                ]
            ), ButtonDisplayInterface::DISPLAY_ICON_AND_LABEL
            )
        );

        $buttonToolBar->addButton($commonActions);

        return $buttonToolBar;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    protected function getGroup(): Group
    {
        if (!isset($this->group)) {
            $this->group = $this->getGroupService()->findGroupByIdentifier($this->getGroupIdentifier());
        }

        return $this->group;
    }

    protected function getGroupIdentifier(): string
    {
        return $this->getRequest()->query->get(self::PARAM_GROUP_ID);
    }

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
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

        $query = $this->getButtonToolBarRenderer()->getSearchForm()->getQuery();

        if (isset($query) && $query != '') {
            $or_conditions[] = new ContainsCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_GIVEN_NAME), $query
            );
            $or_conditions[] = new ContainsCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_SURNAME), $query
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
     * @throws \TableException
     * @throws \Chamilo\Libraries\UserInterface\Table\Architecture\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
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