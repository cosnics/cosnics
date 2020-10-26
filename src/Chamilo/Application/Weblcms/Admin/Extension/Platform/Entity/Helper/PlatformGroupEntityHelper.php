<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\Helper;

use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\PlatformGroupEntity;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Manager;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataClass\Admin;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

class PlatformGroupEntityHelper
{
    const PROPERTY_PATH = 'path';

    public static function get_table_columns()
    {
        $columns = array();
        $columns[] = new DataClassPropertyTableColumn(
            Group::class,
            Group::PROPERTY_NAME);
        $columns[] = new StaticTableColumn(self::PROPERTY_PATH);
        $columns[] = new DataClassPropertyTableColumn(
            Group::class,
            Group::PROPERTY_CODE);
        return $columns;
    }

    /**
     *
     * @param EntityTableCellRenderer $renderer
     * @param NewTableColumn $column
     * @param string[] $result
     * @return NULL
     */
    public static function render_table_cell($renderer, $column, $result)
    {
        switch ($column->get_name())
        {
            case Group::PROPERTY_NAME :
                $url = $renderer->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_TARGET,
                        Manager::PARAM_ENTITY_TYPE => $renderer->get_component()->get_selected_entity_type(),
                        Manager::PARAM_ENTITY_ID => $result[DataClass::PROPERTY_ID]));
                return '<a href="' . $url . '">' . $result[Group::PROPERTY_NAME] .
                     '</a>';
                break;
            case self::PROPERTY_PATH :
                $group = DataManager::retrieve_by_id(
                    Group::class,
                    $result[Group::PROPERTY_ID]);
                return $group->get_fully_qualified_name();
                break;
            default :
                return null;
        }
    }

    /**
     * Returns the data as a resultset
     *
     * @param \libraries\Condition $condition
     * @param $condition
     * @param int $offset
     * @param int $count
     * @param ObjectTableOrder[] $order_property
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public static function retrieve_table_data($condition, $count, $offset, $order_property)
    {
        $join = new Join(
            Admin::class,
            new EqualityCondition(
                new PropertyConditionVariable(
                    Group::class,
                    Group::PROPERTY_ID),
                new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ENTITY_ID)));
        $joins = new Joins(array($join));

        $properties = new DataClassProperties();
        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::DISTINCT,
                new PropertiesConditionVariable(Group::class)));

        $parameters = new RecordRetrievesParameters($properties, $condition, $count, $offset, $order_property, $joins);

        return DataManager::records(
            Group::class,
            $parameters);
    }

    /**
     * Counts the data
     *
     * @param \libraries\Condition $condition
     *
     * @return int
     */
    public function count_table_data($condition)
    {
        $join = new Join(
            Admin::class,
            new EqualityCondition(
                new PropertyConditionVariable(
                    Group::class,
                    Group::PROPERTY_ID),
                new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ENTITY_ID)));
        $joins = new Joins(array($join));

        $parameters = new DataClassCountParameters(
            $condition,
            $joins,
            new DataClassProperties(
                array(
                    new FunctionConditionVariable(
                        FunctionConditionVariable::DISTINCT,
                        new PropertyConditionVariable(
                            Group::class,
                            Group::PROPERTY_ID)))));

        return DataManager::count(
            Group::class,
            $parameters);
    }

    public static function expand($entity_id)
    {
        $entities = array();

        $group = DataManager::retrieve_by_id(
            Group::class,
            $entity_id);

        if ($group instanceof Group)
        {
            $parents = $group->get_parents();

            foreach($parents as $parent)
            {
                $entities[PlatformGroupEntity::ENTITY_TYPE][] = $parent;
            }
        }

        return $entities;
    }

    /**
     * Get the fully qualified class name of the object
     *
     * @return string
     */
    public static function class_name()
    {
        return get_called_class();
    }
}
