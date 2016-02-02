<?php
namespace Chamilo\Application\Weblcms\Admin\Entity\Helper;

use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Application\Weblcms\Admin\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Application\Weblcms\Admin\Storage\DataClass\Admin;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Application\Weblcms\Admin\Entity\UserEntity;
use Chamilo\Application\Weblcms\Admin\Entity\PlatformGroupEntity;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;

class UserEntityHelper
{

    public static function get_table_columns()
    {
        $columns = array();
        $columns[] = new DataClassPropertyTableColumn(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
            \Chamilo\Core\User\Storage\DataClass\User :: PROPERTY_LASTNAME);
        $columns[] = new DataClassPropertyTableColumn(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
            \Chamilo\Core\User\Storage\DataClass\User :: PROPERTY_FIRSTNAME);
        $columns[] = new DataClassPropertyTableColumn(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
            \Chamilo\Core\User\Storage\DataClass\User :: PROPERTY_EMAIL);
        return $columns;
    }

    public static function render_table_cell($renderer, $column, $result)
    {
        switch ($column->get_name())
        {
            case \Chamilo\Core\User\Storage\DataClass\User :: PROPERTY_FIRSTNAME :
                $url = self :: get_target_url($renderer, $result);
                return '<a href="' . $url . '">' .
                     $result[\Chamilo\Core\User\Storage\DataClass\User :: PROPERTY_FIRSTNAME] . '</a>';
                break;
            case \Chamilo\Core\User\Storage\DataClass\User :: PROPERTY_LASTNAME :
                $url = self :: get_target_url($renderer, $result);
                return '<a href="' . $url . '">' .
                     $result[\Chamilo\Core\User\Storage\DataClass\User :: PROPERTY_LASTNAME] . '</a>';
                break;
            default :
                return null;
        }

        return null;
    }

    public static function get_target_url($renderer, $result)
    {
        return $renderer->get_component()->get_url(
            array(
                Manager :: PARAM_ACTION => Manager :: ACTION_TARGET,
                Manager :: PARAM_ENTITY_TYPE => $renderer->get_component()->get_selected_entity_type(),
                Manager :: PARAM_ENTITY_ID => $result[DataClass :: PROPERTY_ID]));
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
     * @return \libraries\ResultSet
     */
    public static function retrieve_table_data($condition, $count, $offset, $order_property)
    {
        $join = new Join(
            Admin :: class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                    \Chamilo\Core\User\Storage\DataClass\User :: PROPERTY_ID),
                new PropertyConditionVariable(Admin :: class_name(), Admin :: PROPERTY_ENTITY_ID)));
        $joins = new Joins(array($join));

        $properties = new DataClassProperties();
        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable :: DISTINCT,
                new PropertiesConditionVariable(\Chamilo\Core\User\Storage\DataClass\User :: class_name())));

        $parameters = new RecordRetrievesParameters($properties, $condition, $count, $offset, $order_property, $joins);

        return \Chamilo\Core\User\Storage\DataManager :: records(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
            $parameters);
    }

    /**
     * Counts the data
     *
     * @param \libraries\Condition $condition
     *
     * @return int
     */
    public static function count_table_data($condition)
    {
        $join = new Join(
            Admin :: class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                    \Chamilo\Core\User\Storage\DataClass\User :: PROPERTY_ID),
                new PropertyConditionVariable(Admin :: class_name(), Admin :: PROPERTY_ENTITY_ID)));
        $joins = new Joins(array($join));

        $parameters = new DataClassCountParameters(
            $condition,
            $joins,
            new FunctionConditionVariable(
                FunctionConditionVariable :: DISTINCT,
                new PropertyConditionVariable(
                    \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                    \Chamilo\Core\User\Storage\DataClass\User :: PROPERTY_ID)));

        return \Chamilo\Core\User\Storage\DataManager :: count(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
            $parameters);
    }

    public static function expand($entity_id)
    {
        $entities = array();

        $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
            $entity_id);

        if ($user instanceof \Chamilo\Core\User\Storage\DataClass\User)
        {
            $entities[UserEntity :: ENTITY_TYPE][] = $user->get_id();

            $group_ids = $user->get_groups(true);

            foreach ($group_ids as $group_id)
            {
                $entities[PlatformGroupEntity :: ENTITY_TYPE][] = $group_id;
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
