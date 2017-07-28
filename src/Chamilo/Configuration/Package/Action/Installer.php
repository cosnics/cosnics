<?php
namespace Chamilo\Configuration\Package\Action;

use Chamilo\Configuration\Package\Action;
use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Configuration\Package\Properties\Dependencies\DependencyVerifier;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Platform\Translation;
use DOMDocument;

/**
 *
 * @package Chamilo\Configuration\Package\Action
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Installer extends Action
{

    /**
     * Form values passed on from the installation wizard
     */
    private $form_values;

    /**
     * Constructor
     */
    public function __construct($form_values)
    {
        parent::__construct();

        $this->form_values = $form_values;
    }

    /**
     * Installs and configures the package
     *
     * @return boolean
     */
    public function run()
    {
        if (! $this->verify_dependencies())
        {
            return false;
        }

        if (! $this->install_storage_units())
        {
            return false;
        }

        if (! $this->configure_package())
        {
            return false;
        }

        if (method_exists($this, 'extra'))
        {
            $this->add_message(
                self::TYPE_NORMAL,
                '<span class="subtitle">' . Translation::get('Various', null, 'Chamilo\Core\Install') . '</span>');
            if (! $this->extra())
            {
                return $this->failed(Translation::get('VariousFailed', null, 'Chamilo\Core\Install'));
            }
            else
            {
                $this->add_message(
                    self::TYPE_NORMAL,
                    Translation::get('VariousFinished', null, 'Chamilo\Core\Install'));
            }
            $this->add_message(self::TYPE_NORMAL, '');
        }

        if (! $this->register_package())
        {
            return false;
        }

        PlatformPackageBundles::getInstance(PlatformPackageBundles::MODE_AVAILABLE)->reset();
        PlatformPackageBundles::getInstance(PlatformPackageBundles::MODE_INSTALLED)->reset();

        return $this->successful();
    }

    /**
     * Verifies the package dependencies
     *
     * @param $package_attributes
     * @return boolean
     */
    public function verify_dependencies()
    {
        $context = ClassnameUtilities::getInstance()->getNamespaceParent(static::context());

        $verifier = new DependencyVerifier(Package::get($context));
        $success = $verifier->is_installable();

        $this->add_message(self::TYPE_NORMAL, $verifier->get_logger()->render());

        if (! $success)
        {
            return $this->failed(Translation::get('PackageDependenciesFailed'));
        }
        else
        {
            $this->add_message(self::TYPE_NORMAL, Translation::get('PackageDependenciesVerified'));
            return true;
        }
    }

    /**
     * Scans for the available storage units and creates them
     *
     * @return boolean
     */
    public function install_storage_units()
    {
        $dir = $this->get_path() . 'Resources/Storage/';
        $files = Filesystem::get_directory_content($dir, Filesystem::LIST_FILES);

        foreach ($files as $file)
        {
            if ((substr($file, - 3) == 'xml'))
            {
                if (! $this->create_storage_unit($file))
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Parses an XML file describing a storage unit.
     * For defining the 'type' of the field, the same definition is used
     * as the PEAR::MDB2 package. See http://pear.php.net/manual/en/package.database. mdb2.datatypes.php
     *
     * @param $file string The complete path to the XML-file from which the storage unit definition should be read.
     * @return array An with values for the keys 'name','properties' and 'indexes'
     */
    public static function parse_xml_file($file)
    {
        $name = '';
        $properties = array();
        $indexes = array();

        $doc = new DOMDocument();
        $doc->load($file);
        $object = $doc->getElementsByTagname('object')->item(0);
        $name = $object->getAttribute('name');
        $xml_properties = $doc->getElementsByTagname('property');
        $attributes = array('type', 'length', 'unsigned', 'notnull', 'default', 'autoincrement', 'fixed');
        foreach ($xml_properties as $index => $property)
        {
            $property_info = array();
            foreach ($attributes as $index => $attribute)
            {
                if ($property->hasAttribute($attribute))
                {
                    $property_info[$attribute] = $property->getAttribute($attribute);
                }
            }
            $properties[$property->getAttribute('name')] = $property_info;
        }
        $xml_indexes = $doc->getElementsByTagname('index');
        foreach ($xml_indexes as $key => $index)
        {
            $index_info = array();
            $index_info['type'] = $index->getAttribute('type');
            $index_properties = $index->getElementsByTagname('indexproperty');
            foreach ($index_properties as $subkey => $index_property)
            {
                $index_info['fields'][$index_property->getAttribute('name')] = array(
                    'length' => $index_property->getAttribute('length'));
            }
            $indexes[$index->getAttribute('name')] = $index_info;
        }
        $result = array();
        $result['name'] = $name;
        $result['properties'] = $properties;
        $result['indexes'] = $indexes;

        return $result;
    }

    public function set_form_values($form_values)
    {
        $this->form_values = $form_values;
    }

    public function get_form_values()
    {
        return $this->form_values;
    }

    /**
     * Parses an XML file and sends the request to the database manager
     *
     * @param $path String
     */
    public function create_storage_unit($path)
    {
        $storage_unit_info = self::parse_xml_file($path);

        $this->add_message(
            self::TYPE_NORMAL,
            Translation::getInstance()->getTranslation('StorageUnitCreation', null, 'Chamilo\Core\Install') . ': <em>' .
                 $storage_unit_info['name'] . '</em>');

        $context = ClassnameUtilities::getInstance()->getNamespaceParent(static::context());
        $data_manager = $context . '\Storage\DataManager';

        $prefix = $data_manager::PREFIX;
        $table_name = $storage_unit_info['name'];

        if(strpos($table_name, $prefix) !== 0)
        {
            $table_name = $prefix . $table_name;
        }

        if (! $data_manager::create_storage_unit(
            $table_name,
            $storage_unit_info['properties'],
            $storage_unit_info['indexes']))
        {
            return $this->failed(
                Translation::getInstance()->getTranslation('StorageUnitCreationFailed', null, 'Chamilo\Core\Install') .
                     ': <em>' . $storage_unit_info['name'] . '</em>');
        }
        else
        {
            return true;
        }
    }

    public function parse_application_settings($file)
    {
        $doc = new DOMDocument();

        $doc->load($file);

        $setting_elements = $doc->getElementsByTagname('setting');
        $settings = array();

        foreach ($setting_elements as $setting_element)
        {
            $settings[$setting_element->getAttribute('name')] = array(
                'default' => $setting_element->getAttribute('default'),
                'user_setting' => $setting_element->getAttribute('user_setting'));
        }

        return $settings;
    }

    public function configure_package()
    {
        $settings_file = $this->get_path() . 'Resources/Settings/settings.xml';

        if (file_exists($settings_file))
        {
            $xml = $this->parse_application_settings($settings_file);

            foreach ($xml as $name => $parameters)
            {
                $setting = new Setting();
                $setting->set_context(ClassnameUtilities::getInstance()->getNamespaceParent(static::context()));
                $setting->set_variable($name);
                $setting->set_value($parameters['default']);

                $user_setting = $parameters['user_setting'];
                if ($user_setting)
                {
                    $setting->set_user_setting($user_setting);
                }
                else
                {
                    $setting->set_user_setting(0);
                }

                if (! $setting->create())
                {
                    $message = Translation::get('PackageConfigurationFailed', null, 'Chamilo\Core\Install');
                    return $this->failed($message);
                }
            }

            $this->add_message(
                self::TYPE_NORMAL,
                Translation::get('PackageSettingsAdded', null, 'Chamilo\Core\Install'));
        }

        return true;
    }

    public function register_package()
    {
        $namespace = ClassnameUtilities::getInstance()->getNamespaceParent(static::context());

        $this->add_message(self::TYPE_NORMAL, Translation::get('RegisteringPackage', null, 'Chamilo\Core\Install'));

        $package_info = Package::get($namespace);

        $application_registration = new Registration();
        $application_registration->set_type(($package_info->get_type()));
        $application_registration->set_name($package_info->get_code());
        $application_registration->set_category($package_info->get_category());
        $application_registration->set_version($package_info->get_version());
        $application_registration->set_status(Registration::STATUS_ACTIVE);
        $application_registration->set_context($namespace);

        if (! $application_registration->create())
        {
            return $this->failed(Translation::get('PackageRegistrationFailed', null, 'Chamilo\Core\Install'));
        }
        else
        {
            return true;
        }
    }

    /**
     * Creates an application-specific installer.
     *
     * @param $context string The namespace of the package for which we want to start the installer.
     * @param $values string The form values passed on by the wizard.
     */
    public static function factory($context, $values)
    {
        $class = $context . '\Package\Installer';

        return new $class($values);
    }

    /**
     * Returns the list with extra installable packages that are connected to this package
     *
     * @return multitype:string
     */
    public static function get_additional_packages()
    {
        return array();
    }
}
