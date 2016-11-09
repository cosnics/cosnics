<?php
namespace Chamilo\Core\Menu\Renderer\Menu\Type;

use Chamilo\Core\Menu\Renderer\Menu\Renderer;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Configuration\Configuration;

/**
 *
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Bar extends Renderer
{
    const TYPE = 'Bar';

    public function display_menu_header($numberOfItems = 0)
    {
        $html = array();

        $class = 'navbar navbar-chamilo navbar-default';

        if ($numberOfItems == 0)
        {
            $class .= ' navbar-no-items';
        }

        $html[] = '<nav class="' . $class . '">';
        $html[] = '<div class="' . $this->getContainerMode() . '">';
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
        $siteName = Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'site_name'));
        $brandImage = Configuration::getInstance()->get_setting(array('Chamilo\Core\Menu', 'brand_image'));

        if ($brandImage)
        {
            $brandSource = $brandImage;
        }
        else
        {
            $brandSource = Theme::getInstance()->getImagePath('Chamilo\Configuration', 'LogoHeader');
        }

        return '<a class="navbar-brand" href="' . Path::getInstance()->getBasePath(true) . '">' . '<img alt="' .
             $siteName . '" src="' . $brandSource . '"></a>';
    }

    /**
     * Returns whether or not the menu is available for anonymous users
     */
    public function isMenuAvailableAnonymously()
    {
        return true;
    }
}
