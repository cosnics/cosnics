<?php
namespace Chamilo\Libraries\Cache\Assetic;

use Assetic\Filter\CssImportFilter;
use Assetic\Filter\CssMinFilter;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\CssFileAsset;
use Chamilo\Libraries\File\PathBuilder;

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
    public function __construct(PathBuilder $pathBuilder, ConfigurablePathBuilder $configurablePathBuilder,
        Theme $themeUtilities)
    {
        parent::__construct($pathBuilder, $configurablePathBuilder);
        $this->themeUtilities = $themeUtilities;
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
     * @see \Chamilo\Libraries\Cache\Assetic\AsseticCacheService::getAssets()
     */
    protected function getAssets()
    {
        $packages = \Chamilo\Configuration\Package\PlatformPackageBundles::getInstance()->get_type_packages();

        $assets = array();

        foreach ($packages as $category => $namespaces)
        {
            foreach ($namespaces as $namespace => $package)
            {
                $stylesheetPath = $this->getThemeUtilities()->getStylesheetPath($namespace, false);

                if (file_exists($stylesheetPath))
                {
                    $assets[] = new CssFileAsset($this->getPathBuilder(), $stylesheetPath);
                }
            }
        }

        return $assets;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\Assetic\AsseticCacheService::getAssetFilters()
     */
    protected function getAssetFilters()
    {
        return array(new CssImportFilter(), new CssMinFilter());
    }
}