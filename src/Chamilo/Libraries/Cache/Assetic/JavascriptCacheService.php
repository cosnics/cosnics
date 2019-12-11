<?php
namespace Chamilo\Libraries\Cache\Assetic;

use Chamilo\Libraries\Format\Utilities\FileAsset;

/**
 *
 * @package Chamilo\Libraries\Cache\Assetic
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class JavascriptCacheService extends AsseticCacheService
{

    /**
     *
     * @see \Chamilo\Libraries\Cache\Assetic\AsseticCacheService::getCachePath()
     */
    protected function getCachePath()
    {
        return $this->getConfigurablePathBuilder()->getCachePath('Chamilo\Libraries\Resources\Javascript');
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\Assetic\AsseticCacheService::getAssets()
     */
    protected function getAssets()
    {
        $assets = array();

        $javascriptPath = $this->getPathBuilder()->getJavascriptPath('Chamilo\Libraries');
        $pluginPath = $javascriptPath . 'Plugin/';

        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.browser.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Bootstrap/bootstrap.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Bootstrap/bootstrap-toggle.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $javascriptPath . 'BootstrapConflictFixes.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.ui.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.tabula.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.dynamic.visual_tabs.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.tablednd.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.ui.tabs.paging.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.treeview.async.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.timeout.interval.idle.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.mousewheel.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.scrollable.pack.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.xml2json.pack.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.json.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.jsuggest.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.jeditable.mini.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.query.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.tree_menu.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.timepicker.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'JqueryContextMenu/jquery.contextMenu.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'AngularJS/angular.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'AngularJS/angular-sanitize.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'AngularJS-UI-Bootstrap/ui-bootstrap.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Fancytree/dist/jquery.fancytree-all.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Dropzone/dropzone.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Highlight/highlight.pack.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'phpjs.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $javascriptPath . 'Utilities.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $javascriptPath . 'Notifications.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $javascriptPath . 'Help.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $javascriptPath . 'Visit.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $javascriptPath . 'InitHighlight.js');
//        $assets[] = new FileAsset($this->getPathBuilder(), $javascriptPath . 'Common.js');


        return $assets;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\Assetic\AsseticCacheService::getAssetFilters()
     */
    protected function getAssetFilters()
    {
        return array(new \Assetic\Filter\JSMinFilter());
    }
}
