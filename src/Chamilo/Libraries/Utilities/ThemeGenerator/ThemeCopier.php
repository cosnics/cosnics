<?php
namespace Chamilo\Libraries\Utilities\ThemeGenerator;

use Chamilo\Configuration\Package\Finder\ResourceBundles;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;

require_once realpath(__DIR__ . '/../../../../') . '/vendor/autoload.php';

/**
 *
 * @package Chamilo\Libraries\Utilities\ThemeGenerator
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ThemeCopier
{

    /**
     *
     * @var string
     */
    private $sourceTheme;

    /**
     *
     * @var string
     */
    private $targetTheme;

    /**
     *
     * @var boolean
     */
    private $overwriteExisting;

    /**
     *
     * @param string $sourceTheme
     * @param string $targetTheme
     */
    public function __construct($sourceTheme, $targetTheme, $overwriteExisting = false)
    {
        $this->sourceTheme = $sourceTheme;
        $this->targetTheme = $targetTheme;
        $this->overwriteExisting = $overwriteExisting;
    }

    /**
     *
     * @return string
     */
    public function getSourceTheme()
    {
        return $this->sourceTheme;
    }

    /**
     *
     * @param string $sourceTheme
     */
    public function setSourceTheme($sourceTheme)
    {
        $this->sourceTheme = $sourceTheme;
    }

    /**
     *
     * @return string
     */
    public function getTargetTheme()
    {
        return $this->targetTheme;
    }

    /**
     *
     * @param string $targetTheme
     */
    public function setTargetTheme($targetTheme)
    {
        $this->targetTheme = $targetTheme;
    }

    /**
     *
     * @return boolean
     */
    public function getOverwriteExisting()
    {
        return $this->overwriteExisting;
    }

    /**
     *
     * @param boolean $overwriteExisting
     */
    public function setOverwriteExisting($overwriteExisting)
    {
        $this->overwriteExisting = $overwriteExisting;
    }

    public function run()
    {
        $this->setHeader();
        $packageNamespaces = $this->getPackageNamespaces();

        $basePath = Path::getInstance()->getBasePath();
        $baseWebPath = realpath($basePath . '..') . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR;

        // Copy the resources
        foreach ($packageNamespaces as $packageNamespace)
        {
            $this->processType($packageNamespace, 'Images');
            $this->processType($packageNamespace, 'Css');
        }
    }

    public function setHeader()
    {
        header("Content-Type:text/plain");
    }

    /**
     *
     * @return string[]
     */
    public function getPackageNamespaces()
    {
        $resourceBundles = new ResourceBundles(PackageList::ROOT);
        return $resourceBundles->getPackageNamespaces();
    }

    /**
     *
     * @param string $packageNamespace
     */
    public function processType($packageNamespace, $folderType)
    {
        $basePath = Path::getInstance()->getResourcesPath($packageNamespace) . $folderType . DIRECTORY_SEPARATOR;
        $this->processContent($basePath);
    }

    /**
     *
     * @param string $basePath
     */
    private function processContent($basePath)
    {
        $sourceResourcePath = $basePath . $this->getSourceTheme() . DIRECTORY_SEPARATOR;
        $targetResourcePath = $basePath . $this->getTargetTheme() . DIRECTORY_SEPARATOR;

        $sourceFilePaths = Filesystem::get_directory_content($sourceResourcePath, Filesystem::LIST_FILES);

        foreach ($sourceFilePaths as $sourceFilePath)
        {
            $targetFilePath = str_replace($sourceResourcePath, $targetResourcePath, $sourceFilePath);
            $fileExists = file_exists($targetFilePath);

            if (! $fileExists || $this->getOverwriteExisting())
            {
                $targetFileFolderPath = dirname($targetFilePath);

                if (! file_exists($targetFileFolderPath) || ! is_dir($targetFileFolderPath))
                {
                    Filesystem::create_dir($targetFileFolderPath);
                    echo 'FOLDER CREATED: ' . str_replace(Path::getInstance()->getBasePath(), '', $targetFileFolderPath) .
                         PHP_EOL;
                }

                Filesystem::copy_file($sourceFilePath, $targetFilePath, $this->getOverwriteExisting());

                $actionPrefix = $this->getOverwriteExisting() && $fileExists ? 'REPLACED' : 'COPIED';
                echo 'FILE ' . $actionPrefix . ': ' .
                     str_replace(Path::getInstance()->getBasePath(), '', $targetFilePath) . PHP_EOL;
            }
        }
    }
}

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get('chamilo.libraries.architecture.bootstrap.bootstrap')->setup();

$themeCopier = new ThemeCopier('Aqua', 'Ruby');
$themeCopier->run();