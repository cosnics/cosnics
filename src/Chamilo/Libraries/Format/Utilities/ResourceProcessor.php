<?php

namespace Chamilo\Libraries\Format\Utilities;

use Chamilo\Configuration\Package\Finder\ResourceBundles;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\PathBuilder;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Processes resources from one or multiple packages
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResourceProcessor
{
    /**
     * @var PathBuilder
     */
    protected $pathBuilder;

    /**
     * ResourceProcessor constructor.
     *
     * @param PathBuilder $pathBuilder
     */
    public function __construct(PathBuilder $pathBuilder)
    {
        $this->pathBuilder = $pathBuilder;
    }

    /**
     * Processes the resources for all (or a given set) of packages
     *
     * @param array $packageNamespaces
     * @param OutputInterface $output
     */
    public function processResources($packageNamespaces = array(), OutputInterface $output)
    {
        $processAll = false;

        if (empty($packageNamespaces))
        {
            $packageNamespaces = $this->getDefaultNamespaces();
            $processAll = true;
        }

        $basePath = $this->pathBuilder->getBasePath();
        $baseWebPath = realpath($basePath . '..') . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR;

        foreach ($packageNamespaces as $packageNamespace)
        {
            $resourcesPath = $this->pathBuilder->getResourcesPath($packageNamespace);
            if (!is_dir($resourcesPath))
            {
                throw new \InvalidArgumentException(
                    sprintf('The given package %s does not have a valid resources path', $packageNamespace)
                );
            }

            $this->processImages($packageNamespace, $basePath, $baseWebPath);
            $this->processCss($packageNamespace, $basePath, $baseWebPath);
            $this->processJavascript($packageNamespace, $basePath, $baseWebPath);
            $output->writeln('Processed resources for: ' . $packageNamespace);
        }

        if($processAll)
        {
            $this->processFileExtensions($basePath, $baseWebPath);
            $output->writeln('Processed file extension resources');
        }
    }

    /**
     * Processes the images for a given package
     *
     * @param $packageNamespace
     * @param $basePath
     * @param $baseWebPath
     */
    protected function processImages($packageNamespace, $basePath, $baseWebPath)
    {
        $sourceResourceImagePath = $this->pathBuilder->getResourcesPath($packageNamespace) . 'Images' .
            DIRECTORY_SEPARATOR;
        $webResourceImagePath = str_replace($basePath, $baseWebPath, $sourceResourceImagePath);

        $this->recurseCopy($sourceResourceImagePath, $webResourceImagePath, true);
    }

    /**
     * Processes the css for a given package
     *
     * @param $packageNamespace
     * @param $basePath
     * @param $baseWebPath
     */
    protected function processCss($packageNamespace, $basePath, $baseWebPath)
    {
        $sourceResourceImagePath = $this->pathBuilder->getResourcesPath($packageNamespace) . 'Css' .
            DIRECTORY_SEPARATOR;
        $webResourceImagePath = str_replace($basePath, $baseWebPath, $sourceResourceImagePath);

        $this->recurseCopy($sourceResourceImagePath, $webResourceImagePath, true);
    }

    /**
     * Processes the javascript for a given package
     *
     * @param $packageNamespace
     * @param $basePath
     * @param $baseWebPath
     */
    protected function processJavascript($packageNamespace, $basePath, $baseWebPath)
    {
        $sourceResourceJavascriptPath = $this->pathBuilder->getResourcesPath($packageNamespace) . 'Javascript' .
            DIRECTORY_SEPARATOR;
        $webResourceJavascriptPath = str_replace($basePath, $baseWebPath, $sourceResourceJavascriptPath);

        $this->recurseCopy($sourceResourceJavascriptPath, $webResourceJavascriptPath, true);
    }

    /**
     * Processes the file extensions for a given package
     *
     * @param $basePath
     * @param $baseWebPath
     */
    protected function processFileExtensions($basePath, $baseWebPath)
    {
        $sourceResourceImagePath = $this->pathBuilder->getResourcesPath('Chamilo\Configuration') . 'File' .
            DIRECTORY_SEPARATOR;
        $webResourceImagePath = str_replace($basePath, $baseWebPath, $sourceResourceImagePath);

        $this->recurseCopy($sourceResourceImagePath, $webResourceImagePath, true);
    }

    /**
     * Wrapper method for recurse copy
     * 
     * @param string $sourcePath
     * @param string $targetPath
     * @param bool $overwrite
     */
    protected function recurseCopy($sourcePath, $targetPath, $overwrite = false)
    {
        Filesystem::recurse_copy($sourcePath, $targetPath, $overwrite);
    }

    /**
     * Returns an array of the default namespaces
     *
     * @return string[]
     */
    protected function getDefaultNamespaces()
    {
        $resourceBundles = new ResourceBundles(PackageList::ROOT);
        $packageNamespaces = $resourceBundles->getPackageNamespaces();

        return $packageNamespaces;
    }
}