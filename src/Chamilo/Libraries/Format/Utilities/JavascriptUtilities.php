<?php
namespace Chamilo\Libraries\Format\Utilities;

use Assetic\Asset\AssetCollection;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * @package libraries
 * @author Laurent Opprecht
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class JavascriptUtilities extends ResourceUtilities
{

    public function run()
    {
        $assets = array();

        if ($this->getContext() == __NAMESPACE__)
        {
            $plugin_path = $this->getPathUtilities()->getPluginPath();
            $configuration = $this->getPathUtilities()->getConfigurationPath();

            $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'jquery/jquery.min.js');
            $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'jquery/jquery.tabula.js');
            $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'jquery/jquery.dynamic.visual_tabs.js');
            $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'jquery/jquery.tablednd.js');
            $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'jquery/jquery.ui.min.js');
            $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'jquery/jquery.ui.tabs.paging.js');
            $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'jquery/jquery.treeview.async.js');
            $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'jquery/jquery.timeout.interval.idle.js');
            $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'jquery/jquery.mousewheel.min.js');
            $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'jquery/jquery.scrollable.pack.js');
            $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'jquery/jquery.xml2json.pack.js');
            $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'jquery/jquery.json.js');
            $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'jquery/jquery.iphone.checkboxes.js');
            $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'jquery/jquery.jsuggest.js');
            $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'jquery/jquery.jeditable.mini.js');
            $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'jquery/jquery.query.js');
            $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'jquery/jquery.simplemodal.js');
            $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'jquery/jquery.tree_menu.js');
            $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'jquery/jquery.timepicker.js');
            $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'jquery/phpjs.js');
            $assets[] = new FileAsset($this->getPathUtilities(), $configuration . 'Resources/Javascript/Utilities.js');
            $assets[] = new FileAsset(
                $this->getPathUtilities(),
                $configuration . 'Resources/Javascript/Notifications.js');
            $assets[] = new FileAsset($this->getPathUtilities(), $configuration . 'Resources/Javascript/Help.js');
            $assets[] = new FileAsset($this->getPathUtilities(), $configuration . 'Resources/Javascript/Visit.js');
        }
        else
        {
            $path = $this->getClassnameUtilties()->namespaceToFullPath($this->getContext()) . 'Resources/Javascript/' .
                 $this->getClassnameUtilties()->getPackageNameFromNamespace($this->getContext()) . '.js';

            if (is_readable($path))
            {
                $assets[] = new FileAsset($this->getPathUtilities(), $path);
            }
        }

        if ($this->getCachingEnabled())
        {
            $asset_collection = new AssetCollection($assets, array(new \Assetic\Filter\JSMinFilter()));
            $assets = new \Assetic\Asset\AssetCache(
                $asset_collection,
                new \Assetic\Cache\FilesystemCache($this->getPathUtilities()->getCachePath() . 'Resource/'));
        }
        else
        {
            $assets = new AssetCollection($assets);
        }

        $response = new Response();
        $response->setContent($assets->dump());
        $response->headers->set('Content-Type', 'text/javascript');
        $response->send();
    }
}
