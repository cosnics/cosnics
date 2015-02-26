<?php
namespace Chamilo\Libraries\Format\Utilities;

use Assetic\Asset\AssetCache;
use Assetic\Asset\AssetCollection;
use Assetic\Cache\FilesystemCache;
use Assetic\Filter\CssImportFilter;
use Assetic\Filter\CssMinFilter;
use Chamilo\Libraries\Protocol\HttpHeader;

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
        $packages = \Chamilo\Configuration\Package\PlatformPackageList :: getInstance()->get_type_packages();

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

        HttpHeader :: content_type(HttpHeader :: CONTENT_TYPE_CSS, self :: DEFAULT_CHARSET);

        if ($this->getCachingEnabled())
        {
            $asset_collection = new AssetCollection($assets, array(new CssImportFilter(), new CssMinFilter()));
            $asset_cache = new AssetCache(
                $asset_collection,
                new FilesystemCache($this->getPathUtilities()->getCachePath() . 'resource/'));
            echo $asset_cache->dump();
        }
        else
        {
            $asset_collection = new AssetCollection($assets, array(new CssImportFilter()));
            echo $asset_collection->dump();
        }
    }
}
