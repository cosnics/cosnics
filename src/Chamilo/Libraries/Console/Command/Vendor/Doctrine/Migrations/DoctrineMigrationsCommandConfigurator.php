<?php
namespace Chamilo\Libraries\Console\Command\Vendor\Doctrine\Migrations;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Doctrine\DBAL\Configuration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Metadata\AvailableMigrationsSet;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
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
     * @param \Doctrine\Migrations\Configuration\Configuration $configuration
     * @param string $packagePath
     */
    public function configure(Configuration $configuration, $packagePath)
    {
        $migrationsFolder = $packagePath . '/migrations';

        if (!file_exists($migrationsFolder))
        {
            Filesystem::create_dir($migrationsFolder);
        }

        $namespace = $this->getNamespaceFromPackagePath($packagePath);

        $configuration->addMigrationsDirectory($namespace, $migrationsFolder);

        $tableMetaDataStorageConfiguration = new TableMetadataStorageConfiguration();
        $tableMetaDataStorageConfiguration->setTableName('libraries_migrations');

        $configuration->setMetadataStorageConfiguration($tableMetaDataStorageConfiguration);

        $migrations = DependencyFactory::fromConnection()->getMigrationRepository()->getMigrations();

        $this->injectContainerToMigrations($migrations);
    }

    /**
     * Returns the namespace from a given package path
     *
     * @param string $packagePath
     *
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

    /**
     * Injects the container to migrations aware of it
     *
     * @param \Doctrine\Migrations\Metadata\AvailableMigrationsSet $availableMigrationsSet
     */
    protected function injectContainerToMigrations(AvailableMigrationsSet $availableMigrationsSet)
    {
        foreach ($availableMigrationsSet->getItems() as $availableMigration)
        {
            $migration = $availableMigration->getMigration();
            if ($migration instanceof ContainerAwareInterface)
            {
                $migration->setContainer($this->container);
            }
        }
    }
}