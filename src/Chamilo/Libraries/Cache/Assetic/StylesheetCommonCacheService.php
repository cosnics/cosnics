<?php
namespace Chamilo\Libraries\Cache\Assetic;

use Assetic\Filter\CssImportFilter;
use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Chamilo\Libraries\Format\Utilities\CssFileAsset;

/**
 *
 * @package Chamilo\Libraries\Cache\Assetic
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StylesheetCommonCacheService extends AsseticCacheService
{

    /**
     *
     * @var \Chamilo\Libraries\Format\Theme\ThemePathBuilder
     */
    private $themePathBuilder;

    /**
     *
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     * @param \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
     * @param \Chamilo\Libraries\Format\Theme\ThemePathBuilder $themePathBuilder
     */
    public function __construct(
        PathBuilder $pathBuilder, ConfigurablePathBuilder $configurablePathBuilder, ThemePathBuilder $themePathBuilder
    )
    {
        parent::__construct($pathBuilder, $configurablePathBuilder);
        $this->themePathBuilder = $themePathBuilder;
    }

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
        return array($this->getThemePathBuilder()->getTheme());
    }

    /**
     * @return \Assetic\Asset\FileAsset[]
     */
    protected function getAssets()
    {
        $packages = PlatformPackageBundles::getInstance()->get_type_packages();

        $assets = array();

        $stylesheetPath = $this->getThemePathBuilder()->getStylesheetPath('Chamilo\Libraries', false, true);
        $assets[] = new CssFileAsset($this->getPathBuilder(), $stylesheetPath);

        foreach ($packages as $category => $namespaces)
        {
            foreach ($namespaces as $namespace => $package)
            {
                $stylesheetPath = $this->getThemePathBuilder()->getStylesheetPath($namespace, false, true);

                if (file_exists($stylesheetPath) && $namespace != 'Chamilo\Libraries')
                {
                    $assets[] = new CssFileAsset($this->getPathBuilder(), $stylesheetPath);
                }
            }
        }

        return $assets;
    }

    /**
     * @return string
     */
    protected function getCachePath()
    {
        return $this->getConfigurablePathBuilder()->getCachePath('Chamilo\Libraries\Resources\Stylesheet\Common');
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Theme\ThemePathBuilder
     */
    public function getThemePathBuilder()
    {
        return $this->themePathBuilder;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Theme\ThemePathBuilder $themePathBuilder
     */
    public function setThemePathBuilder(ThemePathBuilder $themePathBuilder)
    {
        $this->themePathBuilder = $themePathBuilder;
    }
}