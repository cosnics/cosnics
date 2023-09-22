<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Action;

use Chamilo\Configuration\Package\Properties\Dependencies\DependencyVerifier;
use Chamilo\Configuration\Package\Properties\Dependencies\DependencyVerifierRenderer;
use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Configuration\Service\ConfigurationService;
use Chamilo\Configuration\Service\RegistrationService;
use Chamilo\Core\Metadata\Manager;
use Chamilo\Core\Metadata\Relation\Service\RelationService;
use Chamilo\Core\Metadata\Schema\Storage\DataManager;
use Chamilo\Core\Metadata\Storage\DataClass\RelationInstance;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\DataManager\Repository\StorageUnitRepository;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Action
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Installer extends \Chamilo\Core\Metadata\Action\Installer
{
    protected DataClassRepositoryCache $dataClassRepositoryCache;

    public function __construct(
        ClassnameUtilities $classnameUtilities, ConfigurationService $configurationService,
        StorageUnitRepository $storageUnitRepository, Translator $translator,
        PackageBundlesCacheService $packageBundlesCacheService, PackageFactory $packageFactory,
        RegistrationService $registrationService, SystemPathBuilder $systemPathBuilder,
        DependencyVerifier $dependencyVerifier, DependencyVerifierRenderer $dependencyVerifierRenderer, string $context,
        DataClassRepositoryCache $dataClassRepositoryCache, array $propertyProviderTypes = []
    )
    {
        parent::__construct(
            $classnameUtilities, $configurationService, $storageUnitRepository, $translator,
            $packageBundlesCacheService, $packageFactory, $registrationService, $systemPathBuilder, $dependencyVerifier,
            $dependencyVerifierRenderer, $context, $propertyProviderTypes
        );

        $this->dataClassRepositoryCache = $dataClassRepositoryCache;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function extra(array $formValues): bool
    {
        if (!parent::extra($formValues))
        {
            return false;
        }

        if (!$this->linkToSchemas())
        {
            return $this->failed($this->getTranslator()->trans('ContentObjectSchemaLinkFailed', [], Manager::CONTEXT));
        }

        return true;
    }

    public function getContentObjectType(): string
    {
        $namespace = static::CONTEXT;
        $classNameUtilities = ClassnameUtilities::getInstance();
        $packageNamespace = $classNameUtilities->getNamespaceParent($namespace, 5);
        $packageName = $classNameUtilities->getPackageNameFromNamespace($packageNamespace);

        return $packageNamespace . '\Storage\DataClass\\' . $packageName;
    }

    protected function getDataClassRepositoryCache(): DataClassRepositoryCache
    {
        return $this->dataClassRepositoryCache;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function linkToSchemas(): bool
    {
        $schemaNamespaces = ['dc', 'ct'];
        $this->getDataClassRepositoryCache()->truncate(Schema::class);

        $relationService = new RelationService();
        $relation = $relationService->getRelationByName('isAvailableFor');

        foreach ($schemaNamespaces as $schemaNamespace)
        {
            $schema = DataManager::retrieveSchemaByNamespace($schemaNamespace);

            $relationInstance = new RelationInstance();
            $relationInstance->set_source_type(Schema::class);
            $relationInstance->set_source_id($schema->get_id());
            $relationInstance->set_target_type($this->getContentObjectType());
            $relationInstance->set_target_id(0);
            $relationInstance->set_relation_id($relation->get_id());
            $relationInstance->set_user_id(0);
            $relationInstance->set_creation_date(time());

            if (!$relationInstance->create())
            {
                return false;
            }
        }

        return true;
    }
}