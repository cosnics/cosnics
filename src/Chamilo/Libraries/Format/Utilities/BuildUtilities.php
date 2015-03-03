<?php
namespace Chamilo\Libraries\Format\Utilities;

use Chamilo\Configuration\Package\Finder\ResourceBundles;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Filesystem;
use Composer\Script\Event;

class BuildUtilities
{

    public static function processResources(Event $event)
    {
        $resourceBundles = new ResourceBundles(PackageList :: ROOT);
        $packageNamespaces = $resourceBundles->getPackageNamespaces();

        $basePath = Path :: getInstance()->getBasePath();
        $baseWebPath = realpath(
            Path :: getInstance()->getBasePath() . '..' . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR) .
             DIRECTORY_SEPARATOR;

        // Create the web-folder
        Filesystem :: create_dir($baseWebPath);
        $event->getIO()->write('Created the web-folder: ' . $baseWebPath);

        // Copy the resources
        foreach ($packageNamespaces as $packageNamespace)
        {
            // Images
            $sourceResourceImagePath = Path :: getInstance()->getResourcesPath($packageNamespace) . 'Images' .
                 DIRECTORY_SEPARATOR;
            $webResourceImagePath = str_replace($basePath, $baseWebPath, $sourceResourceImagePath);
            Filesystem :: recurse_copy($sourceResourceImagePath, $webResourceImagePath, true);

            // Javascript
            $sourceResourceJavascriptPath = Path :: getInstance()->getResourcesPath($packageNamespace) . 'Javascript' .
                 DIRECTORY_SEPARATOR;
            $webResourceJavascriptPath = str_replace($basePath, $baseWebPath, $sourceResourceJavascriptPath);
            Filesystem :: recurse_copy($sourceResourceJavascriptPath, $webResourceJavascriptPath, true);

            $event->getIO()->write('Processed resources for: ' . $packageNamespace);
        }

        // Write pseudo-index file
        $indexWebPath = $baseWebPath . 'index.php';
        $php = <<<EOT
                require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'index.php';
EOT;
        Filesystem :: write_to_file($indexWebPath, $php);
        $event->getIO()->write('Created the index-file: ' . $indexWebPath);
    }
}