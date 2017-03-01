<?php
namespace Chamilo\Libraries\Format\Utilities;

use Chamilo\Configuration\Package\Finder\BasicBundles;
use Chamilo\Configuration\Package\Finder\ResourceBundles;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ArrayLoader;
use Composer\Package\Loader\JsonLoader;
use Composer\Script\Event;

class BuildUtilities
{

    public static function processComposer(Event $event)
    {
        $packageBundles = new BasicBundles(PackageList::ROOT);
        $packageNamespaces = $packageBundles->getPackageNamespaces();
        
        $composer = $event->getComposer();
        $package = $event->getComposer()->getPackage();
        
        $requires = $package->getRequires();
        $devRequires = $package->getDevRequires();
        $autoload = $package->getAutoload();
        $repositories = $package->getRepositories();
        
        $repositoryManager = $composer->getRepositoryManager();
        
        foreach ($packageNamespaces as $packageNamespace)
        {
            $packageComposerPath = Path::getInstance()->namespaceToFullPath($packageNamespace) . 'composer.json';
            
            if (file_exists($packageComposerPath))
            {
                $jsonLoader = new JsonLoader(new ArrayLoader());
                $completePackage = $jsonLoader->load($packageComposerPath);
                
                // Process require
                foreach ($completePackage->getRequires() as $requireName => $requirePackage)
                {
                    if (! isset($requires[$requireName]))
                    {
                        $requires[$requireName] = $requirePackage;
                    }
                }
                
                // Process require-dev
                foreach ($completePackage->getDevRequires() as $requireName => $requirePackage)
                {
                    if (! isset($devRequires[$requireName]))
                    {
                        $devRequires[$requireName] = $requirePackage;
                    }
                }
                
                // Process PSR-4 autoload
                $packageAutoloaders = $completePackage->getAutoload();
                
                if (isset($packageAutoloaders['psr-4']))
                {
                    foreach ($packageAutoloaders['psr-4'] as $autoloaderKey => $autoloaderValue)
                    {
                        if (! isset($autoload['psr-4'][$autoloaderKey]))
                        {
                            $autoload['psr-4'][$autoloaderKey] = $autoloaderValue;
                        }
                    }
                }
                
                // Process repositories
                foreach ((array) $completePackage->getRepositories() as $repositoryConfig)
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