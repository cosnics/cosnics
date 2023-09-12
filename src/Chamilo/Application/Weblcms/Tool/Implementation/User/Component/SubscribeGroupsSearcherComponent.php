<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Table\UnsubscribedGroupTableRenderer;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * With this component the users can search through all the platform groups in a flast list without hierachy.
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SubscribeGroupsSearcherComponent extends Manager
{
    protected ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function run()
    {
        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->getButtonToolbarRenderer()->render();
        $html[] = $this->renderTable();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([self::PARAM_ACTION => self::ACTION_SUBSCRIBE_GROUP_DETAILS]),
                $this->getTranslator()->trans('SubscribeGroupsDetailsComponent', [], Manager::CONTEXT)
            )
        );
    }

    protected function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \Exception
     */
    public function getUnsubscribedGroupCondition(): ?AndCondition
    {
        $properties = [
            new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME),
            new PropertyConditionVariable(Group::class, Group::PROPERTY_DESCRIPTION)
        ];

        return $this->getButtonToolbarRenderer()->getConditions($properties);
    }

    public function getUnsubscribedGroupTableRenderer(): UnsubscribedGroupTableRenderer
    {
        return $this->getService(UnsubscribedGroupTableRenderer::class);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Exception
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = $this->getGroupService()->countGroups($this->getUnsubscribedGroupCondition());
        $unsubscribedGroupTableRenderer = $this->getUnsubscribedGroupTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $unsubscribedGroupTableRenderer->getParameterNames(),
            $unsubscribedGroupTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $groups = $this->getGroupService()->findGroups(
            $this->getUnsubscribedGroupCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $unsubscribedGroupTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $unsubscribedGroupTableRenderer->legacyRender($this, $tableParameterValues, $groups);
    }
}