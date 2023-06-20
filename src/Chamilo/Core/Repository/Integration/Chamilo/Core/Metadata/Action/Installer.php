<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Action;

use Chamilo\Core\Metadata\Manager;
use Chamilo\Core\Metadata\Relation\Service\RelationService;
use Chamilo\Core\Metadata\Schema\Storage\DataManager;
use Chamilo\Core\Metadata\Storage\DataClass\RelationInstance;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Action
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Installer extends \Chamilo\Core\Metadata\Action\Installer
{

    public function extra(): bool
    {
        if (!parent::extra())
        {
            return false;
        }

        if (!$this->linkToSchemas())
        {
            return $this->failed(Translation::get('ContentObjectSchemaLinkFailed', null, Manager::CONTEXT));
        }

        return true;
    }

    public function getContentObjectType()
    {
        $namespace = static::CONTEXT;
        $classNameUtilities = ClassnameUtilities::getInstance();
        $packageNamespace = $classNameUtilities->getNamespaceParent($namespace, 5);
        $packageName = $classNameUtilities->getPackageNameFromNamespace($packageNamespace);

        return $packageNamespace . '\Storage\DataClass\\' . $packageName;
    }

    /**
     * @return \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache
     */
    protected function getDataClassRepositoryCache()
    {
        return $this->getService(
            DataClassRepositoryCache::class
        );
    }

    /**
     * @param string $serviceName
     *
     * @return object
     * @throws \Exception
     */
    protected function getService(string $serviceName)
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            $serviceName
        );
    }

    /**
     * @return bool
     */
    public function linkToSchemas()
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