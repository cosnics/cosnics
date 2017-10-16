<?php
namespace Chamilo\Libraries\Format\Utilities;

use Chamilo\Configuration\Package\Finder\ResourceBundles;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Utilities\StringUtilities;

require_once realpath(__DIR__ . '/../../../../') . '/vendor/autoload.php';

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get('chamilo.libraries.architecture.bootstrap.bootstrap')->setup();

$resourceBundles = new ResourceBundles(PackageList::ROOT);
$packageNamespaces = $resourceBundles->getPackageNamespaces();

$basePath = Path::getInstance()->getBasePath();
$baseWebPath = realpath($basePath . '..') . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR;

foreach ($packageNamespaces as $packageNamespace)
{
    $sourceResourceImagePath = Path::getInstance()->getResourcesPath($packageNamespace) . 'Images' . DIRECTORY_SEPARATOR;

    // $sourceResourceImagePathCC = Path :: getInstance()->getResourcesPath($packageNamespace) . 'Images' .
    // DIRECTORY_SEPARATOR;

    // Filesystem :: move_file($sourceResourceImagePath, $sourceResourceImagePathCC);

    $files = Filesystem::get_directory_content($sourceResourceImagePath, Filesystem::LIST_FILES, true);

    foreach ($files as $file)
    {
        $oldFile = str_replace($basePath, '', $file);
        $parts = explode('/', $oldFile);

        foreach ($parts as $key => $part)
        {
            $parts[$key] = StringUtilities::getInstance()->createString($part)->upperCamelize()->__toString();
        }

        $newFile = implode('/', $parts);
        $newFile = $basePath . $newFile;

        Filesystem::move_file($file, $newFile, true);
    }

    echo $packageNamespace . '<br />';
}