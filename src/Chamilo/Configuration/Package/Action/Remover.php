<?php
namespace Chamilo\Configuration\Package\Action;

use Chamilo\Configuration\Package\Action;
use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Configuration\Package\Properties\Dependencies\DependencyVerifier;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Configuration\Storage\DataManager;
use Chamilo\Libraries\Translation\Translation;
use DOMDocument;
use Symfony\Component\Finder\Iterator\FileTypeFilterIterator;

/**
 * @package Chamilo\Configuration\Package\Action
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Remover extends Action
{

    /**
     * Removes the package
     *
     * @return bool
     */
    public function run()
    {
        if (!$this->verify_dependencies())
        {
            return false;
        }

        if (!$this->deregister_package())
        {
            return false;
        }

        if (method_exists($this, 'extra'))
        {
            $this->add_message(
                self::TYPE_NORMAL,
                '<small class="text-muted">' . Translation::get('Various', null, 'Chamilo\Core\Install') . '<small>'
            );
            if (!$this->extra())
            {
                return $this->failed(Translation::get('VariousFailed', null, 'Chamilo\Core\Install'));
            }
            else
            {
                $this->add_message(
                    self::TYPE_NORMAL, Translation::get('VariousFinished', null, 'Chamilo\Core\Install')
                );
            }
            $this->add_message(self::TYPE_NORMAL, '');
        }

        if (!$this->deconfigure_package())
        {
            return false;
        }

        if (!$this->uninstall_storage_units())
        {
            return false;
        }

        PlatformPackageBundles::getInstance(PlatformPackageBundles::MODE_AVAILABLE)->reset();
        PlatformPackageBundles::getInstance(PlatformPackageBundles::MODE_INSTALLED)->reset();

        return $this->successful();
    }

    public function deconfigure_package()
    {
        $settings_file = $this->get_path() . 'php/settings/settings.xml';

        if (file_exists($settings_file))
        {
            $xml = $this->parse_application_settings($settings_file);

            foreach ($xml as $name => $parameters)
            {
                $setting = DataManager::retrieve_setting_from_variable_name(
                    $name, static::CONTEXT
                );

                if (!$setting instanceof Setting || !$setting->delete())
                {
                    $message = Translation::get('PackageDeconfigurationFailed', null, 'Chamilo\Core\Install');

                    return $this->failed($message);
                }
            }

            $this->add_message(
                self::TYPE_NORMAL, Translation::get('PackageSettingsRemoved', null, 'Chamilo\Core\Install')
            );
        }

        return true;
    }

    /**
     * Parses an XML file and sends the request to the database manager
     *
     * @param $path String
     */
    public function delete_storage_unit($path)
    {
        $storage_unit_name = self::parse_xml_file($path);

        $this->add_message(
            self::TYPE_NORMAL,
            Translation::getInstance()->getTranslation('StorageUnitRemoval', null, 'Chamilo\Core\Install') . ': <em>' .
            $storage_unit_name . '</em>'
        );

        $data_manager = static::CONTEXT . '\DataManager';

        if (!$data_manager::drop_storage_unit($storage_unit_name))
        {
            return $this->failed(
                Translation::getInstance()->getTranslation('StorageUnitRemovalFailed', null, 'Chamilo\Core\Install') .
                ': <em>' . $storage_unit_name . '</em>'
            );
        }
        else
        {
            return true;
        }
    }

    public function deregister_package()
    {
        $registration = DataManager::retrieveRegistrationByContext(static::CONTEXT);

        if (!$registration->delete())
        {
            return $this->failed(Translation::get('PackageDeregistrationFailed', null, 'Chamilo\Core\Install'));
        }
        else
        {
            return true;
        }
    }

    /**
     * @param string $context
     *
     * @return \configuration\package\action\Remover
     */
    public static function factory($context)
    {
        $class = $context . '\Package\Remover';

        return new $class();
    }

    /**
     * Returns the list with extra installable packages that are connected to this package
     *
     * @return string[]
     */
    public function get_additional_packages()
    {
        return [];
    }

    public function get_path()
    {
        return $this->getSystemPathBuilder()->namespaceToFullPath(static::CONTEXT);
    }

    public function parse_application_settings($file)
    {
        $doc = new DOMDocument();

        $doc->load($file);
        $object = $doc->getElementsByTagname('package')->item(0);

        $setting_elements = $doc->getElementsByTagname('setting');
        $settings = [];

        foreach ($setting_elements as $index => $setting_element)
        {
            $settings[$setting_element->getAttribute('name')] = [
                'default' => $setting_element->getAttribute('default'),
                'user_setting' => $setting_element->getAttribute('user_setting')
            ];
        }

        return $settings;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public static function parse_xml_file($file)
    {
        $doc = new DOMDocument();
        $doc->load($file);
        $object = $doc->getElementsByTagname('object')->item(0);

        return $object->getAttribute('name');
    }

    /**
     * Scans for the available storage units and removes them
     *
     * @return bool
     */
    public function uninstall_storage_units()
    {
        $dir = $this->get_path() . 'php/package/install/';
        $files = $this->getFilesystemTools()->getDirectoryContent($dir, FileTypeFilterIterator::ONLY_FILES);

        foreach ($files as $file)
        {
            if ((substr($file, - 3) == 'xml'))
            {
                if (!$this->delete_storage_unit($file))
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Verifies the package dependencies
     *
     * @param $package_attributes
     *
     * @return bool
     */
    public function verify_dependencies()
    {
        $verifier = new DependencyVerifier(Package::get(static::CONTEXT));
        $success = $verifier->is_removable();

        $this->add_message(self::TYPE_NORMAL, $verifier->get_logger()->render());

        if (!$success)
        {
            return $this->failed(Translation::get('PackageDependenciesFailed'));
        }
        else
        {
            $this->add_message(self::TYPE_NORMAL, Translation::get('PackageDependenciesVerified'));

            return true;
        }
    }
}
