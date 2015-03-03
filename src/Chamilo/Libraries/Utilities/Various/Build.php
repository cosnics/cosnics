<?php
namespace Chamilo\Libraries\Utilities\Various;

use Chamilo\Configuration\Package\Finder\ResourceBundles;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Filesystem;

require_once __DIR__ . '/../../Architecture/Bootstrap.php';
\Chamilo\Libraries\Architecture\Bootstrap :: getInstance()->setup();

$resourceBundles = new ResourceBundles(PackageList :: ROOT);
$packageNamespaces = $resourceBundles->getPackageNamespaces();

$basePath = Path :: getInstance()->getBasePath();
$baseWebPath = realpath(Path :: getInstance()->getBasePath() . '..' . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR) .
     DIRECTORY_SEPARATOR;

foreach ($packageNamespaces as $packageNamespace)
{
    $sourceResourcePath = Path :: getInstance()->getResourcesPath($packageNamespace);
    $webResourcePath = str_replace($basePath, $baseWebPath, $sourceResourcePath);

    Filesystem :: recurse_copy($sourceResourcePath, $webResourcePath);
}