<?php
namespace Chamilo\Libraries\Cache\Assetic;

use Chamilo\Libraries\Format\Utilities\FileAsset;

/**
 *
 * @package Chamilo\Libraries\Format\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class JavascriptCacheService extends AsseticCacheService
{

    /**
     *
     * @see \Chamilo\Libraries\Format\Service\AsseticCacheService::getCachePath()
     */
    protected function getCachePath()
    {
        return $this->getPathUtilities()->getCachePath('Chamilo\Libraries\Resources\Javascript');
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Service\AsseticCacheService::getAssets()
     */
    protected function getAssets()
    {
        $assets = array();
        
        $javascriptPath = $this->getPathUtilities()->getJavascriptPath('Chamilo\Libraries');
        $pluginPath = $javascriptPath . 'Plugin/';
        
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'Jquery/jquery.min.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'Jquery/jquery.browser.min.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'Bootstrap/bootstrap.min.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'Bootstrap/bootstrap-toggle.min.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $javascriptPath . 'BootstrapConflictFixes.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'Jquery/jquery.ui.min.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'Jquery/jquery.tabula.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'Jquery/jquery.dynamic.visual_tabs.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'Jquery/jquery.tablednd.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'Jquery/jquery.ui.tabs.paging.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'Jquery/jquery.treeview.async.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'Jquery/jquery.timeout.interval.idle.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'Jquery/jquery.mousewheel.min.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'Jquery/jquery.scrollable.pack.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'Jquery/jquery.xml2json.pack.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'Jquery/jquery.json.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'Jquery/jquery.jsuggest.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'Jquery/jquery.jeditable.mini.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'Jquery/jquery.query.js');
        // $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'Jquery/jquery.simplemodal.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'Jquery/jquery.tree_menu.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'Jquery/jquery.timepicker.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'AngularJS/angular.min.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'AngularJS/angular-sanitize.min.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'Dropzone/dropzone.min.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $pluginPath . 'phpjs.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $javascriptPath . 'Utilities.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $javascriptPath . 'Notifications.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $javascriptPath . 'Help.js');
        $assets[] = new FileAsset($this->getPathUtilities(), $javascriptPath . 'Visit.js');
        
        return $assets;
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Service\AsseticCacheService::getAssetFilters()
     */
    protected function getAssetFilters()
    {
        return array(new \Assetic\Filter\JSMinFilter());
    }
}