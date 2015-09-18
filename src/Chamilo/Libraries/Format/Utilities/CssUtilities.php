<?php
namespace Chamilo\Libraries\Format\Utilities;

use Assetic\Asset\AssetCache;
use Assetic\Asset\AssetCollection;
use Assetic\Cache\FilesystemCache;
use Assetic\Filter\CssImportFilter;
use Assetic\Filter\CssMinFilter;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * @package libraries
 * @author Laurent Opprecht
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CssUtilities extends ResourceUtilities
{

    public function run()
    {
        $packages = \Chamilo\Configuration\Package\PlatformPackageBundles :: getInstance()->get_type_packages();

        $assets = array();

        foreach ($packages as $category => $namespaces)
        {
            foreach ($namespaces as $namespace => $package)
            {
                $stylesheetPath = $this->getThemeUtilities()->getStylesheetPath($namespace, false);

                if (file_exists($stylesheetPath))
                {
                    $assets[] = new CssFileAsset($this->getPathUtilities(), $stylesheetPath);
                }
            }
        }

        if ($this->getCachingEnabled())
        {
            $asset_collection = new AssetCollection($assets, array(new CssImportFilter(), new CssMinFilter()));
            $assets = new AssetCache(
                $asset_collection,
                new FilesystemCache($this->getPathUtilities()->getCachePath() . 'Resource/'));
        }
        else
        {
            $assets = new AssetCollection($assets, array(new CssImportFilter()));
        }

        $response = new Response();
        $response->setContent($assets->dump());
        $response->headers->set('Content-Type', 'text/css');
        $response->send();
        exit;
    }
}
