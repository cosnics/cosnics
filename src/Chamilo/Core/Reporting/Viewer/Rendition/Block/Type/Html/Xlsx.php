<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;

use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\TemplateRendition;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\TemplateRenditionImplementation;
use Chamilo\Libraries\File\Properties\FileProperties;

/**
 * @author  Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class Xlsx extends Html
{

    public function render()
    {
        $file_path = TemplateRenditionImplementation::launch(
            $this->get_context()->get_context(), $this->get_context()->get_template(), TemplateRendition::FORMAT_XLSX
        );

        $file_properties = FileProperties::from_path($file_path);

        $this->getFilesystemTools()->sendFileForDownload(
            $file_path, $file_properties->get_name_extension(), 'application/vnd.openxmlformats'
        );
        $this->getFilesystem()->remove($file_path);
        exit();
    }
}
