<?php
namespace Chamilo\Core\Metadata\Action;

use Chamilo\Configuration\Package\Properties\Dependencies\DependencyVerifier;
use Chamilo\Configuration\Package\Properties\Dependencies\DependencyVerifierRenderer;
use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Configuration\Service\ConfigurationService;
use Chamilo\Configuration\Service\RegistrationService;
use Chamilo\Core\Metadata\Manager;
use Chamilo\Core\Metadata\Storage\DataClass\ProviderRegistration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Storage\DataManager\Repository\StorageUnitRepository;
use Symfony\Component\Translation\Translator;

/**
 * Extension of the generic installer for metadata integrations
 *
 * @package Chamilo\Core\Metadata\Action
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    /**
     * @var string[]
     */
    protected array $propertyProviderTypes;

    public function __construct(
        ClassnameUtilities $classnameUtilities, ConfigurationService $configurationService,
        StorageUnitRepository $storageUnitRepository, Translator $translator,
        PackageBundlesCacheService $packageBundlesCacheService, PackageFactory $packageFactory,
        RegistrationService $registrationService, SystemPathBuilder $systemPathBuilder,
        DependencyVerifier $dependencyVerifier, DependencyVerifierRenderer $dependencyVerifierRenderer, string $context,
        array $propertyProviderTypes = []
    )
    {
        parent::__construct(
            $classnameUtilities, $configurationService, $storageUnitRepository, $translator,
            $packageBundlesCacheService, $packageFactory, $registrationService, $systemPathBuilder, $dependencyVerifier,
            $dependencyVerifierRenderer, $context
        );

        $this->propertyProviderTypes = $propertyProviderTypes;
    }

    public function extra(array $formValues): bool
    {
        if (!$this->registerPropertyProviders())
        {
            return $this->failed(
                $this->getTranslator()->trans('PropertyProviderRegistrationFailed', [], Manager::CONTEXT)
            );
        }

        return true;
    }

    /**
     * @return string[]
     */
    public function getPropertyProviderTypes(): array
    {
        return $this->propertyProviderTypes;
    }

    public function registerPropertyProviders(): bool
    {
        $translator = $this->getTranslator();

        foreach ($this->getPropertyProviderTypes() as $propertyProviderType)
        {
            $propertyProvider = new $propertyProviderType();

            $entityType = $propertyProvider->getEntityType();
            $entityProperties = $propertyProvider->getAvailableProperties();

            foreach ($entityProperties as $entityProperty)
            {
                $propertyRegistration = new ProviderRegistration();
                $propertyRegistration->set_entity_type($entityType);
                $propertyRegistration->set_provider_class($propertyProviderType);
                $propertyRegistration->set_property_name($entityProperty);

                if (!$propertyRegistration->create())
                {
                    $this->add_message(
                        self::TYPE_ERROR, $translator->trans(
                        'EntityPropertyRegistrationFailed', [
                        'ENTITY' => $entityType,
                        'PROVIDER_CLASS' => $propertyProviderType,
                        'PROPERTY_NAME' => $entityProperty
                    ], Manager::CONTEXT
                    )
                    );

                    return false;
                }
                else
                {
                    $this->add_message(
                        self::TYPE_NORMAL, $translator->trans(
                        'EntityPropertyRegistrationAdded', [
                        'ENTITY' => $entityType,
                        'PROVIDER_CLASS' => $propertyProviderType,
                        'PROPERTY_NAME' => $entityProperty
                    ], Manager::CONTEXT
                    )
                    );
                }
            }
        }

        return true;
    }
}
