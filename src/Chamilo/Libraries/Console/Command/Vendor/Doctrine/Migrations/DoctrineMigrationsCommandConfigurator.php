<?php
namespace Chamilo\Libraries\Console\Command\Vendor\Doctrine\Migrations;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Doctrine\DBAL\Configuration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This class configures the doctrine migrations based on the namespace of a package
 * Class DoctrineMigrationsCommandConfigurer
 *
 * @package Chamilo\Libraries\Console\Command\Vendor\Doctrine\Migrations
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DoctrineMigrationsCommandConfigurator
{

    /**
     * The Dependency Injection Container
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * Constructor
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Configures the doctrine migrations based on the namespace of a package and optionally injects the container
     * where needed
     *
     * @param \Doctrine\DBAL\Migrations\Configuration\Configuration $configuration
     * @param string $packagePath
     */
    public function configure(Configuration $configuration, $packagePath)
    {
        $migrationsFolder = $packagePath . '/migrations';

        if (! file_exists($migrationsFolder))
        {
            Filesystem::create_dir($migrationsFolder);
        }

        $namespace = $this->getNamespaceFromPackagePath($packagePath);

        $configuration->setMigrationsNamespace($namespace);
        $configuration->setMigrationsDirectory($migrationsFolder);
        $configuration->registerMigrationsFromDirectory($migrationsFolder);
        $configuration->setName('Chamilo Migrations for package: ' . $namespace);
        $configuration->setMigrationsTableName('libraries_migrations');

        $this->injectContainerToMigrations($configuration->getMigrations());
    }

    /**
     * Injects the container to migrations aware of it
     *
     * @param \Doctrine\DBAL\Migrations\Version[] $versions
     */
    protected function injectContainerToMigrations(array $versions)
    {
        foreach ($versions as $version)
        {
            $migration = $version->getMigration();
            if ($migration instanceof ContainerAwareInterface)
            {
                $migration->setContainer($this->container);
            }
        }
    }

    /**
     * Returns the namespace from a given package path
     *
     * @param string $packagePath
     * @return string
     */
    protected function getNamespaceFromPackagePath($packagePath)
    {
        $namespace = $packagePath;

        if (strpos($namespace, Path::getInstance()->getBasePath()) !== false)
        {
            $namespace = substr($namespace, Path::getInstance()->getBasePath());
        }

        $namespace = str_replace('/', '\\', $namespace);

        if (strrpos($namespace, '\\') == strlen($namespace) - 1)
        {
            $namespace = substr($namespace, 0, - 1);
        }

        if (strpos($namespace, '\\') == 0)
        {
            $namespace = substr($namespace, 1);
        }

        return $namespace;
    }
}