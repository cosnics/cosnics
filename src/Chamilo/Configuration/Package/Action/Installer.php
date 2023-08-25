<?php
namespace Chamilo\Configuration\Package\Action;

use Chamilo\Configuration\Package\Action;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Configuration\Package\Properties\Dependencies\DependencyVerifier;
use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Configuration\Service\RegistrationService;
use Chamilo\Configuration\Storage\DataClass\Registration;
use DOMDocument;
use DOMElement;
use Exception;
use Symfony\Component\Finder\Iterator\FileTypeFilterIterator;

/**
 * @package Chamilo\Configuration\Package\Action
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Installer extends Action
{

    /**
     * Form values passed on from the installation wizard
     */
    private array $formValues;

    /**
     * Constructor
     */
    public function __construct(array $formValues)
    {
        parent::__construct();

        $this->formValues = $formValues;
    }

    /**
     * Installs and configures the package
     *
     * @throws \Exception
     */
    public function run(): bool
    {
        if (!$this->verifyDependencies())
        {
            return false;
        }

        if (!$this->installStorageUnits())
        {
            return false;
        }

        if (!$this->configurePackage())
        {
            return false;
        }

        if (method_exists($this, 'extra'))
        {
            $translator = $this->getTranslator();

            $this->add_message(
                self::TYPE_NORMAL,
                '<small class="text-muted">' . $translator->trans('Various', [], 'Chamilo\Core\Install') . '<small>'
            );

            if (!$this->extra())
            {
                return $this->failed($translator->trans('VariousFailed', [], 'Chamilo\Core\Install'));
            }
            else
            {
                $this->add_message(
                    self::TYPE_NORMAL, $translator->trans('VariousFinished', [], 'Chamilo\Core\Install')
                );
            }

            $this->add_message(self::TYPE_NORMAL, '');
        }

        if (!$this->registerPackage())
        {
            return false;
        }

        $packageBundlesCacheService = $this->getPackageBundlesCacheService();

        $packageBundlesCacheService->clearCacheDataForIdentifier(PackageList::MODE_AVAILABLE);
        $packageBundlesCacheService->clearCacheDataForIdentifier(PackageList::MODE_INSTALLED);

        return $this->successful();
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function configurePackage(): bool
    {
        $translator = $this->getTranslator();
        $settings_file = $this->getPath() . 'Resources/Settings/settings.xml';

        if (file_exists($settings_file))
        {
            $xml = $this->parseApplicationSettings($settings_file);

            foreach ($xml as $name => $parameters)
            {
                if (!$this->getConfigurationService()->createSettingFromParameters(
                    static::CONTEXT, $name, $parameters['default'], (bool) $parameters['user_setting']
                ))
                {
                    $message = $translator->trans('PackageConfigurationFailed', [], 'Chamilo\Core\Install');

                    return $this->failed($message);
                }
            }

            $this->add_message(
                self::TYPE_NORMAL, $translator->trans('PackageSettingsAdded', [], 'Chamilo\Core\Install')
            );
        }

        return true;
    }

    /**
     * Parses an XML file and sends the request to the database manager
     *
     * @throws \Exception
     */
    public function createStorageUnit(string $path): bool
    {
        $translator = $this->getTranslator();
        $storage_unit_info = $this->parseXmlFile($path);

        $this->add_message(
            self::TYPE_NORMAL, $translator->trans('StorageUnitCreation', [], 'Chamilo\Core\Install') . ': <em>' .
            $storage_unit_info['name'] . '</em>'
        );

        $table_name = $storage_unit_info['name'];

        if (!$this->getStorageUnitRepository()->create(
            $table_name, $storage_unit_info['properties'], $storage_unit_info['indexes']
        ))
        {
            return $this->failed(
                $translator->trans('StorageUnitCreationFailed', [], 'Chamilo\Core\Install') . ': <em>' .
                $storage_unit_info['name'] . '</em>'
            );
        }
        else
        {
            return true;
        }
    }

    /**
     * Creates an application-specific installer.
     *
     * @param string $context The namespace of the package for which we want to start the installer.
     * @param array $values   The form values passed on by the wizard.
     */
    public static function factory(string $context, array $values): Installer
    {
        $class = $context . '\Package\Installer';

        return new $class($values);
    }

    /**
     * Returns the list with extra installable packages that are connected to this package
     *
     * @return string[]
     */
    public static function getAdditionalPackages($packagesList = []): array
    {
        return $packagesList;
    }

    public function getFormValues(): array
    {
        return $this->formValues;
    }

    public function getPackageBundlesCacheService(): PackageBundlesCacheService
    {
        return $this->getService(PackageBundlesCacheService::class);
    }

    public function getPackageFactory(): PackageFactory
    {
        return $this->getService(PackageFactory::class);
    }

    public function getPath(): string
    {
        return $this->getSystemPathBuilder()->namespaceToFullPath(static::CONTEXT);
    }

    public function getRegistrationService(): RegistrationService
    {
        return $this->getService(RegistrationService::class);
    }

    /**
     * @throws \Exception
     */
    public function installStorageUnits(): bool
    {
        $dir = $this->getPath() . 'Resources/Storage/';
        $files = $this->getFilesystemTools()->getDirectoryContent($dir, FileTypeFilterIterator::ONLY_FILES);

        foreach ($files as $file)
        {
            if ((str_ends_with($file, 'xml')))
            {
                if (!$this->createStorageUnit($file))
                {
                    return false;
                }
            }
        }

        return true;
    }

    public function parseApplicationSettings(string $file): array
    {
        $doc = new DOMDocument();

        $doc->load($file);

        $setting_elements = $doc->getElementsByTagName('setting');
        $settings = [];

        foreach ($setting_elements as $setting_element)
        {
            $settings[$setting_element->getAttribute('name')] = [
                'default' => $setting_element->getAttribute('default'),
                'user_setting' => $setting_element->getAttribute('user_setting')
            ];
        }

        return $settings;
    }

    /**
     * Parses an XML file describing a storage unit.
     * For defining the 'type' of the field, the same definition is used
     * as the PEAR::MDB2 package. See http://pear.php.net/manual/en/package.database. mdb2.datatypes.php
     *
     * @param string $file The complete path to the XML-file from which the storage unit definition should be read.
     *
     * @return array An with values for the keys 'name','properties' and 'indexes'
     * @throws \Exception
     */
    public function parseXmlFile(string $file): array
    {
        $properties = [];
        $indexes = [];

        $doc = new DOMDocument();
        $doc->load($file);
        $object = $doc->getElementsByTagName('object')->item(0);

        if (!$object instanceof DOMElement)
        {
            throw new Exception('Invalid storage info file: ' . $file);
        }

        $name = $object->getAttribute('name');
        $xml_properties = $doc->getElementsByTagName('property');
        $attributes = ['type', 'length', 'unsigned', 'notnull', 'default', 'autoincrement', 'fixed'];

        foreach ($xml_properties as $property)
        {
            $property_info = [];

            foreach ($attributes as $attribute)
            {
                if ($property->hasAttribute($attribute))
                {
                    $property_info[$attribute] = $property->getAttribute($attribute);
                }
            }

            $properties[$property->getAttribute('name')] = $property_info;
        }

        $xml_indexes = $doc->getElementsByTagName('index');

        foreach ($xml_indexes as $index)
        {
            $index_info = [];
            $index_info['type'] = $index->getAttribute('type');
            $index_properties = $index->getElementsByTagName('indexproperty');

            foreach ($index_properties as $index_property)
            {
                $index_info['fields'][$index_property->getAttribute('name')] = [
                    'length' => $index_property->getAttribute('length')
                ];
            }

            $indexes[$index->getAttribute('name')] = $index_info;
        }

        $result = [];
        $result['name'] = $name;
        $result['properties'] = $properties;
        $result['indexes'] = $indexes;

        return $result;
    }

    /**
     * @throws \Exception
     */
    public function registerPackage(): bool
    {
        $translator = $this->getTranslator();

        $this->add_message(self::TYPE_NORMAL, $translator->trans('RegisteringPackage', [], 'Chamilo\Core\Install'));

        $package = $this->getPackageFactory()->getPackage(static::CONTEXT);

        if (!$this->getRegistrationService()->createRegistrationFromParameters(
            static::CONTEXT, $package->getType(), $package->get_category(), $package->get_name(),
            $package->get_version(), Registration::STATUS_ACTIVE
        ))
        {
            return $this->failed($translator->trans('PackageRegistrationFailed', [], 'Chamilo\Core\Install'));
        }
        else
        {
            return true;
        }
    }

    /**
     * @throws \Exception
     */
    public function verifyDependencies(): bool
    {
        $translator = $this->getTranslator();

        $verifier = new DependencyVerifier($this->getPackageFactory()->getPackage(static::CONTEXT));
        $success = $verifier->is_installable();

        $this->add_message(self::TYPE_NORMAL, $verifier->get_logger()->render());

        if (!$success)
        {
            return $this->failed($translator->trans('PackageDependenciesFailed', [], 'Chamilo\Configuration\Package'));
        }
        else
        {
            $this->add_message(
                self::TYPE_NORMAL,
                $translator->trans('PackageDependenciesVerified', [], 'Chamilo\Configuration\Package')
            );

            return true;
        }
    }
}
