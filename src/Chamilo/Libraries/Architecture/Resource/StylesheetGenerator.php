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
class StylesheetGenerator
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

    protected function aggregateCssFiles()
    {
        $orderedPackageContexts = $this->getPackageContextSequencer()->sequencePackageContexts(
            $this->getPlatformPackageBundles()->get_packages_contexts()
        );

        $packages = $this->getPlatformPackageBundles()->get_packages();

        $cssFiles = array();

        foreach ($orderedPackageContexts as $orderedPackageContext)
        {
            $this->processPackageCssDefiniton($cssFiles, $packages[$orderedPackageContext]);
        }

        return $cssFiles;
    }

    public function generateStylesheets()
    {
        $aggregatedCssFiles = $this->aggregateCssFiles();

        //        var_dump($aggregatedCssFiles);

        foreach ($aggregatedCssFiles as $aggregatedCssFileName => $aggregatedCssFilePaths)
        {
            $this->writeCssFile($aggregatedCssFileName, $aggregatedCssFilePaths);
        }

        exit;
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
     *
     * @return StylesheetGenerator
     */
    public function setPackageContextSequencer(PackageContextSequencer $packageContextSequencer): StylesheetGenerator
    {
        $this->packageContextSequencer = $packageContextSequencer;

        return $this;
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
     *
     * @return StylesheetGenerator
     */
    public function setPathBuilder(PathBuilder $pathBuilder): StylesheetGenerator
    {
        $this->pathBuilder = $pathBuilder;

        return $this;
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
     *
     * @return StylesheetGenerator
     */
    public function setPlatformPackageBundles(PlatformPackageBundles $platformPackageBundles): StylesheetGenerator
    {
        $this->platformPackageBundles = $platformPackageBundles;

        return $this;
    }

    /**
     * @param string[][] $cssFiles
     * @param \Chamilo\Configuration\Package\Storage\DataClass\Package $package
     */
    public function processPackageCssDefiniton(&$cssFiles, Package $package)
    {
        if ($package instanceof Package)
        {
            $cssDefinitions = $package->getCss();
            $path = $this->getPathBuilder()->namespaceToFullPath($package->get_context());

            foreach ($cssDefinitions as $cssDefinition)
            {
                if (isset($cssDefinition->themes))
                {
                    foreach ($cssDefinition->themes as $themeCssDefinition)
                    {
                        foreach ($themeCssDefinition->input as $themeCssDefinitionFile)
                        {

                            $cssFiles[$themeCssDefinition->output][] =
                                $path . str_replace('/', DIRECTORY_SEPARATOR, $themeCssDefinitionFile);
                        }
                    }
                }
                else
                {
                    foreach ($cssDefinition->input as $cssDefinitionFile)
                    {
                        $cssFiles[$cssDefinition->output][] =
                            $path . str_replace('/', DIRECTORY_SEPARATOR, $cssDefinitionFile);
                    }
                }
            }
        }
    }

    protected function writeCssFile($aggregatedCssFileName, $aggregatedCssFilePaths)
    {
        $cssContent = array();

        foreach ($aggregatedCssFilePaths as $aggregatedCssFilePath)
        {
            $cssContent[] = file_get_contents($aggregatedCssFilePath);
        }

        $aggregatedCssFilePath = $this->getPathBuilder()->getCssPath('Chamilo\Libraries') . $aggregatedCssFileName;

        Filesystem::write_to_file($aggregatedCssFilePath, implode(PHP_EOL, $cssContent));

        $basePath = $this->getPathBuilder()->getBasePath();
        $baseWebPath = realpath($basePath . '..') . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR;

        $webAggregatedCssFilePath = str_replace($basePath, $baseWebPath, $aggregatedCssFilePath);

        Filesystem::copy_file($aggregatedCssFilePath, $webAggregatedCssFilePath, true);
    }

}
