<?php
namespace Chamilo\Core\Repository\Instance\Storage;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Exception\DataClassNoResultException;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package core\repository\instance
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'repository_instance_';
    const ACTION_COUNT = 1;
    const ACTION_RETRIEVES = 2;

    public static function retrieve_setting_from_variable_name($variable, $external_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting :: class_name(), Setting :: PROPERTY_EXTERNAL_ID), 
            new StaticConditionVariable($external_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting :: class_name(), Setting :: PROPERTY_VARIABLE), 
            new StaticConditionVariable($variable));
        $condition = new AndCondition($conditions);
        return self :: retrieve(Setting :: class_name(), $condition);
    }

    public static function retrieve_synchronization_data($condition)
    {
        $join = new Join(
            ContentObject :: class_name(), 
            new EqualityCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID), 
                new PropertyConditionVariable(
                    SynchronizationData :: class_name(), 
                    SynchronizationData :: PROPERTY_CONTENT_OBJECT_ID)));
        
        $parameters = new DataClassRetrieveParameters($condition);
        $parameters->set_joins(new Joins(array($join)));
        
        return self :: retrieve(SynchronizationData :: class_name(), $parameters);
    }

    public static function retrieve_synchronization_data_set($condition = null, $count = null, $offset = null, $order_by = array())
    {
        $join = new Join(
            ContentObject :: class_name(), 
            new EqualityCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID), 
                new PropertyConditionVariable(
                    SynchronizationData :: class_name(), 
                    SynchronizationData :: PROPERTY_CONTENT_OBJECT_ID)));
        
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_by, new Joins(array($join)));
        
        return self :: retrieves(SynchronizationData :: class_name(), $parameters);
    }

    public static function retrieve_active_instances($types = array())
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance :: class_name(), Instance :: PROPERTY_ENABLED), 
            new StaticConditionVariable(1));
        
        foreach ($types as $type)
        {
            $namespaces[] = \Chamilo\Core\Repository\Manager :: context() . '\implementation\\' .
                 ClassnameUtilities :: getInstance()->getPackageNameFromNamespace($type);
        }
        
        if (count($namespaces) > 0)
        {
            $conditions[] = new InCondition(
                new PropertyConditionVariable(Instance :: class_name(), Instance :: PROPERTY_IMPLEMENTATION), 
                $namespaces);
        }
        
        $condition = new AndCondition($conditions);
        
        return self :: retrieves(Instance :: class_name(), $condition);
    }

    public static function deactivate_instance_objects($external_instance_id, $user_id, $external_user_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                SynchronizationData :: class_name(), 
                SynchronizationData :: PROPERTY_EXTERNAL_ID), 
            new StaticConditionVariable($external_instance_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                SynchronizationData :: class_name(), 
                SynchronizationData :: PROPERTY_EXTERNAL_USER_ID), 
            new StaticConditionVariable($external_user_id));
        
        $name = new PropertyConditionVariable(
            SynchronizationData :: class_name(), 
            SynchronizationData :: PROPERTY_CONTENT_OBJECT_ID);
        $value = new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID);
        $sub_select_condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID), 
            new StaticConditionVariable($user_id));
        $conditions[] = new SubselectCondition($name, $value, null, $sub_select_condition);
        $condition = new AndCondition($conditions);
        
        $properties = array();
        $properties[new PropertyConditionVariable(
            SynchronizationData :: class_name(), 
            SynchronizationData :: PROPERTY_STATE)] = new StaticConditionVariable(SynchronizationData :: STATE_INACTIVE);
        
        return self :: updates(SynchronizationData :: class_name(), $properties, $condition);
    }

    public static function activate_instance_objects($external_instance_id, $user_id, $external_user_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                SynchronizationData :: class_name(), 
                SynchronizationData :: PROPERTY_EXTERNAL_ID), 
            new StaticConditionVariable($external_instance_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                SynchronizationData :: class_name(), 
                SynchronizationData :: PROPERTY_EXTERNAL_USER_ID), 
            new StaticConditionVariable($external_user_id));
        
        $name = new PropertyConditionVariable(
            SynchronizationData :: class_name(), 
            SynchronizationData :: PROPERTY_CONTENT_OBJECT_ID);
        $value = new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID);
        $sub_select_condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID), 
            new StaticConditionVariable($user_id));
        $conditions[] = new SubselectCondition($name, $value, null, $sub_select_condition);
        $condition = new AndCondition($conditions);
        
        $properties = array();
        $properties[new PropertyConditionVariable(
            SynchronizationData :: class_name(), 
            SynchronizationData :: PROPERTY_STATE)] = new StaticConditionVariable(SynchronizationData :: STATE_ACTIVE);
        
        return self :: updates(SynchronizationData :: class_name(), $properties, $condition);
    }

    public static function retrieve_instance($id, $type = null)
    {
        if (! isset($id) || strlen($id) == 0 || $id == DataClass :: NO_UID)
        {
            throw new DataClassNoResultException(
                Instance :: class_name(), 
                DataClassRetrieveParameters :: generate((int) $id));
        }
        
        if (is_null($type))
        {
            $type = self :: determine_instance_type($id);
        }
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Instance :: class_name(), Instance :: PROPERTY_ID), 
            new StaticConditionVariable($id));
        
        $parameters = new DataClassRetrieveParameters($condition);
        return self :: fetch_instance($parameters, $type);
    }

    public static function determine_instance_type($id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Instance :: class_name(), Instance :: PROPERTY_ID), 
            new StaticConditionVariable($id));
        $parameters = new RecordRetrieveParameters(
            new DataClassProperties(
                array(new PropertyConditionVariable(Instance :: class_name(), Instance :: PROPERTY_TYPE))), 
            $condition);
        $type = self :: record(Instance :: class_name(), $parameters);
        if (isset($type[Instance :: PROPERTY_TYPE]))
        {
            return $type[Instance :: PROPERTY_TYPE];
        }
        else
        {
            throw new ObjectNotExistException($id);
        }
    }

    private static function fetch_instance($parameters, $type)
    {
        if ($type :: is_extended())
        {
            $join = new Join(
                Instance :: class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(Instance :: class_name(), Instance :: PROPERTY_ID), 
                    new PropertyConditionVariable($type, $type :: PROPERTY_ID)));
            if ($parameters->get_joins() instanceof Joins)
            {
                $joins = $parameters->get_joins();
                $joins->add($join);
                $parameters->set_joins($joins);
            }
            else
            {
                $joins = new Joins(array($join));
                $parameters->set_joins($joins);
            }
        }
        
        return self :: retrieve($type, $parameters);
    }

    public static function count_instances($type, $parameters = null)
    {
        return self :: count($type, self :: prepare_parameters(self :: ACTION_COUNT, $type, $parameters));
    }

    public static function retrieve_instances($type, $parameters = null)
    {
        return self :: retrieves($type, self :: prepare_parameters(self :: ACTION_RETRIEVES, $type, $parameters));
    }

    public static function prepare_parameters($action, $type, $parameters = null)
    {
        if (! is_null($type) && $type :: is_extended())
        {
            $type_join = new Join(
                Instance :: class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(Instance :: class_name(), Instance :: PROPERTY_ID), 
                    new PropertyConditionVariable($type, $type :: PROPERTY_ID)));
            
            if (($parameters instanceof DataClassCountParameters && $action == self :: ACTION_COUNT) ||
                 ($parameters instanceof DataClassRetrievesParameters && $action == self :: ACTION_RETRIEVES))
            {
                if ($parameters->get_joins() instanceof Joins)
                {
                    $already_exists = false;
                    foreach ($parameters->get_joins()->get() as $join)
                    {
                        if ($join->hash() == $type_join->hash())
                        {
                            $already_exists = true;
                        }
                    }
                    if (! $already_exists)
                    {
                        $parameters->get_joins()->add($type_join);
                    }
                }
                else
                {
                    $parameters->set_joins(new Joins(array($type_join)));
                }
            }
            else
            {
                if ($action == self :: ACTION_COUNT)
                {
                    $parameters = new DataClassCountParameters();
                }
                else
                {
                    $parameters = new DataClassRetrievesParameters();
                }
                $parameters->set_joins(new Joins(array($type_join)));
            }
        }
        elseif ($type != Instance :: class_name())
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(Instance :: class_name(), Instance :: PROPERTY_TYPE), 
                new StaticConditionVariable($type));
            
            if (($parameters instanceof DataClassCountParameters && $action == self :: ACTION_COUNT) ||
                 ($parameters instanceof DataClassRetrievesParameters && $action == self :: ACTION_RETRIEVES))
            {
                if ($parameters->get_condition() instanceof Condition)
                {
                    $parameters->set_condition(new AndCondition($parameters->get_condition(), $condition));
                }
                else
                {
                    $parameters->set_condition($condition);
                }
            }
            else
            {
                if ($action == self :: ACTION_COUNT)
                {
                    $parameters = new DataClassCountParameters();
                }
                else
                {
                    $parameters = new DataClassRetrievesParameters();
                }
                $parameters->set_condition($condition);
            }
        }
        
        return $parameters;
    }
}
