<?php
namespace Chamilo\Libraries\Service\Resource;

use Chamilo\Core\Admin\Service\PackageBundlesCacheService;
use Chamilo\Core\Admin\Storage\DataClass\Package;
use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use stdClass;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @package Chamilo\Libraries\Service\Resource
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ResourceGenerator
{
    protected Filesystem $filesystem;

    private PackageBundlesCacheService $packageBundlesCacheService;

    private SystemPathBuilder $systemPathBuilder;

    public function __construct(
        PackageBundlesCacheService $packageBundlesCacheService, SystemPathBuilder $systemPathBuilder,
        Filesystem $filesystem
    )
    {
        $this->packageBundlesCacheService = $packageBundlesCacheService;
        $this->systemPathBuilder = $systemPathBuilder;
        $this->filesystem = $filesystem;
    }

    /**
     * @param \stdClass $resourceDefinition
     * @param string[][] $resourceFiles
     * @param \Chamilo\Core\Admin\Storage\DataClass\Package $package
     */
    protected function addResourceDefinitiontoResourceFiles(
        stdClass $resourceDefinition, array &$resourceFiles, Package $package
    ): void
    {
        $path = $this->getSystemPathBuilder()->namespaceToFullPath($package->getContext());

        if (is_array($resourceDefinition->input)) {
            foreach ($resourceDefinition->input as $resourceDefinitionFile) {
                $resourceFiles[$resourceDefinition->output][] =
                    $path . $this->parseResourcePath($resourceDefinitionFile);
            }
        }
        else {
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
        $packageBundlesCacheService = $this->getPackageBundlesCacheService();
        $packages = $packageBundlesCacheService->getPackages();

        $resourceFiles = [];

        foreach ($packages as $package) {
            $this->processPackageResourceDefiniton($resourceFiles, $package);
        }

        return $resourceFiles;
    }

    /**
     * @throws \Exception
     */
    public function generateResources(): void
    {
        $aggregatedResourceFiles = $this->aggregateResources();

        foreach ($aggregatedResourceFiles as $outputPath => $inputPaths) {
            $this->writeResource($outputPath, $inputPaths);
        }
    }

    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    public function getPackageBundlesCacheService(): PackageBundlesCacheService
    {
        return $this->packageBundlesCacheService;
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
    protected function processPackageResourceDefiniton(array &$resourceFiles, Package $package): void
    {
        foreach ($package->getResources() as $resourceDefinition) {
            $this->addResourceDefinitiontoResourceFiles($resourceDefinition, $resourceFiles, $package);
        }
    }

    /**
     * @param string[] $inputPaths
     */
    protected function writeResource(string $outputPath, array $inputPaths): void
    {
        $basePath = $this->getSystemPathBuilder()->getBasePath();
        $baseWebPath = realpath($basePath . '..') . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR;

        $fullOutputSourcePath = $basePath . $this->parseResourcePath($outputPath);
        $fullOutputWebPath = str_replace($basePath, $baseWebPath, $fullOutputSourcePath);

        if ($this->isOutputPathDirectory($fullOutputWebPath)) {
            $this->writeResourcesFolder($fullOutputWebPath, $inputPaths);
        }
        else {
            $this->writeResourcesFile($fullOutputWebPath, $inputPaths);
        }
    }

    /**
     * @param string[] $inputPaths
     */
    protected function writeResourcesFile(string $outputPath, array $inputPaths): void
    {
        if (count($inputPaths) == 1) {
            $this->getFilesystem()->copy($inputPaths[0], $outputPath, true);
        }
        else {
            $resourceContent = [];

            foreach ($inputPaths as $inputPath) {
                $resourceContent[] = file_get_contents($inputPath);
            }

            $this->getFilesystem()->dumpFile($outputPath, implode(PHP_EOL, $resourceContent));
        }
    }

    /**
     * @param string[] $inputPaths
     */
    protected function writeResourcesFolder(string $outputPath, array $inputPaths): void
    {
        foreach ($inputPaths as $inputPath) {
            $this->getFilesystem()->mirror($inputPath, $outputPath);
        }
    }
}
