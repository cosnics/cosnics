<?php
namespace Chamilo\Libraries\Architecture\Resource;

use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Configuration\Service\PackageContextSequencer;
use Chamilo\Libraries\File\SystemPathBuilder;
use stdClass;

/**
 * @package Chamilo\Libraries\Architecture\Resource
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ResourceGenerator
{

    protected \Symfony\Component\Filesystem\Filesystem $filesystem;

    private PackageContextSequencer $packageContextSequencer;

    private PlatformPackageBundles $platformPackageBundles;

    private SystemPathBuilder $systemPathBuilder;

    public function __construct(
        PlatformPackageBundles $platformPackageBundles, PackageContextSequencer $packageContextSequencer,
        SystemPathBuilder $systemPathBuilder, \Symfony\Component\Filesystem\Filesystem $filesystem
    )
    {
        $this->platformPackageBundles = $platformPackageBundles;
        $this->packageContextSequencer = $packageContextSequencer;
        $this->systemPathBuilder = $systemPathBuilder;
        $this->filesystem = $filesystem;
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
        $path = $this->getSystemPathBuilder()->namespaceToFullPath($package->get_context());

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

    /**
     * @return string[][]
     * @throws \Exception
     */
    protected function aggregateResources(): array
    {
        $orderedPackageContexts = $this->getPackageContextSequencer()->sequencePackageContexts(
            $this->getPlatformPackageBundles()->get_packages_contexts()
        );

        $packages = $this->getPlatformPackageBundles()->get_packages();

        $resourceFiles = [];

        foreach ($orderedPackageContexts as $orderedPackageContext)
        {
            $this->processPackageResourceDefiniton($resourceFiles, $packages[$orderedPackageContext]);
        }

        return $resourceFiles;
    }

    /**
     * @throws \Exception
     */
    public function generateResources()
    {
        $aggregatedResourceFiles = $this->aggregateResources();

        foreach ($aggregatedResourceFiles as $outputPath => $inputPaths)
        {
            $this->writeResource($outputPath, $inputPaths);
        }
    }

    public function getFilesystem(): \Symfony\Component\Filesystem\Filesystem
    {
        return $this->filesystem;
    }

    public function getPackageContextSequencer(): PackageContextSequencer
    {
        return $this->packageContextSequencer;
    }

    public function getPlatformPackageBundles(): PlatformPackageBundles
    {
        return $this->platformPackageBundles;
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }

    protected function isOutputPathDirectory(string $outputPath): bool
    {
        return $outputPath[- 1] == DIRECTORY_SEPARATOR;
    }

    protected function parseResourcePath(string $resourcePath): string
    {
        return str_replace('/', DIRECTORY_SEPARATOR, $resourcePath);
    }

    /**
     * @param string[][] $resourceFiles
     */
    protected function processPackageResourceDefiniton(array &$resourceFiles, Package $package)
    {
        foreach ($package->getResources() as $resourceDefinition)
        {
            $this->addResourceDefinitiontoResourceFiles($resourceDefinition, $resourceFiles, $package);
        }
    }

    /**
     * @param string[] $inputPaths
     */
    protected function writeResource(string $outputPath, array $inputPaths)
    {
        $basePath = $this->getSystemPathBuilder()->getBasePath();
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
     * @param string[] $inputPaths
     */
    protected function writeResourcesFile(string $outputPath, array $inputPaths)
    {
        if (count($inputPaths) == 1)
        {
            $this->getFilesystem()->copy($inputPaths[0], $outputPath, true);
        }
        else
        {
            $resourceContent = [];

            foreach ($inputPaths as $inputPath)
            {
                $resourceContent[] = file_get_contents($inputPath);
            }

            $this->getFilesystem()->dumpFile($outputPath, implode(PHP_EOL, $resourceContent));
        }
    }

    /**
     * @param string[] $inputPaths
     */
    protected function writeResourcesFolder(string $outputPath, array $inputPaths)
    {
        foreach ($inputPaths as $inputPath)
        {
            $this->getFilesystem()->mirror($inputPath, $outputPath);
        }
    }

}
