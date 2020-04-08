<?php
namespace Chamilo\Core\Install\Format\Structure;

use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Format\Theme\ThemePathBuilder;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Banner extends \Chamilo\Libraries\Format\Structure\Banner
{
    /**
     * Creates the HTML output for the banner.
     */
    public function render()
    {
        $html = array();

        $html[] = '<nav class="navbar navbar-static-top navbar-cosnics navbar-inverse">';
        $html[] = '<div class="' . $this->getContainerMode() . '">';
        $html[] = '<div class="navbar-header">';

        $brandSource = $this->getThemePathBuilder()->getImagePath('Chamilo\Libraries', 'LogoHeader');

        $html[] = '<a class="navbar-brand" href="' . $this->getPathBuilder()->getBasePath(true) . '">';
        $html[] =
            '<img alt="' . $this->getTranslator()->trans('ChamiloInstallationTitle', array(), 'Chamilo\Core\Install') .
            '" src="' . $brandSource . '">';
        $html[] = '</a>';

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</nav>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Chamilo\Libraries\File\PathBuilder
     */
    public function getPathBuilder()
    {
        return $this->getService(PathBuilder::class);
    }

    /**
     * @return \Chamilo\Libraries\Format\Theme\ThemePathBuilder
     */
    public function getThemePathBuilder()
    {
        return $this->getService(ThemePathBuilder::class);
    }
}
