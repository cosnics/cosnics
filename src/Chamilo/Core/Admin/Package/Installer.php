<?php
namespace Chamilo\Core\Admin\Package;

use Chamilo\Configuration\Package\Properties\Dependencies\DependencyVerifier;
use Chamilo\Configuration\Package\Properties\Dependencies\DependencyVerifierRenderer;
use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Configuration\Service\ConfigurationService;
use Chamilo\Configuration\Service\RegistrationService;
use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Core\Admin\Announcement\Service\RightsService;
use Chamilo\Core\Admin\Manager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\DataManager\Repository\StorageUnitRepository;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Admin\Package
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{
    public const CONTEXT = Manager::CONTEXT;

    protected DataClassRepositoryCache $dataClassRepositoryCache;

    protected RightsService $rightsService;

    public function __construct(
        ClassnameUtilities $classnameUtilities, ConfigurationService $configurationService,
        StorageUnitRepository $storageUnitRepository, Translator $translator,
        PackageBundlesCacheService $packageBundlesCacheService, PackageFactory $packageFactory,
        RegistrationService $registrationService, SystemPathBuilder $systemPathBuilder,
        DependencyVerifier $dependencyVerifier, DependencyVerifierRenderer $dependencyVerifierRenderer, string $context,
        DataClassRepositoryCache $dataClassRepositoryCache, RightsService $rightsService
    )
    {
        parent::__construct(
            $classnameUtilities, $configurationService, $storageUnitRepository, $translator,
            $packageBundlesCacheService, $packageFactory, $registrationService, $systemPathBuilder, $dependencyVerifier,
            $dependencyVerifierRenderer, $context
        );

        $this->dataClassRepositoryCache = $dataClassRepositoryCache;
        $this->rightsService = $rightsService;
    }

    /**
     * @throws \Exception
     */
    public function extra(array $formValues): bool
    {
        $translator = $this->getTranslator();

        // Update the default settings to the database
        if (!$this->update_settings($formValues))
        {
            return false;
        }
        else
        {
            $this->add_message(
                self::TYPE_NORMAL, $translator->trans(
                'ObjectsAdded', ['OBJECTS' => $translator->trans('DefaultSettings', [], Manager::CONTEXT)],
                StringUtilities::LIBRARIES
            )
            );
        }

        if (!$this->getRightsService()->createRoot())
        {
            return false;
        }
        else
        {
            $this->add_message(
                self::TYPE_NORMAL, $translator->trans(
                'ObjectCreated', ['OBJECT' => $translator->trans('RightsTree, [], Manager::CONTEXT')],
                StringUtilities::LIBRARIES
            )
            );
        }

        return true;
    }

    protected function getDataClassRepositoryCache(): DataClassRepositoryCache
    {
        return $this->dataClassRepositoryCache;
    }

    protected function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    /**
     * @throws \Exception
     */
    public function update_settings(array $values): bool
    {
        $settings = [];
        $settings[] = ['Chamilo\Core\Admin', 'site_name', $values['site_name']];
        $settings[] = ['Chamilo\Core\Admin', 'platform_language', $values['platform_language']];
        $settings[] = ['Chamilo\Core\Admin', 'version', '1.0'];
        $settings[] = ['Chamilo\Core\Admin', 'theme', 'Aqua'];

        $settings[] = ['Chamilo\Core\Admin', 'institution', $values['organization_name']];
        $settings[] = ['Chamilo\Core\Admin', 'institution_url', $values['organization_url']];

        $settings[] = ['Chamilo\Core\Admin', 'show_administrator_data', 'true'];
        $settings[] = ['Chamilo\Core\Admin', 'administrator_firstname', $values['admin_firstname']];
        $settings[] = ['Chamilo\Core\Admin', 'administrator_surname', $values['admin_surname']];
        $settings[] = ['Chamilo\Core\Admin', 'administrator_email', $values['admin_email']];
        $settings[] = ['Chamilo\Core\Admin', 'administrator_telephone', $values['admin_phone']];

        $this->getDataClassRepositoryCache()->truncate(Setting::class);

        foreach ($settings as $setting)
        {
            if (!$this->getConfigurationService()->updateSettingFromParameters($setting[0], $setting[1], $setting[2]))
            {
                return false;
            }
        }

        return true;
    }
}
