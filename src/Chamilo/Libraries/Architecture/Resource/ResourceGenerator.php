<?php
namespace Chamilo\Libraries\Architecture\Resource;

use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Configuration\Service\PackageContextSequencer;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\PathBuilder;
use stdClass;

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

    /**
     * @param \stdClass $resourceDefinition
     * @param string[][] $resourceFiles
     * @param \Chamilo\Configuration\Package\Storage\DataClass\Package $package
     */
    protected function addResourceDefinitiontoResourceFiles(
        stdClass $resourceDefinition, array &$resourceFiles, Package $package
    )
    {
        $path = $this->getPathBuilder()->namespaceToFullPath($package->get_context());

        if (is_array($resourceDefinition->input))
        {
            foreach ($resourceDefinition->input as $resourceDefinitionFile)
            {
                $resourceFiles[$resourceDefinition->output][] =
                    $path . $this->parseResourcePath($resourceDefinitionFile);
            }
        }
        else
        {
            $resourceFiles[$resourceDefinition->output][] =
                $path . $this->parseResourcePath($resourceDefinition->input);
        }
    }

    protected function aggregateResources()
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

    public function generateResources()
    {
        $aggregatedResourceFiles = $this->aggregateResources();

        foreach ($aggregatedResourceFiles as $outputPath => $inputPaths)
        {
            $this->writeResource($outputPath, $inputPaths);
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
     * @param string $outputPath
     *
     * @return boolean
     */
    protected function isOutputPathDirectory(string $outputPath)
    {
        return $outputPath[- 1] == DIRECTORY_SEPARATOR;
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
            foreach ($package->getResources() as $resourceDefinition)
            {
                $this->addResourceDefinitiontoResourceFiles($resourceDefinition, $resourceFiles, $package);
            }
        }
    }

    /**
     * @param string $outputPath
     * @param string[] $inputPaths
     */
    protected function writeResource($outputPath, $inputPaths)
    {
        $basePath = $this->getPathBuilder()->getBasePath();
        $baseWebPath = realpath($basePath . '..') . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR;

        $fullOutputSourcePath = $basePath . $this->parseResourcePath($outputPath);
        $fullOutputWebPath = str_replace($basePath, $baseWebPath, $fullOutputSourcePath);

        if ($this->isOutputPathDirectory($fullOutputWebPath))
        {
            $this->writeResourcesFolder($fullOutputWebPath, $inputPaths);
        }
        else
        {
            $this->writeResourcesFile($fullOutputWebPath, $inputPaths);
        }
    }

    /**
     * @param string $outputPath
     * @param string[] $inputPaths
     */
    protected function writeResourcesFile($outputPath, $inputPaths)
    {
        if (count($inputPaths) == 1)
        {
            Filesystem::copy_file($inputPaths[0], $outputPath, true);
        }
        else
        {
            $resourceContent = array();

            foreach ($inputPaths as $inputPath)
            {
                $resourceContent[] = file_get_contents($inputPath);
            }

            Filesystem::write_to_file($outputPath, implode(PHP_EOL, $resourceContent));
        }
    }

    /**
     * @param string $outputPath
     * @param string[] $inputPaths
     */
    protected function writeResourcesFolder($outputPath, $inputPaths)
    {
        foreach ($inputPaths as $inputPath)
        {
            Filesystem::recurse_copy($inputPath, $outputPath, true);
        }
    }

}
