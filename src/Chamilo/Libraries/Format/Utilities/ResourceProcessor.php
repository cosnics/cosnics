<?php
namespace Chamilo\Libraries\Format\Utilities;

use Chamilo\Configuration\Package\Finder\ResourceBundles;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\PathBuilder;
use Symfony\Component\Console\Output\OutputInterface;
use function file_get_contents;
use const DIRECTORY_SEPARATOR;

/**
 * Processes resources from one or multiple packages
 *
 * @package Chamilo\Libraries\Format\Utilities
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResourceProcessor
{

    /**
     *
     * @var \Chamilo\Libraries\File\PathBuilder
     */
    protected $pathBuilder;

    /**
     * @var ClassnameUtilities
     */
    protected $classnameUtilities;

    /**
     * ResourceProcessor constructor.
     *
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     * @param ClassnameUtilities $classnameUtilities
     */
    public function __construct(PathBuilder $pathBuilder, ClassnameUtilities $classnameUtilities)
    {
        $this->pathBuilder = $pathBuilder;
        $this->classnameUtilities = $classnameUtilities;
    }

    /**
     * Processes the resources for all (or a given set) of packages
     *
     * @param string[] $packageNamespaces
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function processResources(OutputInterface $output, $packageNamespaces = array())
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
            if (! is_dir($resourcesPath))
            {
                throw new \InvalidArgumentException(
                    sprintf('The given package %s does not have a valid resources path', $packageNamespace));
            }

            $this->processImages($packageNamespace, $basePath, $baseWebPath);
            $this->processCss($packageNamespace, $basePath, $baseWebPath);
            $this->processJavascript($packageNamespace, $basePath, $baseWebPath);
            $this->processCustomConfig($packageNamespace, $basePath, $baseWebPath);

            $output->writeln('Processed resources for: ' . $packageNamespace);
        }

        if ($processAll)
        {
            $this->processFileExtensions($basePath, $baseWebPath);
            $output->writeln('Processed file extension resources');
        }
    }

    /**
     * Processes the images for a given package
     *
     * @param string $packageNamespace
     * @param string $basePath
     * @param string $baseWebPath
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
     * @param string $packageNamespace
     * @param string $basePath
     * @param string $baseWebPath
     */
    protected function processCss($packageNamespace, $basePath, $baseWebPath)
    {
        $sourceResourceImagePath = $this->pathBuilder->getResourcesPath($packageNamespace) . 'Css' . DIRECTORY_SEPARATOR;
        $webResourceImagePath = str_replace($basePath, $baseWebPath, $sourceResourceImagePath);

        $this->recurseCopy($sourceResourceImagePath, $webResourceImagePath, true);
    }

    /**
     * Processes the javascript for a given package
     *
     * @param string $packageNamespace
     * @param string $basePath
     * @param string $baseWebPath
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
     * @param string $basePath
     * @param string $baseWebPath
     */
    protected function processFileExtensions($basePath, $baseWebPath)
    {
        $sourceResourceImagePath = $this->pathBuilder->getResourcesPath('Chamilo\Configuration') . 'File' .
             DIRECTORY_SEPARATOR;
        $webResourceImagePath = str_replace($basePath, $baseWebPath, $sourceResourceImagePath);

        $this->recurseCopy($sourceResourceImagePath, $webResourceImagePath, true);
    }

    /**
     * @param string $packageNamespace
     * @param string $basePath
     * @param string $baseWebPath
     */
    protected function processCustomConfig(string $packageNamespace, string $basePath, string $baseWebPath)
    {
        $customConfigPath = $this->pathBuilder->getResourcesPath($packageNamespace) . 'Configuration' .
            DIRECTORY_SEPARATOR . 'process_resources.json';

        $relativePackagePath = $this->classnameUtilities->namespaceToPath($packageNamespace);
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

                $this->recurseCopy($inputPath, $outputPath, true);
            }
        }
    }

    /**
     * Wrapper method for recurse copy
     *
     * @param string $sourcePath
     * @param string $targetPath
     * @param boolean $overwrite
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
