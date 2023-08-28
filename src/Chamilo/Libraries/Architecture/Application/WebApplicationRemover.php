<?php
namespace Chamilo\Libraries\Architecture\Application;

use Chamilo\Configuration\Package\Action\Remover;
use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Configuration\Service\ConfigurationService;
use Chamilo\Configuration\Service\RegistrationService;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Service\Renderer\ApplicationItemRenderer;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Storage\DataManager\Repository\StorageUnitRepository;
use Symfony\Component\Translation\Translator;

/**
 * Base class for specific removal extensions of web applications
 *
 * @package Chamilo\Libraries\Architecture\Application
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class WebApplicationRemover extends Remover
{

    protected CachedItemService $itemService;

    public function __construct(
        ClassnameUtilities $classnameUtilities, ConfigurationService $configurationService,
        StorageUnitRepository $storageUnitRepository, Translator $translator,
        PackageBundlesCacheService $packageBundlesCacheService, PackageFactory $packageFactory,
        RegistrationService $registrationService, SystemPathBuilder $systemPathBuilder, string $context,
        CachedItemService $itemService
    )
    {
        parent::__construct(
            $classnameUtilities, $configurationService, $storageUnitRepository, $translator,
            $packageBundlesCacheService, $packageFactory, $registrationService, $systemPathBuilder, $context
        );

        $this->itemService = $itemService;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function extra(): bool
    {
        $itemService = $this->getItemService();

        $items = $itemService->findApplicationItems();

        foreach ($items as $item)
        {
            if ($item->getSetting(ApplicationItemRenderer::CONFIGURATION_APPLICATION) == $this->getContext())
            {
                if (!$itemService->deleteItem($item))
                {
                    return false;
                }
            }
        }

        return true;
    }

    public function getItemService(): CachedItemService
    {
        return $this->itemService;
    }
}
