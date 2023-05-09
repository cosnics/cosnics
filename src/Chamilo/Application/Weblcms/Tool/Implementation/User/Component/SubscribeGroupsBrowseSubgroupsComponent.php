<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\User\Table\UnsubscribedGroupTableRenderer;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\DataClass\NestedSet;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package application.lib.weblcms.tool.user.component
 */
class SubscribeGroupsBrowseSubgroupsComponent extends SubscribeGroupsTabComponent
{

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \QuickformException
     */
    public function getUnsubscribedGroupCondition(): AndCondition
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Group::class, NestedSet::PROPERTY_PARENT_ID),
            new StaticConditionVariable($this->getGroupId())
        );

        $query = $this->tabButtonToolbarRenderer->getSearchForm()->getQuery();
        if (isset($query) && $query != '')
        {
            $conditions2[] = new ContainsCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME), $query
            );
            $conditions2[] = new ContainsCondition(
                new PropertyConditionVariable(Group::class, Group::PROPERTY_DESCRIPTION), $query
            );
            $conditions[] = new OrCondition($conditions2);
        }

        return new AndCondition($conditions);
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
     */
    protected function renderTabContent(): string
    {
        $html = [];
        $html[] = $this->tabButtonToolbarRenderer->render();

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

        $html[] = $unsubscribedGroupTableRenderer->render($tableParameterValues, $groups);

        return implode(PHP_EOL, $html);
    }
}
