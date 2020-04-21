<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\ORM;

use Chamilo\Libraries\File\Filesystem;
use Doctrine\ORM\EntityManager;
use Chamilo\Libraries\Cache\FileBasedCacheService;
use RuntimeException;

/**
 * Manages the cache for the doctrine ORM proxies
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\ORM
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DoctrineProxyCacheService extends FileBasedCacheService
{

    /**
     * The EntityManager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Constructor
     *
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\FileBasedCacheService::warmUp()
     */
    public function warmUp()
    {
        if (! is_dir($proxyCacheDir = $this->entityManager->getConfiguration()->getProxyDir()))
        {
            if (! Filesystem::create_dir($proxyCacheDir))
            {
                throw new RuntimeException(
                    sprintf('Unable to create the Doctrine Proxy directory "%s".', $proxyCacheDir));
            }
        }
        elseif (! is_writable($proxyCacheDir))
        {
            throw new RuntimeException(
                sprintf(
                    'The Doctrine Proxy directory "%s" is not writeable for the current system user.',
                    $proxyCacheDir));
        }

        if ($this->entityManager->getConfiguration()->getAutoGenerateProxyClasses())
        {
            return;
        }

        $classes = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $this->entityManager->getProxyFactory()->generateProxyClasses($classes);
    }

    /**
     * Returns the path to the cache directory or file
     *
     * @return string
     */
    function getCachePath()
    {
        return $this->entityManager->getConfiguration()->getProxyDir();
    }
}