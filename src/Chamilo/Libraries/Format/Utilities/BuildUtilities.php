<?php
namespace Chamilo\Libraries\Format\Utilities;

use Chamilo\Configuration\Package\Finder\BasicBundlesGenerator;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;
use Composer\Package\Loader\JsonLoader;
use Composer\Script\Event;
use Exception;

/**
 * @package Chamilo\Libraries\Format\Utilities
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BuildUtilities
{

    public static function processComposer(Event $event): void
    {
        $systemPathBuilder = new SystemPathBuilder(new ClassnameUtilities(new StringUtilities()));

        $basicBundlesGenerator = new BasicBundlesGenerator($systemPathBuilder);
        $packageNamespaces = $basicBundlesGenerator->getPackageNamespaces();

        $composer = $event->getComposer();
        $package = $event->getComposer()->getPackage();

        $requires = $package->getRequires();
        $devRequires = $package->getDevRequires();
        $autoload = $package->getAutoload();
        $repositories = $package->getRepositories();

        $repositoryManager = $composer->getRepositoryManager();

        foreach ($packageNamespaces as $packageNamespace)
        {
            $packageComposerPath = $systemPathBuilder->namespaceToFullPath($packageNamespace) . 'composer.json';

            if (file_exists($packageComposerPath))
            {
                $jsonLoader = new JsonLoader(new ArrayLoader());

                try
                {
                    $completePackage = $jsonLoader->load($packageComposerPath);
                }
                catch (Exception)
                {
                    continue;
                }

                // Process require
                foreach ($completePackage->getRequires() as $requireName => $requirePackage)
                {
                    if (!isset($requires[$requireName]))
                    {
                        $requires[$requireName] = $requirePackage;
                    }
                }

                // Process require-dev
                foreach ($completePackage->getDevRequires() as $requireName => $requirePackage)
                {
                    if (!isset($devRequires[$requireName]))
                    {
                        $devRequires[$requireName] = $requirePackage;
                    }
                }

                // Process psr-4 autoload
                $packageAutoloaders = $completePackage->getAutoload();

                if (isset($packageAutoloaders['psr-4']))
                {
                    foreach ($packageAutoloaders['psr-4'] as $autoloaderKey => $autoloaderValue)
                    {
                        if (!isset($autoload['psr-4'][$autoloaderKey]))
                        {
                            $autoload['psr-4'][$autoloaderKey] = $autoloaderValue;
                        }
                    }
                }

                // Process classmap autoload
                if (isset($packageAutoloaders['classmap']))
                {
                    foreach ($packageAutoloaders['classmap'] as $autoloaderValue)
                    {
                        if (!in_array($autoloaderValue, $autoload['classmap']))
                        {
                            $autoload['classmap'][] = $autoloaderValue;
                        }
                    }
                }

                // Process exclude-from-classmap autoload

                if (isset($packageAutoloaders['exclude-from-classmap']))
                {
                    foreach ($packageAutoloaders['exclude-from-classmap'] as $autoloaderValue)
                    {
                        if (!in_array($autoloaderValue, $autoload['exclude-from-classmap']))
                        {
                            $autoload['exclude-from-classmap'][] = $autoloaderValue;
                        }
                    }
                }

                // Process repositories
                foreach ($completePackage->getRepositories() as $repositoryConfig)
                {
                    $repository = $repositoryManager->createRepository($repositoryConfig['type'], $repositoryConfig);
                    $repositoryManager->addRepository($repository);
                    array_unshift($repositories, $repositoryConfig);
                }
            }
        }

        $package->setRequires($requires);
        $package->setDevRequires($devRequires);
        $package->setAutoload($autoload);
        $package->setRepositories($repositories);
    }
}