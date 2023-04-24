<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ORM;

use Chamilo\Libraries\Cache\FileBasedCacheService;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Filesystem;
use Doctrine\ORM\EntityManager;
use RuntimeException;

/**
 * Manages the cache for the doctrine ORM proxies
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ORM
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class DoctrineProxyCacheService extends FileBasedCacheService
{

    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager, ConfigurablePathBuilder $configurablePathBuilder)
    {
        parent::__construct($configurablePathBuilder);

        $this->entityManager = $entityManager;
    }

    public function getCachePath(): string
    {
        return $this->getEntityManager()->getConfiguration()->getProxyDir();
    }

    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    public function preLoadCacheData()
    {
        $entityManager = $this->getEntityManager();

        if (!is_dir($proxyCacheDir = $entityManager->getConfiguration()->getProxyDir()))
        {
            if (!Filesystem::create_dir($proxyCacheDir))
            {
                throw new RuntimeException(
                    sprintf('Unable to create the Doctrine Proxy directory "%s".', $proxyCacheDir)
                );
            }
        }
        elseif (!is_writable($proxyCacheDir))
        {
            throw new RuntimeException(
                sprintf(
                    'The Doctrine Proxy directory "%s" is not writeable for the current system user.', $proxyCacheDir
                )
            );
        }

        if ($entityManager->getConfiguration()->getAutoGenerateProxyClasses())
        {
            return;
        }

        $classes = $entityManager->getMetadataFactory()->getAllMetadata();
        $entityManager->getProxyFactory()->generateProxyClasses($classes);
    }
}