<?php
namespace Chamilo\Libraries\Format\Utilities;

use Chamilo\Configuration\Package\Finder\BasicBundles;
use Chamilo\Configuration\Package\Finder\ResourceBundles;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ArrayLoader;
use Composer\Package\Loader\JsonLoader;
use Composer\Script\Event;

/**
 *
 * @package Chamilo\Libraries\Format\Utilities
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BuildUtilities
{

    /**
     *
     * @param \Composer\Script\Event $event
     */
    public static function processResources(Event $event)
    {
        $resourceBundles = new ResourceBundles(PackageList::ROOT);
        $packageNamespaces = $resourceBundles->getPackageNamespaces();

        $basePath = Path::getInstance()->getBasePath();
        $baseWebPath = realpath($basePath . '..') . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR;

        // Copy the resources
        foreach ($packageNamespaces as $packageNamespace)
        {
            // Images
            $sourceResourceImagePath = Path::getInstance()->getResourcesPath($packageNamespace) . 'Images' .
                 DIRECTORY_SEPARATOR;
            $webResourceImagePath = str_replace($basePath, $baseWebPath, $sourceResourceImagePath);
            Filesystem::recurse_copy($sourceResourceImagePath, $webResourceImagePath, true);

            // Css
            $sourceResourceImagePath = Path::getInstance()->getResourcesPath($packageNamespace) . 'Css' .
                 DIRECTORY_SEPARATOR;
            $webResourceImagePath = str_replace($basePath, $baseWebPath, $sourceResourceImagePath);
            Filesystem::recurse_copy($sourceResourceImagePath, $webResourceImagePath, true);

            // Javascript
            $sourceResourceJavascriptPath = Path::getInstance()->getResourcesPath($packageNamespace) . 'Javascript' .
                 DIRECTORY_SEPARATOR;
            $webResourceJavascriptPath = str_replace($basePath, $baseWebPath, $sourceResourceJavascriptPath);
            Filesystem::recurse_copy($sourceResourceJavascriptPath, $webResourceJavascriptPath, true);

            self::processCustomConfig($packageNamespace, $basePath, $baseWebPath);

            $event->getIO()->write('Processed resources for: ' . $packageNamespace);
        }

        // Copy the file extensions
        $sourceResourceImagePath = Path::getInstance()->getResourcesPath('Chamilo\Configuration') . 'File' .
             DIRECTORY_SEPARATOR;
        $webResourceImagePath = str_replace($basePath, $baseWebPath, $sourceResourceImagePath);
        Filesystem::recurse_copy($sourceResourceImagePath, $webResourceImagePath, true);
        $event->getIO()->write('Processed file extension resources');

        // Copy the error pages
        $sourceResourceImagePath = Path::getInstance()->getResourcesPath('Chamilo\Configuration') . 'ErrorPages' .
             DIRECTORY_SEPARATOR;
        $webResourceImagePath = str_replace($basePath, $baseWebPath, $sourceResourceImagePath);
        Filesystem::recurse_copy($sourceResourceImagePath, $webResourceImagePath, true);
        $event->getIO()->write('Processed error pages');
    }

    /**
     * @param string $packageNamespace
     * @param string $basePath
     * @param string $baseWebPath
     */
    protected static function processCustomConfig(string $packageNamespace, string $basePath, string $baseWebPath)
    {
        $customConfigPath = Path::getInstance()->getResourcesPath($packageNamespace) . 'Configuration' .
            DIRECTORY_SEPARATOR . 'process_resources.json';

        $relativePackagePath = ClassnameUtilities::getInstance()->namespaceToPath($packageNamespace);
        $packagePath = $basePath . $relativePackagePath;
        $packageWebPath = $baseWebPath . $relativePackagePath;

        if(file_exists($customConfigPath))
        {
            $customConfig = json_decode(file_get_contents($customConfigPath), true);
            foreach($customConfig as $copyConfig)
            {
                $inputPath = realpath($packagePath . DIRECTORY_SEPARATOR . $copyConfig['input']);
                $outputPath = $packageWebPath . DIRECTORY_SEPARATOR . $copyConfig['output'];

                if(empty($inputPath) || empty($outputPath))
                {
                    continue;
                }

                Filesystem::recurse_copy($inputPath, $outputPath, true);
            }
        }
    }

    /**
     *
     * @param \Composer\Script\Event $event
     */
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
        $config = $package->getConfig();

        $repositoryManager = $composer->getRepositoryManager();

        foreach ($packageNamespaces as $packageNamespace)
        {
            $packageComposerPath = Path::getInstance()->namespaceToFullPath($packageNamespace) . 'composer.json';

            if (file_exists($packageComposerPath))
            {
                $jsonLoader = new JsonLoader(new ArrayLoader());

                try
                {
                    $completePackage = $jsonLoader->load($packageComposerPath);
                }
                catch(\Exception $ex)
                {
                    continue;
                }

//                if(!array_key_exists('cosnics', $completePackage->getExtra()))
//                {
//                    var_dump($packageComposerPath);
//                    continue;
//                }

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

                $config = array_merge_recursive($config, $package->getConfig());

                // Process repositories
                foreach ((array) $completePackage->getRepositories() as $repositoryConfig)
                {
                    $repository = $repositoryManager->createRepository($repositoryConfig['type'], $repositoryConfig);
                    $repositoryManager->addRepository($repository);
                    array_unshift($repositories, $repositoryConfig);
                }
            }
        }
        $package->setConfig($config);
        $package->setRequires($requires);
        $package->setDevRequires($devRequires);
        $package->setAutoload($autoload);
        $package->setRepositories($repositories);

        var_dump($package->getConfig());
    }
}
