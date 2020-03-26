<?php
namespace Chamilo\Libraries\Cache\Assetic;

use Assetic\Filter\CssImportFilter;
use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\CssFileAsset;

/**
 *
 * @package Chamilo\Libraries\Cache\Assetic
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StylesheetCacheService extends AsseticCacheService
{

    /**
     *
     * @var \Chamilo\Libraries\Format\Theme
     */
    private $themeUtilities;

    /**
     *
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     * @param \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     */
    public function __construct(
        PathBuilder $pathBuilder, ConfigurablePathBuilder $configurablePathBuilder, Theme $themeUtilities
    )
    {
        parent::__construct($pathBuilder, $configurablePathBuilder);
        $this->themeUtilities = $themeUtilities;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\Assetic\AsseticCacheService::getAssetFilters()
     */
    protected function getAssetFilters()
    {
        return array(new CssImportFilter());
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\Assetic\AsseticCacheService::getAssets()
     */
    protected function getAssets()
    {
        $packages = PlatformPackageBundles::getInstance()->get_type_packages();

        $assets = array();

        $stylesheetPath = $this->getThemeUtilities()->getStylesheetPath('Chamilo\Libraries', false, true);
        $assets[] = new CssFileAsset($this->getPathBuilder(), $stylesheetPath);

        foreach ($packages as $category => $namespaces)
        {
            foreach ($namespaces as $namespace => $package)
            {
                $stylesheetPath = $this->getThemeUtilities()->getStylesheetPath($namespace, false, true);

                if (file_exists($stylesheetPath) && $namespace != 'Chamilo\Libraries')
                {
                    $assets[] = new CssFileAsset($this->getPathBuilder(), $stylesheetPath);
                }
            }
        }

        return $assets;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\Assetic\AsseticCacheService::getCachePath()
     */
    protected function getCachePath()
    {
        return $this->getConfigurablePathBuilder()->getCachePath('Chamilo\Libraries\Resources\Stylesheet');
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Theme
     */
    public function getThemeUtilities()
    {
        return $this->themeUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     */
    public function setThemeUtilities(Theme $themeUtilities)
    {
        $this->themeUtilities = $themeUtilities;
    }
}