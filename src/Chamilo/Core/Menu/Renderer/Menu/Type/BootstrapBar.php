<?php
namespace Chamilo\Core\Menu\Renderer\Menu\Type;

use Chamilo\Core\Menu\Renderer\Menu\Renderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;

/**
 *
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BootstrapBar extends Renderer
{
    const TYPE = 'BootstrapBar';

    public function display_menu_header()
    {
        $html = array();

        $html[] = '<nav class="navbar navbar-chamilo navbar-default">';
        $html[] = '<div class="container-fluid">';
        $html[] = '<div class="navbar-header">';

        $html[] = '<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#menu-navbar-collapse" aria-expanded="false">';
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

    public function display_menu_footer()
    {
        $html = array();

        $html[] = '</ul>';
        $html[] = '</nav>';

        return implode(PHP_EOL, $html);
    }

    public function renderBrand()
    {
        $siteName = PlatformSetting :: get('site_name', 'Chamilo\Core\Admin');
        $brandImage = PlatformSetting :: get('brand_image', 'Chamilo\Core\Menu');

        if ($brandImage)
        {
            $brandSource = $brandImage;
        }
        else
        {
            $brandSource = Theme :: getInstance()->getImagePath('Chamilo\Configuration', 'LogoHeader');
        }

        return '<a class="navbar-brand" href="#"><img alt="' . $siteName . '" src="' . $brandSource . '"></a>';
    }
}
