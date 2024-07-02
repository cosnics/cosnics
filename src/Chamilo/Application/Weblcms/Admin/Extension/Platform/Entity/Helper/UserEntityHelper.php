<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\Helper;

use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\PlatformGroupEntity;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\UserEntity;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Manager;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataClass\Admin;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\RetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;

class UserEntityHelper
{

    /**
     * Get the fully qualified class name of the object
     *
     * @return string
     */
    public static function class_name()
    {
        return get_called_class();
    }

    /**
     * Counts the data
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public static function count_table_data($condition)
    {
        $join = new Join(
            Admin::class, new EqualityCondition(
                new PropertyConditionVariable(
                    User::class, User::PROPERTY_ID
                ), new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ENTITY_ID)
            )
        );
        $joins = new Joins([$join]);

        $parameters = new DataClassCountParameters(
            condition: $condition, joins: $joins, retrieveProperties: new RetrieveProperties(
            [
                new FunctionConditionVariable(
                    FunctionConditionVariable::DISTINCT, new PropertyConditionVariable(
                        User::class, User::PROPERTY_ID
                    )
                )
            ]
        )
        );

        return DataManager::count(
            User::class, $parameters
        );
    }

    public static function expand($entity_id)
    {
        $entities = [];

        $user = DataManager::retrieve_by_id(
            User::class, $entity_id
        );

        if ($user instanceof User)
        {
            $entities[UserEntity::ENTITY_TYPE][] = $user->get_id();

            $group_ids = $user->get_groups(true);

            foreach ($group_ids as $group_id)
            {
                $entities[PlatformGroupEntity::ENTITY_TYPE][] = $group_id;
            }
        }

        return $entities;
    }

    public static function get_table_columns()
    {
        $translator = Translation::getInstance();

        $columns = [];
        $columns[] = new DataClassPropertyTableColumn(
            User::class, User::PROPERTY_LASTNAME,
            $translator->getTranslation('LastName', [], \Chamilo\Core\User\Manager::CONTEXT)
        );
        $columns[] = new DataClassPropertyTableColumn(
            User::class, User::PROPERTY_FIRSTNAME,
            $translator->getTranslation('FirstName', [], \Chamilo\Core\User\Manager::CONTEXT)
        );
        $columns[] = new DataClassPropertyTableColumn(
            User::class, User::PROPERTY_EMAIL,
            $translator->getTranslation('Email', [], \Chamilo\Core\User\Manager::CONTEXT)
        );

        return $columns;
    }

    public static function get_target_url($renderer, $result)
    {
        return $renderer->getUrlGenerator()->fromRequest(
            [
                Manager::PARAM_ACTION => Manager::ACTION_TARGET,
                Manager::PARAM_ENTITY_TYPE => $renderer->get_component()->get_selected_entity_type(),
                Manager::PARAM_ENTITY_ID => $result[DataClass::PROPERTY_ID]
            ]
        );
    }

    public static function render_table_cell($renderer, $column, $result)
    {
        switch ($column->get_name())
        {
            case User::PROPERTY_FIRSTNAME :
                $url = self::get_target_url($renderer, $result);

                return '<a href="' . $url . '">' . $result[User::PROPERTY_FIRSTNAME] . '</a>';
                break;
            case User::PROPERTY_LASTNAME :
                $url = self::get_target_url($renderer, $result);

                return '<a href="' . $url . '">' . $result[User::PROPERTY_LASTNAME] . '</a>';
                break;
            default :
                return null;
        }

        return null;
    }

    /**
     * Returns the data as a resultset
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param $condition
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $order_property
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public static function retrieve_table_data($condition, $count, $offset, $order_property)
    {
        $join = new Join(
            Admin::class, new EqualityCondition(
                new PropertyConditionVariable(
                    User::class, User::PROPERTY_ID
                ), new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ENTITY_ID)
            )
        );
        $joins = new Joins([$join]);

        $properties = new RetrieveProperties();
        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::DISTINCT, new PropertiesConditionVariable(User::class)
            )
        );

        $parameters = new RetrievesParameters(
            condition: $condition, count: $count, offset: $offset, orderBy: $order_property, joins: $joins,
            retrieveProperties: $properties
        );

        return DataManager::records(
            User::class, $parameters
        );
    }
}
