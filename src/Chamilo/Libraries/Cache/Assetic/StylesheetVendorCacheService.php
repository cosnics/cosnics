<?php
namespace Chamilo\Libraries\Cache\Assetic;

use Assetic\Filter\CssImportFilter;
use Chamilo\Libraries\Format\Utilities\CssFileAsset;

/**
 *
 * @package Chamilo\Libraries\Cache\Assetic
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StylesheetVendorCacheService extends AsseticCacheService
{

    /**
     * @return \Assetic\Filter\FilterInterface[]
     */
    protected function getAssetFilters()
    {
        return array(new CssImportFilter());
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
        $pluginPath = $this->getPathBuilder()->getPluginPath('Chamilo\Libraries');

        $assets = array();

        // Bootstrap
        $assets[] = new CssFileAsset($this->getPathBuilder(), $pluginPath . 'Bootstrap/css/bootstrap.min.css');
        $assets[] = new CssFileAsset(
            $this->getPathBuilder(), $pluginPath . 'BootstrapCheckbox/awesome-bootstrap-checkbox.min.css'
        );

        // FontAwesome
        $assets[] = new CssFileAsset($this->getPathBuilder(), $pluginPath . 'FontAwesome5/css/all.min.css');
        $assets[] = new CssFileAsset($this->getPathBuilder(), $pluginPath . 'ConnectIdents/ConnectIdents.min.css');

        // Other plugins
        $assets[] = new CssFileAsset($this->getPathBuilder(), $pluginPath . 'Dropzone/dropzone.min.css');
        $assets[] = new CssFileAsset($this->getPathBuilder(), $pluginPath . 'Jquery/jquery-ui.min.css');
        $assets[] = new CssFileAsset($this->getPathBuilder(), $pluginPath . 'Bootstrap/css/bootstrap-toggle.min.css');
        $assets[] =
            new CssFileAsset($this->getPathBuilder(), $pluginPath . 'JqueryContextMenu/jquery.contextMenu.min.css');
        $assets[] = new CssFileAsset($this->getPathBuilder(), $pluginPath . 'Highlight/github.min.css');
        $assets[] = new CssFileAsset(
            $this->getPathBuilder(), $pluginPath . 'Fancytree/dist/skin-bootstrap/ui.fancytree.min.css'
        );

        return $assets;
    }

    /**
     * @return string
     */
    protected function getCachePath()
    {
        return $this->getConfigurablePathBuilder()->getCachePath('Chamilo\Libraries\Resources\Stylesheet\Vendor');
    }
}