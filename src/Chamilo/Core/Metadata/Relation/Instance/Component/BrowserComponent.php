<?php
namespace Chamilo\Core\Metadata\Relation\Instance\Component;

use Chamilo\Core\Metadata\Relation\Instance\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Core\Metadata\Relation\Instance\Table\Relation\RelationTable;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Core\Metadata\Relation\Instance\Storage\DataClass\RelationInstance;

/**
 *
 * @package Chamilo\Core\Metadata\Relation\Instance\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager implements TableSupport
{

    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $this->verifySetup();

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders this components output as html
     */
    public function as_html()
    {
        $html = array();

        $this->action_bar = $this->get_action_bar();
        $html[] = $this->action_bar->as_html();

        $table = new RelationTable($this);
        $html[] = $table->as_html();

        return implode(PHP_EOL, $html);
    }

    /**
     * Builds the action bar
     *
     * @return ActionBarRenderer
     */
    protected function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());

        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Create'),
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE))));

        return $action_bar;
    }

    /**
     * Returns the condition
     *
     * @param string $table_class_name
     *
     * @return Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function get_table_condition($table_class_name)
    {
        $conditions = array();

        $relations = $this->getRelations();

        if (count($relations) > 0)
        {
            $relationIdentifiers = array();

            foreach ($relations as $relation)
            {
                $relationIdentifiers[] = $relation->get_id();
            }

            $conditions[] = new InCondition(
                new PropertyConditionVariable(RelationInstance :: class_name(), RelationInstance :: PROPERTY_RELATION_ID),
                $relationIdentifiers);
        }

        $sourceEntities = $this->getSourceEntities();

        if (count($sourceEntities) > 0)
        {
            $sourceConditions = array();

            foreach ($sourceEntities as $sourceEntity)
            {
                $sourceConditions[] = new AndCondition(
                    array(
                        new EqualityCondition(
                            new PropertyConditionVariable(
                                RelationInstance :: class_name(),
                                RelationInstance :: PROPERTY_SOURCE_TYPE),
                            new StaticConditionVariable($sourceEntity->class_name())),
                        new EqualityCondition(
                            new PropertyConditionVariable(
                                RelationInstance :: class_name(),
                                RelationInstance :: PROPERTY_SOURCE_ID),
                            new StaticConditionVariable($sourceEntity->get_id()))));
            }

            $conditions[] = new OrCondition($sourceConditions);
        }

        $targetEntities = $this->getTargetEntities();

        if (count($targetEntities) > 0)
        {
            $targetConditions = array();

            foreach ($targetEntities as $targetEntity)
            {
                $targetConditions[] = new AndCondition(
                    array(
                        new EqualityCondition(
                            new PropertyConditionVariable(
                                RelationInstance :: class_name(),
                                RelationInstance :: PROPERTY_TARGET_TYPE),
                            new StaticConditionVariable($targetEntity->class_name())),
                        new EqualityCondition(
                            new PropertyConditionVariable(
                                RelationInstance :: class_name(),
                                RelationInstance :: PROPERTY_TARGET_ID),
                            new StaticConditionVariable($targetEntity->get_id()))));
            }

            $conditions[] = new OrCondition($targetConditions);
        }

        return new AndCondition($conditions);
    }
}
