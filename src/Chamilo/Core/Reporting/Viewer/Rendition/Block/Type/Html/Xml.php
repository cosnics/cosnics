<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;

use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\TemplateRendition;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\TemplateRenditionImplementation;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Properties\FileProperties;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class Xml extends Html
{

    public function render()
    {
        $file_path = TemplateRenditionImplementation::launch(
            $this->get_context()->get_context(), 
            $this->get_context()->get_template(), 
            TemplateRendition::FORMAT_XML
        );
        
        $file_properties = FileProperties::from_path($file_path);
        
        Filesystem::file_send_for_download(
            $file_path, 
            true, 
            $file_properties->get_name_extension(), 
            $file_properties->getType());
        exit();
    }
}
