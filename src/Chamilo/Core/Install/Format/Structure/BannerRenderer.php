<?php
namespace Chamilo\Core\Install\Format\Structure;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Menu\Renderer\MenuRenderer;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrailRenderer;
use Chamilo\Libraries\Format\Structure\PageConfiguration;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;
use Chamilo\Libraries\Platform\Session\SessionUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BannerRenderer extends \Chamilo\Libraries\Format\Structure\BannerRenderer
{
    private PathBuilder $pathBuilder;

    private ThemePathBuilder $themePathBuilder;

    public function __construct(
        PageConfiguration $pageConfiguration, SessionUtilities $sessionUtilities, Translator $translator,
        ConfigurationConsulter $configurationConsulter, UrlGenerator $urlGenerator, MenuRenderer $menuRenderer,
        BreadcrumbTrailRenderer $breadcrumbTrailRenderer, ThemePathBuilder $themePathBuilder, PathBuilder $pathBuilder
    )
    {
        parent::__construct(
            $pageConfiguration, $sessionUtilities, $translator, $configurationConsulter, $urlGenerator, $menuRenderer,
            $breadcrumbTrailRenderer
        );

        $this->themePathBuilder = $themePathBuilder;
        $this->pathBuilder = $pathBuilder;
    }

    public function render(): string
    {
        $html = [];

        $html[] = '<nav class="navbar navbar-static-top navbar-cosnics navbar-inverse">';
        $html[] = '<div class="' . $this->getPageConfiguration()->getContainerMode() . '">';
        $html[] = '<div class="navbar-header">';

        $brandSource = $this->getThemePathBuilder()->getImagePath('Chamilo\Libraries', 'LogoHeader');

        $html[] = '<a class="navbar-brand" href="' . $this->getPathBuilder()->getBasePath(true) . '">';
        $html[] = '<img alt="' . $this->getTranslator()->trans('ChamiloInstallationTitle', [], 'Chamilo\Core\Install') .
            '" src="' . $brandSource . '">';
        $html[] = '</a>';

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</nav>';

        return implode(PHP_EOL, $html);
    }

    public function getPathBuilder(): PathBuilder
    {
        return $this->pathBuilder;
    }

    public function getThemePathBuilder(): ThemePathBuilder
    {
        return $this->themePathBuilder;
    }
}