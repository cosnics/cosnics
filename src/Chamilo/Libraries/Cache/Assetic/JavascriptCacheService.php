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