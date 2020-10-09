<?php
namespace Chamilo\Libraries\Architecture\Resource;

use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Configuration\Service\PackageContextSequencer;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\PathBuilder;

/**
 * @package Chamilo\Libraries\Architecture\Resource
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ResourceGenerator
{
    /**
     * @var \Chamilo\Configuration\Package\PlatformPackageBundles
     */
    private $platformPackageBundles;

    /**
     * @var \Chamilo\Configuration\Service\PackageContextSequencer
     */
    private $packageContextSequencer;

    /**
     * @var \Chamilo\Libraries\File\PathBuilder
     */
    private $pathBuilder;

    /**
     * @param \Chamilo\Configuration\Package\PlatformPackageBundles $platformPackageBundles
     * @param \Chamilo\Configuration\Service\PackageContextSequencer $packageContextSequencer
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     */
    public function __construct(
        PlatformPackageBundles $platformPackageBundles, PackageContextSequencer $packageContextSequencer,
        PathBuilder $pathBuilder
    )
    {
        $this->platformPackageBundles = $platformPackageBundles;
        $this->packageContextSequencer = $packageContextSequencer;
        $this->pathBuilder = $pathBuilder;
    }

    protected function aggregateResourceFiles()
    {
        $orderedPackageContexts = $this->getPackageContextSequencer()->sequencePackageContexts(
            $this->getPlatformPackageBundles()->get_packages_contexts()
        );

        $packages = $this->getPlatformPackageBundles()->get_packages();

        $resourceFiles = array();

        foreach ($orderedPackageContexts as $orderedPackageContext)
        {
            $this->processPackageResourceDefiniton($resourceFiles, $packages[$orderedPackageContext]);
        }

        return $resourceFiles;
    }

    public function generateResourceFiles()
    {
        $aggregatedResourceFiles = $this->aggregateResourceFiles();

        foreach ($aggregatedResourceFiles as $aggregatedResourceFileName => $aggregatedResourceFilePaths)
        {
            $this->writeResource($aggregatedResourceFileName, $aggregatedResourceFilePaths);
        }
    }

    /**
     * @return \Chamilo\Configuration\Service\PackageContextSequencer
     */
    public function getPackageContextSequencer(): PackageContextSequencer
    {
        return $this->packageContextSequencer;
    }

    /**
     * @param \Chamilo\Configuration\Service\PackageContextSequencer $packageContextSequencer
     */
    public function setPackageContextSequencer(PackageContextSequencer $packageContextSequencer)
    {
        $this->packageContextSequencer = $packageContextSequencer;
    }

    /**
     * @return \Chamilo\Libraries\File\PathBuilder
     */
    public function getPathBuilder(): PathBuilder
    {
        return $this->pathBuilder;
    }

    /**
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     */
    public function setPathBuilder(PathBuilder $pathBuilder)
    {
        $this->pathBuilder = $pathBuilder;
    }

    /**
     * @return \Chamilo\Configuration\Package\PlatformPackageBundles
     */
    public function getPlatformPackageBundles(): PlatformPackageBundles
    {
        return $this->platformPackageBundles;
    }

    /**
     * @param \Chamilo\Configuration\Package\PlatformPackageBundles $platformPackageBundles
     */
    public function setPlatformPackageBundles(PlatformPackageBundles $platformPackageBundles)
    {
        $this->platformPackageBundles = $platformPackageBundles;
    }

    /**
     * @param string $resourcePath
     *
     * @return string
     */
    protected function parseResourcePath($resourcePath)
    {
        return str_replace('/', DIRECTORY_SEPARATOR, $resourcePath);
    }

    /**
     * @param string[][] $resourceFiles
     * @param \Chamilo\Configuration\Package\Storage\DataClass\Package $package
     */
    protected function processPackageResourceDefiniton(array &$resourceFiles, Package $package)
    {
        if ($package instanceof Package)
        {
            $resourceDefinitions = $package->getResources();
            $path = $this->getPathBuilder()->namespaceToFullPath($package->get_context());

            foreach ($resourceDefinitions as $resourceDefinition)
            {
                if (isset($resourceDefinition->themes))
                {
                    foreach ($resourceDefinition->themes as $themeResourceDefinition)
                    {
                        foreach ($themeResourceDefinition->input as $themeResourceDefinitionFile)
                        {

                            $resourceFiles[$themeResourceDefinition->output][] =
                                $path . $this->parseResourcePath($themeResourceDefinitionFile);
                        }
                    }
                }
                else
                {
                    foreach ($resourceDefinition->input as $resourceDefinitionFile)
                    {
                        $resourceFiles[$resourceDefinition->output][] =
                            $path . $this->parseResourcePath($resourceDefinitionFile);
                    }
                }
            }
        }
    }

    protected function writeResource($destinationPath, $sourcePaths)
    {
        $basePath = $this->getPathBuilder()->getBasePath();
        $baseWebPath = realpath($basePath . '..') . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR;

        $fullDestinationSourcePath = $basePath . $this->parseResourcePath($destinationPath);
        $fullDestinationWebPath = str_replace($basePath, $baseWebPath, $fullDestinationSourcePath);

        if (is_dir($destinationPath))
        {
            $this->writeResourcesFolder($fullDestinationWebPath, $sourcePaths);
        }
        else
        {
            $this->writeResourcesFile($fullDestinationWebPath, $sourcePaths);
        }
    }

    protected function writeResourcesFile($destinationPath, $sourcePaths)
    {
        if (count($sourcePaths) == 1)
        {
            Filesystem::copy_file($sourcePaths[0], $destinationPath, true);
        }
        else
        {
            $resourceContent = array();

            foreach ($sourcePaths as $sourcePath)
            {
                $resourceContent[] = file_get_contents($sourcePath);
            }

            Filesystem::write_to_file($destinationPath, implode(PHP_EOL, $resourceContent));
        }
    }

    protected function writeResourcesFolder($destinationPath, $sourcePaths)
    {
        foreach ($sourcePaths as $sourcePath)
        {
            Filesystem::recurse_copy($sourcePath, $destinationPath, true);
        }
    }

}
