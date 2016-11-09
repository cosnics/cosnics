<?php
namespace Chamilo\Libraries\Platform\Configuration;

/**
 * This class represents the current configurable settings.
 * They are retrieved from the DB via the AdminDataManager
 * 
 * @author Hans De Bisschop
 * @deprecated [18/07/2014] Use the \configuration\Configuration instance now, see individual methods for replacement
 *             statements
 */
class PlatformSetting
{

    /**
     * Gets a parameter from the configuration.
     * 
     * @param $section string The name of the section in which the parameter is located.
     * @param $name string The parameter name.
     * @return mixed The parameter value.
     * @deprecated [18/07/2014] Use \configuration\Configuration :: get($application, $variable) now.
     */
    public static function get($variable, $application = 'Chamilo\Core\Admin')
    {
        return \Chamilo\Configuration\Configuration :: get($application, $variable);
    }

    /**
     *
     * @param string $variable
     * @param string $value
     * @param string $application
     * @deprecated [18/07/2014] Use \configuration\Configuration :: getInstance()->set(array($application, $variable),
     *             $value) now.
     */
    public function set($variable, $value, $application = 'Chamilo\Core\Admin')
    {
        \Chamilo\Configuration\Configuration :: getInstance()->set(array($application, $variable), $value);
    }

    /**
     *
     * @deprecated [18/07/2014] Use \configuration\Configuration :: getInstance()->has_settings($context) now.
     * @param string $application
     * @return boolean
     */
    public static function application_has_settings($application)
    {
        return \Chamilo\Configuration\Configuration :: getInstance()->has_settings($application);
    }
}
