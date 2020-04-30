<?php
namespace Chamilo\Configuration\Storage;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;

class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{

    public static $registrations;
    const REGISTRATION_CONTEXT = 1;
    const REGISTRATION_TYPE = 2;
    const PREFIX = 'configuration_';

    public static function retrieve_setting_contexts(Condition $condition = null)
    {
        $parameters = new DataClassDistinctParameters(
            $condition,
            new DataClassProperties(array(new PropertyConditionVariable(Setting::class, Setting::PROPERTY_CONTEXT))));
        return self::distinct(Setting::class, $parameters);
    }

    /**
     *
     * @param string $variable
     * @param string $context
     * @return Setting
     */
    public static function retrieve_setting_from_variable_name($variable, $context = 'Chamilo\Core\Admin')
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting::class, Setting::PROPERTY_CONTEXT),
            new StaticConditionVariable($context));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting::class, Setting::PROPERTY_VARIABLE),
            new StaticConditionVariable($variable));
        $condition = new AndCondition($conditions);

        return self::retrieve(Setting::class, new DataClassRetrieveParameters($condition));
    }

    public static function retrieveRegistrationByContext($context)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Registration::class, Registration::PROPERTY_CONTEXT),
            new StaticConditionVariable($context));

        return self::retrieve(Registration::class, new DataClassRetrieveParameters($condition));
    }

    /**
     *
     * @param string $integration The context that has been integrated
     * @param string $root The root context underneath which the integrating contexts should be looked for
     * @return multitype:\configuration\Registration
     */
    public static function get_integrating_contexts($integration, $root = null)
    {
        $conditions = array();
        $conditions[] = new PatternMatchCondition(
            new PropertyConditionVariable(Registration::class, Registration::PROPERTY_CONTEXT),
            '*\\\Integration\\' . $integration);

        if ($root)
        {
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Registration::class, Registration::PROPERTY_CONTEXT),
                $root . '*');
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Registration::class, Registration::PROPERTY_STATUS),
            new StaticConditionVariable(1));

        $condition = new AndCondition($conditions);

        return self::retrieves(Registration::class, new DataClassRetrievesParameters($condition))->as_array();
    }
}
