<?php
namespace Chamilo\Libraries\Format\Utilities;

use Chamilo\Configuration\Package\Finder\ResourceBundles;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Filesystem;

require __DIR__ . '/../../Architecture/Bootstrap.php';
\Chamilo\Libraries\Architecture\Bootstrap :: getInstance();

$resourceBundles = new ResourceBundles(PackageList :: ROOT);
$packageNamespaces = $resourceBundles->getPackageNamespaces();

$basePath = Path :: getInstance()->getBasePath();
$baseWebPath = realpath($basePath . '..') . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR;

foreach ($packageNamespaces as $packageNamespace)
{
    $sourceResourceImagePath = Path :: getInstance()->getResourcesPath($packageNamespace) . 'ImagesCC' .
         DIRECTORY_SEPARATOR;

    $sourceResourceImagePathCC = Path :: getInstance()->getResourcesPath($packageNamespace) . 'Images' .
        DIRECTORY_SEPARATOR;

    Filesystem :: move_file($sourceResourceImagePath, $sourceResourceImagePathCC);

//     $files = Filesystem :: get_directory_content($sourceResourceImagePath, Filesystem :: LIST_FILES, true);

//     foreach ($files as $file)
//     {
//         $parts = explode('\\', $file);
//         $last = array_pop($parts);

//         $parts[] = StringUtilities :: getInstance()->createString($last)->upperCamelize()->__toString();

//         $newFile = implode('\\', $parts);

//         Filesystem :: move_file($file, $newFile);
//     }

    echo $packageNamespace . PHP_EOL;
}