<?php
namespace Chamilo\Core\Repository\Instance\Storage\DataClass;

use Chamilo\Core\Repository\Instance\Storage\DataManager;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use DOMDocument;

/**
 *
 * @author Hans De Bisschop
 */
class Setting extends DataClass
{
    const PROPERTY_EXTERNAL_ID = 'external_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_VALUE = 'value';
    const PROPERTY_VARIABLE = 'variable';

    /**
     * A static array containing all settings of external repository instances
     *
     * @var array
     */
    private static $settings;

    /**
     * Get the default properties of all settings.
     *
     * @return array The property names.
     */

    public function delete()
    {
        if (!parent::delete())
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     *
     * @param $variable string
     * @param $external_repository_id int
     *
     * @return mixed
     */
    public static function get($variable, $external_id)
    {
        if (!isset(self::$settings[$external_id]))
        {
            self::load($external_id);
        }

        return (isset(self::$settings[$external_id][$variable]) ? self::$settings[$external_id][$variable] : null);
    }

    public static function get_all($external_id)
    {
        if (!isset(self::$settings[$external_id]))
        {
            self::load($external_id);
        }

        return self::$settings[$external_id];
    }

    public static function get_class_name()
    {
        return self::class;
    }

    /**
     *
     * @return array:
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array(self::PROPERTY_EXTERNAL_ID, self::PROPERTY_VARIABLE, self::PROPERTY_VALUE, self::PROPERTY_USER_ID)
        );
    }

    /**
     *
     * @return string the external repository id
     */
    public function get_external_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_EXTERNAL_ID);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'repository_instance_setting';
    }

    /**
     * Returns the user_id of this setting object
     *
     * @return string the user_id
     */
    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    /**
     * Returns the value of this setting object
     *
     * @return string the value
     */
    public function get_value()
    {
        return $this->getDefaultProperty(self::PROPERTY_VALUE);
    }

    /**
     * Returns the variable of this setting object
     *
     * @return string the variable
     */
    public function get_variable()
    {
        return $this->getDefaultProperty(self::PROPERTY_VARIABLE);
    }

    public static function initialize(Instance $external_instance)
    {
        $settings_file = Path::getInstance()->namespaceToFullPath($external_instance->get_implementation()) .
            'Resources/Settings/Settings.xml';

        $doc = new DOMDocument();

        $doc->load($settings_file);
        $object = $doc->getElementsByTagname('application')->item(0);
        $settings = $doc->getElementsByTagname('setting');

        foreach ($settings as $index => $setting)
        {
            $external_setting = new self();
            $external_setting->set_external_id($external_instance->get_id());
            $external_setting->set_variable($setting->getAttribute('name'));
            $external_setting->set_value($setting->getAttribute('default'));

            $user_setting = $setting->getAttribute('user_setting');

            if (!$external_setting->create())
            {
                return false;
            }
        }

        return true;
    }

    /**
     *
     * @param $external_repository_id int
     */
    public static function load($external_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self::class, self::PROPERTY_EXTERNAL_ID),
            new StaticConditionVariable($external_id)
        );
        $settings = DataManager::retrieves(self::class, new DataClassRetrievesParameters($condition));

        foreach ($settings as $setting)
        {
            self::$settings[$external_id][$setting->get_variable()] = $setting->get_value();
        }
    }

    /**
     *
     * @param $external_repository_id string
     */
    public function set_external_id($external_id)
    {
        $this->setDefaultProperty(self::PROPERTY_EXTERNAL_ID, $external_id);
    }

    /**
     * Sets the user_id of this setting.
     *
     * @param $user_id string the user_id.
     */
    public function set_user_id($user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }

    /**
     * Sets the value of this setting.
     *
     * @param $value string the value.
     */
    public function set_value($value)
    {
        $this->setDefaultProperty(self::PROPERTY_VALUE, $value);
    }

    /**
     * Sets the variable of this setting.
     *
     * @param $variable string the variable.
     */
    public function set_variable($variable)
    {
        $this->setDefaultProperty(self::PROPERTY_VARIABLE, $variable);
    }
}
