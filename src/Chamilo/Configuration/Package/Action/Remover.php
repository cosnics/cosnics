<?php
namespace Chamilo\Configuration\Package\Action;

use Chamilo\Configuration\Package\Action;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Configuration\Storage\DataClass\Setting;
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
class Remover extends Action
{

    /**
     * Removes the package
     *
     * @return bool
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Exception
     */
    public function run(): bool
    {
        if (!$this->verifyDependencies())
        {
            return false;
        }

        if (!$this->deregisterPackage())
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

        if (!$this->deconfigurePackage())
        {
            return false;
        }

        if (!$this->uninstallStorageUnits())
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
     * @throws \ReflectionException
     */
    public function deconfigurePackage(): bool
    {
        $settings_file = $this->getPath() . 'php/settings/settings.xml';

        if (file_exists($settings_file))
        {
            $xml = $this->parseApplicationSettings($settings_file);
            $translator = $this->getTranslator();

            foreach ($xml as $name => $parameters)
            {
                $setting =
                    $this->getConfigurationService()->findSettingByContextAndVariableName($this->getContext(), $name);

                if (!$setting instanceof Setting || !$this->getConfigurationService()->deleteSetting($setting))
                {
                    $message = $translator->trans('PackageDeconfigurationFailed', [], 'Chamilo\Core\Install');

                    return $this->failed($message);
                }
            }

            $this->add_message(
                self::TYPE_NORMAL, $translator->trans('PackageSettingsRemoved', [], 'Chamilo\Core\Install')
            );
        }

        return true;
    }

    /**
     * @throws \Exception
     */
    public function deleteStorageUnit(string $path): bool
    {
        $storage_unit_name = $this->parseXmlFile($path);
        $translator = $this->getTranslator();

        $this->add_message(
            self::TYPE_NORMAL,
            $translator->trans('StorageUnitRemoval', [], 'Chamilo\Core\Install') . ': <em>' . $storage_unit_name .
            '</em>'
        );

        if (!$this->getStorageUnitRepository()->drop($storage_unit_name))
        {
            return $this->failed(
                $translator->trans('StorageUnitRemovalFailed', [], 'Chamilo\Core\Install') . ': <em>' .
                $storage_unit_name . '</em>'
            );
        }
        else
        {
            return true;
        }
    }

    /**
     * @throws \ReflectionException
     */
    public function deregisterPackage(): bool
    {
        if (!$this->getRegistrationService()->deleteRegistrationForContext($this->getContext()))
        {
            return $this->failed(
                $this->getTranslator()->trans('PackageDeregistrationFailed', [], 'Chamilo\Core\Install')
            );
        }
        else
        {
            return true;
        }
    }

    /**
     * Returns the list with extra installable packages that are connected to this package
     *
     * @return string[]
     */
    public function getAdditionalPackages($packagesList = []): array
    {
        return $packagesList;
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
     * @throws \Exception
     */
    public function parseXmlFile(string $file): string
    {
        $doc = new DOMDocument();
        $doc->load($file);
        $object = $doc->getElementsByTagName('object')->item(0);

        if (!$object instanceof DOMElement)
        {
            throw new Exception('Invalid storage info file: ' . $file);
        }

        return $object->getAttribute('name');
    }

    /**
     * Scans for the available storage units and removes them
     *
     * @throws \Exception
     */
    public function uninstallStorageUnits(): bool
    {
        $dir = $this->getPath() . 'Resources/Storage/';
        $files = $this->getFilesystemTools()->getDirectoryContent($dir, FileTypeFilterIterator::ONLY_FILES);

        foreach ($files as $file)
        {
            if ((str_ends_with($file, 'xml')))
            {
                if (!$this->deleteStorageUnit($file))
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @throws \Exception
     */
    public function verifyDependencies(): bool
    {
        $translator = $this->getTranslator();
        $package = $this->getPackageFactory()->getPackage($this->getContext());

        $success = $this->getDependencyVerifier()->isRemovable($package);

        $this->add_message(
            self::TYPE_NORMAL, $this->getDependencyVerifierRenderer()->renderVerifiedDependencies($package)
        );

        if (!$success)
        {
            return $this->failed($translator->trans('PackageDependenciesFailed'));
        }
        else
        {
            $this->add_message(self::TYPE_NORMAL, $translator->trans('PackageDependenciesVerified'));

            return true;
        }
    }

}
