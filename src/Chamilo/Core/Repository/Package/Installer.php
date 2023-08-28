<?php
namespace Chamilo\Core\Repository\Package;

use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Configuration\Service\ConfigurationService;
use Chamilo\Configuration\Service\RegistrationService;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Quota\Rights\Service\RightsService;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Storage\DataManager\Repository\StorageUnitRepository;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Package
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{
    public const CONTEXT = Manager::CONTEXT;

    protected RightsService $rightsService;

    public function __construct(
        ClassnameUtilities $classnameUtilities, ConfigurationService $configurationService,
        StorageUnitRepository $storageUnitRepository, Translator $translator,
        PackageBundlesCacheService $packageBundlesCacheService, PackageFactory $packageFactory,
        RegistrationService $registrationService, SystemPathBuilder $systemPathBuilder, string $context,
        RightsService $rightsService
    )
    {
        parent::__construct(
            $classnameUtilities, $configurationService, $storageUnitRepository, $translator,
            $packageBundlesCacheService, $packageFactory, $registrationService, $systemPathBuilder, $context
        );

        $this->rightsService = $rightsService;
    }

    public function extra(array $formValues): bool
    {
        $translator = $this->getTranslator();

        if (!$this->getRightsService()->createRoot())
        {
            return false;
        }
        else
        {
            $this->add_message(
                self::TYPE_NORMAL, $translator->trans(
                'ObjectCreated', [
                'OBJECT' => $translator->trans('RightsTree', [], \Chamilo\Core\Repository\Quota\Rights\Manager::CONTEXT)
            ], StringUtilities::LIBRARIES
            )
            );
        }

        return true;
    }

    protected function getRightsService(): RightsService
    {
        return $this->rightsService;
    }
}
