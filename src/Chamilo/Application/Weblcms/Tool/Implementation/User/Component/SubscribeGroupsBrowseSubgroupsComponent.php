<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\User\Component\UnsubscribedGroup\UnsubscribedGroupTable;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.lib.weblcms.tool.user.component
 */
class SubscribeGroupsBrowseSubgroupsComponent extends SubscribeGroupsTabComponent implements TableSupport
{

    /**
     * Returns the condition for the table
     *
     * @param string $table_class_name
     *
     * @return Condition
     */
    public function get_table_condition($table_class_name)
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
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

    /**
     * Renders the content for the tab
     *
     * @return string
     */
    protected function renderTabContent()
    {
        $table = new UnsubscribedGroupTable($this);
        $table->setSearchForm($this->tabButtonToolbarRenderer->getSearchForm());

        $html = [];
        $html[] = $this->tabButtonToolbarRenderer->render();
        $html[] = $table->as_html();

        return implode(PHP_EOL, $html);
    }
}
