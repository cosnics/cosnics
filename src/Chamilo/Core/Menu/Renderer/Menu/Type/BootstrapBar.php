<?php
namespace Chamilo\Core\Menu\Renderer\Menu\Type;

use Chamilo\Core\Menu\Manager;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Core\Menu\Renderer\Menu\Renderer;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BootstrapBar extends Renderer
{
    const TYPE = 'bootstrap_bar';

    public function display_menu_header()
    {
        $packagePath = Path :: getInstance()->namespaceToFullPath('Chamilo\Core\Menu', true);

        $theme = Theme :: getInstance();
        $cssPath = $theme->getCssPath(Manager :: context());

        $html = array();
        $html[] = ResourceManager :: get_instance()->get_resource_html($cssPath . 'ChamiloBootstrapBar.css');
        // $html[] = ResourceManager::get_instance()->get_resource_html($packagePath .
        // 'Resources/Javascript/BootstrapBar.js');

        $html[] = '<nav class="navbar navbar-chamilo navbar-default">';
        $html[] = '<div class="container-fluid">';
        $html[] = '<div class="navbar-header">';
        $html[] = '<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#menu-navbar-collapse" aria-expanded="false">';
        $html[] = '<span class="sr-only">Toggle navigation</span>';
        $html[] = '<span class="icon-bar"></span>';
        $html[] = '<span class="icon-bar"></span>';
        $html[] = '<span class="icon-bar"></span>';
        $html[] = '</button>';

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
}
