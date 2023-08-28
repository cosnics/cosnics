<?php
namespace Chamilo\Core\Group\Package;

use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Configuration\Service\ConfigurationService;
use Chamilo\Configuration\Service\RegistrationService;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Storage\DataManager\Repository\StorageUnitRepository;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Group\Package
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{
    public const CONTEXT = Manager::CONTEXT;

    protected GroupService $groupService;

    public function __construct(
        ClassnameUtilities $classnameUtilities, ConfigurationService $configurationService,
        StorageUnitRepository $storageUnitRepository, Translator $translator,
        PackageBundlesCacheService $packageBundlesCacheService, PackageFactory $packageFactory,
        RegistrationService $registrationService, SystemPathBuilder $systemPathBuilder, string $context,
        GroupService $groupService
    )
    {
        parent::__construct(
            $classnameUtilities, $configurationService, $storageUnitRepository, $translator,
            $packageBundlesCacheService, $packageFactory, $registrationService, $systemPathBuilder, $context
        );

        $this->groupService = $groupService;
    }

    public function createRootGroup(array $formValues): bool
    {
        $group = new Group();

        $group->set_name($formValues['organization_name']);
        $group->setParentId('0');
        $group->set_code(strtolower($formValues['organization_name']));

        return $this->getGroupService()->createGroup($group);
    }

    public function extra(array $formValues): bool
    {
        if (!$this->createRootGroup($formValues))
        {
            return false;
        }

        return true;
    }

    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }
}
