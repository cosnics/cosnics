<?php
namespace Chamilo\Core\Menu\Renderer;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Service\RightsService;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\ChamiloRequest;

/**
 * @package Chamilo\Core\Menu\Renderer
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NavigationBarRenderer extends MenuRenderer
{
    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    private $configurationConsulter;

    /**
     * @var \Chamilo\Libraries\File\PathBuilder
     */
    private $pathBuilder;

    /**
     * @var \Chamilo\Libraries\Format\Theme
     */
    private $themeUtilities;

    /**
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     * @param \Chamilo\Core\Menu\Service\RightsService $rightsService
     * @param \Chamilo\Core\Menu\Renderer\ItemRendererFactory $itemRendererFactory
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $chamiloRequest
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     */
    public function __construct(
        ItemService $itemService, RightsService $rightsService, ItemRendererFactory $itemRendererFactory,
        ChamiloRequest $chamiloRequest, ConfigurationConsulter $configurationConsulter, PathBuilder $pathBuilder,
        Theme $themeUtilities
    )
    {
        parent::__construct($itemService, $rightsService, $itemRendererFactory, $chamiloRequest);

        $this->configurationConsulter = $configurationConsulter;
        $this->pathBuilder = $pathBuilder;
        $this->themeUtilities = $themeUtilities;
    }

    /**
     * @return \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    /**
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function setConfigurationConsulter(ConfigurationConsulter $configurationConsulter): void
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * @return \Chamilo\Libraries\File\PathBuilder
     */
    public function getPathBuilder(): PathBuilder
    {
        return $this->pathBuilder;
    }

    /**
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     */
    public function setPathBuilder(PathBuilder $pathBuilder): void
    {
        $this->pathBuilder = $pathBuilder;
    }

    /**
     * @return \Chamilo\Libraries\Format\Theme
     */
    public function getThemeUtilities(): Theme
    {
        return $this->themeUtilities;
    }

    /**
     * @param \Chamilo\Libraries\Format\Theme $themeUtilities
     */
    public function setThemeUtilities(Theme $themeUtilities): void
    {
        $this->themeUtilities = $themeUtilities;
    }

    /**
     * @param string $containerMode
     * @param integer $numberOfItems
     *
     * @return string
     */
    public function renderHeader(string $containerMode, int $numberOfItems = 0)
    {
        $html = array();

        $class = 'navbar navbar-chamilo navbar-default';

        if ($numberOfItems == 0)
        {
            $class .= ' navbar-no-items';
        }

        $html[] = '<nav class="' . $class . '">';
        $html[] = '<div class="' . $containerMode . '">';
        $html[] = '<div class="navbar-header">';

        $html[] =
            '<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#menu-navbar-collapse" aria-expanded="false">';
        $html[] = '<span class="sr-only">Toggle navigation</span>';
        $html[] = '<span class="icon-bar"></span>';
        $html[] = '<span class="icon-bar"></span>';
        $html[] = '<span class="icon-bar"></span>';
        $html[] = '</button>';
        $html[] = $this->renderBrand();

        $html[] = '</div>';
        $html[] = '<div class="collapse navbar-collapse" id="menu-navbar-collapse">';
        $html[] = '<ul class="nav navbar-nav navbar-right">';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string
     */
    public function renderFooter()
    {
        $html = array();

        $html[] = '</ul>';
        $html[] = '</nav>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string
     */
    public function renderBrand()
    {
        $configurationConsulter = $this->getConfigurationConsulter();

        $siteName = $configurationConsulter->getSetting(array('Chamilo\Core\Admin', 'site_name'));
        $brandImage = $configurationConsulter->getSetting(array('Chamilo\Core\Menu', 'brand_image'));

        if ($brandImage)
        {
            $brandSource = $brandImage;
        }
        else
        {
            $brandSource = $this->getThemeUtilities()->getImagePath('Chamilo\Configuration', 'LogoHeader');
        }

        $basePath = $this->getPathBuilder()->getBasePath(true);

        return '<a class="navbar-brand" href="' . $basePath . '">' . '<img alt="' . $siteName . '" src="' .
            $brandSource . '"></a>';
    }
}