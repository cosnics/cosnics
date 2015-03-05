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

        // if ($this->getContext() == __NAMESPACE__)
        // {
        $plugin_path = $this->getPathUtilities()->getJavascriptPath('Chamilo\Libraries') . 'Plugin/';
        $configuration = $this->getPathUtilities()->getJavascriptPath('Chamilo\Configuration');

        $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'JQuery/jquery.min.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'JQuery/jquery.tabula.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'JQuery/jquery.dynamic.visual_tabs.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'JQuery/jquery.tablednd.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'JQuery/jquery.ui.min.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'JQuery/jquery.ui.tabs.paging.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'JQuery/jquery.treeview.async.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'JQuery/jquery.timeout.interval.idle.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'JQuery/jquery.mousewheel.min.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'JQuery/jquery.scrollable.pack.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'JQuery/jquery.xml2json.pack.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'JQuery/jquery.json.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'JQuery/jquery.iphone.checkboxes.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'JQuery/jquery.jsuggest.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'JQuery/jquery.jeditable.mini.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'JQuery/jquery.query.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'JQuery/jquery.simplemodal.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'JQuery/jquery.tree_menu.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'JQuery/jquery.timepicker.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $plugin_path . 'phpjs.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $configuration . 'Utilities.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $configuration . 'Notifications.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $configuration . 'Help.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $configuration . 'Visit.js');
        // }
        // else
        // {
        // $path = $this->getPathUtilities()->getJavascriptPath($this->getContext()) .
        // $this->getClassnameUtilties()->getPackageNameFromNamespace($this->getContext(), true) . '.js';

        // if (is_readable($path))
        // {
        // $assets[] = new FileAsset($this->getPathUtilities(), $path);
        // }
        // }

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
