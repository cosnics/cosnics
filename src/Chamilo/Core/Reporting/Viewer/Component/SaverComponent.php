<?php
namespace Chamilo\Core\Reporting\Viewer\Component;

use Chamilo\Core\Reporting\Viewer\Manager;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\TemplateRendition;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\TemplateRenditionImplementation;
use Chamilo\Libraries\File\Properties\FileProperties;

/**
 * @author  Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class SaverComponent extends Manager
{

    public function run()
    {
        $format = $this->getRequest()->query->get(self::PARAM_FORMAT, TemplateRendition::FORMAT_HTML);
        $view = $this->getRequest()->query->get(self::PARAM_VIEW, TemplateRendition::VIEW_BASIC);

        $file_path = TemplateRenditionImplementation::launch($this, $this->get_template(), $format, $view);

        $file_properties = FileProperties::from_path($file_path);

        $this->getFilesystemTools()->sendFileForDownload(
            $file_path, $file_properties->get_name_extension(), $file_properties->getType()
        );
        $this->getFilesystem()->remove($file_path);
        exit();
    }
}
