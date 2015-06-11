<?php
namespace Chamilo\Configuration\Storage;

use Chamilo\Configuration\Storage\DataClass\Language;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{

    public static $registrations;
    const REGISTRATION_CONTEXT = 1;
    const REGISTRATION_TYPE = 2;
    const PREFIX = 'configuration_';

    /**
     *
     * @deprecated [18/07/2014] Use \configuration\Configuration :: is_registered($context) now
     * @param string $context
     * @return boolean
     */
    public static function is_registered($context)
    {
        $registration = self :: get_registration($context);
        return ($registration instanceof Registration);
    }

    /**
     *
     * @deprecated [18/07/2014] Use \configuration\Configuration :: registration($context) now
     * @param \configuration\Registration $context
     */
    public static function get_registration($context)
    {
        return \Chamilo\Configuration\Configuration :: registration($context);
    }

    /**
     *
     * @deprecated [18/07/2014] Use \configuration\Configuration :: registrations() now
     * @return multitype:\configuration\Registration
     */
    public static function get_registrations()
    {
        return \Chamilo\Configuration\Configuration :: registrations();
    }

    /**
     *
     * @deprecated [18/07/2014] Use \configuration\Configuration :: registrations_by_type($type) now
     * @param string $type
     * @return \configuration\Registration[]
     */
    public static function get_registrations_by_type($type)
    {
        return \Chamilo\Configuration\Configuration :: registrations_by_type($type);
    }

    public static function retrieve_setting_contexts(Condition $condition = null)
    {
        $parameters = new DataClassDistinctParameters($condition, Setting :: PROPERTY_CONTEXT);
        return self :: distinct(Setting :: class_name(), $parameters);
    }

    /**
     *
     * @param string $variable
     * @param string $context
     * @return \configuration\Setting
     */
    public static function retrieve_setting_from_variable_name($variable, $context = 'Chamilo\Core\Admin')
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting :: class_name(), Setting :: PROPERTY_CONTEXT),
            new StaticConditionVariable($context));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Setting :: class_name(), Setting :: PROPERTY_VARIABLE),
            new StaticConditionVariable($variable));
        $condition = new AndCondition($conditions);

        return self :: retrieve(Setting :: class_name(), new DataClassRetrieveParameters($condition));
    }

    public static function is_language_active($isocode)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Language :: class_name(), Language :: PROPERTY_ISOCODE),
            new StaticConditionVariable($isocode));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Language :: class_name(), Language :: PROPERTY_AVAILABLE),
            new StaticConditionVariable(1));

        $parameters = new DataClassCountParameters(new AndCondition($conditions));

        return self :: count(Language :: class_name(), $parameters) == 1;
    }

    public static function retrieve_language_from_english_name($english_name)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Language :: class_name(), Language :: PROPERTY_ENGLISH_NAME),
            new StaticConditionVariable($english_name));
        $parameters = new DataClassRetrieveParameters($condition);
        return self :: retrieve(Language :: class_name(), $parameters);
    }

    public static function retrieve_language_from_isocode($isocode)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Language :: class_name(), Language :: PROPERTY_ISOCODE),
            new StaticConditionVariable($isocode));

        $parameters = new DataClassRetrieveParameters($condition);
        return self :: retrieve(Language :: class_name(), $parameters);
    }

    public static function get_languages($use_folder_name_as_key = true)
    {
        $options = array();

        $languages = self :: retrieves(Language :: class_name());
        while ($language = $languages->next_result())
        {
            if ($use_folder_name_as_key)
            {
                $key = $language->get_isocode();
            }
            else
            {
                $key = $language->get_id();
            }
            $options[$key] = $language->get_original_name();
        }

        return $options;
    }

    /**
     *
     * @param string $integration The context that has been integrated
     * @param string $root The root context underneath which the integrating contexts should be looked for
     * @return multitype:\configuration\Registration
     */
    public static function get_integrating_contexts($integration, $root = null)
    {
        $condition = new PatternMatchCondition(
            new PropertyConditionVariable(Registration :: class_name(), Registration :: PROPERTY_CONTEXT),
            '*\\\Integration\\' . $integration);

        if ($root)
        {
            $conditions = array();
            $conditions[] = $condition;
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Registration :: class_name(), Registration :: PROPERTY_CONTEXT),
                $root . '*');
            $condition = new AndCondition($conditions);
        }

        return self :: retrieves(Registration :: class_name(), new DataClassRetrievesParameters($condition))->as_array();
    }
}
