<?php
namespace Chamilo\Core\Repository\Instance\Storage;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

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
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting::class, Setting::PROPERTY_EXTERNAL_ID), 
            new StaticConditionVariable($external_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting::class, Setting::PROPERTY_VARIABLE), 
            new StaticConditionVariable($variable));
        $condition = new AndCondition($conditions);
        
        return self::retrieve(Setting::class, new DataClassRetrieveParameters($condition));
    }

    public static function retrieve_synchronization_data($condition)
    {
        $join = new Join(
            ContentObject::class, 
            new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID), 
                new PropertyConditionVariable(
                    SynchronizationData::class, 
                    SynchronizationData::PROPERTY_CONTENT_OBJECT_ID)));
        
        $parameters = new DataClassRetrieveParameters($condition);
        $parameters->set_joins(new Joins(array($join)));
        
        return self::retrieve(SynchronizationData::class, $parameters);
    }

    public static function retrieve_synchronization_data_set($condition = null, $count = null, $offset = null, $order_by = [])
    {
        $join = new Join(
            ContentObject::class, 
            new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID), 
                new PropertyConditionVariable(
                    SynchronizationData::class, 
                    SynchronizationData::PROPERTY_CONTENT_OBJECT_ID)));
        
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_by, new Joins(array($join)));
        
        return self::retrieves(SynchronizationData::class, $parameters);
    }

    public static function retrieve_active_instances($types = [])
    {
        $configuration = Configuration::getInstance();
        $stringUtilities = StringUtilities::getInstance();
        
        $packages = $configuration->get_registrations_by_type(
            Manager::context() . '\Implementation');
        
        $namespaces = [];
        
        foreach ($types as $type)
        {
            $typeName = ClassnameUtilities::getInstance()->getPackageNameFromNamespace($type);
            foreach ($packages as $package)
            {
                $packageContext = $package[Registration::PROPERTY_CONTEXT];
                if ($stringUtilities->createString($packageContext)->contains($typeName))
                {
                    $namespaces[] = $packageContext;
                }
            }
        }
        
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance::class, Instance::PROPERTY_ENABLED), 
            new StaticConditionVariable(1));
        
        if (count($namespaces) > 0)
        {
            $conditions[] = new InCondition(
                new PropertyConditionVariable(Instance::class, Instance::PROPERTY_IMPLEMENTATION), 
                $namespaces);
        }
        
        $condition = new AndCondition($conditions);
        
        return self::retrieves(Instance::class, new DataClassRetrievesParameters($condition));
    }

    public static function deactivate_instance_objects($external_instance_id, $user_id, $external_user_id)
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(SynchronizationData::class, SynchronizationData::PROPERTY_EXTERNAL_ID), 
            new StaticConditionVariable($external_instance_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                SynchronizationData::class, 
                SynchronizationData::PROPERTY_EXTERNAL_USER_ID), 
            new StaticConditionVariable($external_user_id));
        
        $name = new PropertyConditionVariable(
            SynchronizationData::class, 
            SynchronizationData::PROPERTY_CONTENT_OBJECT_ID);
        $value = new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID);
        $sub_select_condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID), 
            new StaticConditionVariable($user_id));
        $conditions[] = new SubselectCondition($name, $value, $sub_select_condition);
        $condition = new AndCondition($conditions);
        
        $properties = [];
        $properties[new PropertyConditionVariable(
            SynchronizationData::class, 
            SynchronizationData::PROPERTY_STATE)] = new StaticConditionVariable(SynchronizationData::STATE_INACTIVE);
        
        return self::updates(SynchronizationData::class, $properties, $condition);
    }

    public static function activate_instance_objects($external_instance_id, $user_id, $external_user_id)
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(SynchronizationData::class, SynchronizationData::PROPERTY_EXTERNAL_ID), 
            new StaticConditionVariable($external_instance_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                SynchronizationData::class, 
                SynchronizationData::PROPERTY_EXTERNAL_USER_ID), 
            new StaticConditionVariable($external_user_id));
        
        $name = new PropertyConditionVariable(
            SynchronizationData::class, 
            SynchronizationData::PROPERTY_CONTENT_OBJECT_ID);
        $value = new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID);
        $sub_select_condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID), 
            new StaticConditionVariable($user_id));
        $conditions[] = new SubselectCondition($name, $value, $sub_select_condition);
        $condition = new AndCondition($conditions);
        
        $properties = [];
        $properties[new PropertyConditionVariable(
            SynchronizationData::class, 
            SynchronizationData::PROPERTY_STATE)] = new StaticConditionVariable(SynchronizationData::STATE_ACTIVE);
        
        return self::updates(SynchronizationData::class, $properties, $condition);
    }

    /**
     * Retrieves an external repository instance user setting
     * 
     * @param int $externalInstanceId
     * @param int $userId
     * @param string $variable
     *
     * @return Setting
     */
    public static function retrieveUserSetting($externalInstanceId, $userId, $variable)
    {
        $conditions = [];
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting::class, Setting::PROPERTY_EXTERNAL_ID), 
            new StaticConditionVariable($externalInstanceId));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting::class, Setting::PROPERTY_VARIABLE), 
            new StaticConditionVariable($variable));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting::class, Setting::PROPERTY_USER_ID), 
            new StaticConditionVariable($userId));
        $condition = new AndCondition($conditions);
        
        return self::retrieve(Setting::class, new DataClassRetrieveParameters($condition));
    }
}
