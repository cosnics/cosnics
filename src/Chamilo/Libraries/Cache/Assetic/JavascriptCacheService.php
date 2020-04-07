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
     * @return \Assetic\Filter\FilterInterface[]
     */
    protected function getAssetFilters()
    {
        return array();
    }

    /**
     * @return string[]
     */
    protected function getAssetVariables()
    {
        return array();
    }

    /**
     * @return \Assetic\Asset\FileAsset[]
     */
    protected function getAssets()
    {
        $assets = array();

        $javascriptPath = $this->getPathBuilder()->getJavascriptPath('Chamilo\Libraries');
        $pluginPath = $this->getPathBuilder()->getPluginPath('Chamilo\Libraries');

        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.browser.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery-ui.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.timeout.interval.idle.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.mousewheel.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.scrollable.pack.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.xml2json.pack.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.json.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery.query.min.js');

        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Bootstrap/bootstrap.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Bootstrap/bootstrap-toggle.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Bootstrap/BootstrapConflictFixes.js');

        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'JqueryContextMenu/jquery.contextMenu.min.js');

        $assets[] = new FileAsset($this->getPathBuilder(), $javascriptPath . 'Jquery/jquery.dynamic.visualTabs.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $javascriptPath . 'Jquery/jquery.treeMenu.min.js');

        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'AngularJS/angular.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'AngularJS/angular-sanitize.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'AngularJS-UI-Bootstrap/ui-bootstrap.min.js');

        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Fancytree/dist/jquery.fancytree-all.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Dropzone/dropzone.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'Highlight/highlight.pack.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $pluginPath . 'PhpJs/phpjs.min.js');

        $assets[] = new FileAsset($this->getPathBuilder(), $javascriptPath . 'Utilities.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $javascriptPath . 'Help.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $javascriptPath . 'Visit.min.js');
        $assets[] = new FileAsset($this->getPathBuilder(), $javascriptPath . 'InitHighlight.min.js');

        return $assets;
    }

    /**
     * @return string
     */
    protected function getCachePath()
    {
        return $this->getConfigurablePathBuilder()->getCachePath('Chamilo\Libraries\Resources\Javascript');
    }
}