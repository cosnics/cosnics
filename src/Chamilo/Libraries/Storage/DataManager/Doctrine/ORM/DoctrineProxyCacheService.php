<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ORM;

use Chamilo\Libraries\Cache\FileBasedCacheService;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Doctrine\ORM\EntityManager;
use Exception;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Manages the cache for the doctrine ORM proxies
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ORM
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DoctrineProxyCacheService extends FileBasedCacheService
{

    private EntityManager $entityManager;

    public function __construct(
        EntityManager $entityManager, ConfigurablePathBuilder $configurablePathBuilder, Filesystem $filesystem
    )
    {
        parent::__construct($configurablePathBuilder, $filesystem);

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

    public function initializeCache()
    {
        $entityManager = $this->getEntityManager();

        if (!is_dir($proxyCacheDir = $entityManager->getConfiguration()->getProxyDir()))
        {
            try
            {
                $this->getFilesystem()->mkdir($proxyCacheDir);
            }
            catch (Exception)
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