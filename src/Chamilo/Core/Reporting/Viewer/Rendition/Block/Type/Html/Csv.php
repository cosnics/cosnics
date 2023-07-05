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
class Csv extends Html
{

    public function render()
    {
        $file_path = TemplateRenditionImplementation::launch(
            $this->get_context()->getContext(), $this->get_context()->get_template(), TemplateRendition::FORMAT_CSV
        );

        $file_properties = FileProperties::from_path($file_path);

        $this->getFilesystemTools()->sendFileForDownload(
            $file_path, $file_properties->get_name_extension(), $file_properties->getType()
        );
        $this->getFilesystem()->remove($file_path);
        exit();
    }
}
