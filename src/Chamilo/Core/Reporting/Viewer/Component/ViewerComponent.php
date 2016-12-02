<?php
namespace Chamilo\Core\Reporting\Viewer\Component;

use Chamilo\Core\Reporting\Viewer\Manager;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\TemplateRendition;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\TemplateRenditionImplementation;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class ViewerComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $format = Request::get(self::PARAM_FORMAT) ? Request::get(self::PARAM_FORMAT) : TemplateRendition::FORMAT_HTML;
        $view = Request::get(self::PARAM_VIEW) ? Request::get(self::PARAM_VIEW) : TemplateRendition::VIEW_BASIC;
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = TemplateRenditionImplementation::launch($this, $this->get_template(), $format, $view);
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }
}
