<?php
namespace Chamilo\Core\Reporting\Viewer\Component;

use Chamilo\Core\Reporting\Viewer\Manager;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\TemplateRendition;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\TemplateRenditionImplementation;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class SaverComponent extends Manager
{

    public function run()
    {
        $format = Request::get(self::PARAM_FORMAT) ? Request::get(self::PARAM_FORMAT) : TemplateRendition::FORMAT_HTML;
        $view = Request::get(self::PARAM_VIEW) ? Request::get(self::PARAM_VIEW) : TemplateRendition::VIEW_BASIC;
        
        $file_path = TemplateRenditionImplementation::launch($this, $this->get_template(), $format, $view);
        
        $file_properties = FileProperties::from_path($file_path);
        
        Filesystem::file_send_for_download(
            $file_path, 
            true, 
            $file_properties->get_name_extension(), 
            $file_properties->get_type());
        exit();
    }
}
