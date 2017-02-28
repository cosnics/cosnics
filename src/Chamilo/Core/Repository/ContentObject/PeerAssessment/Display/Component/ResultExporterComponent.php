<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component;

use Chamilo\Core\Reporting\Viewer\Rendition\Template\TemplateRendition;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\TemplateRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Reporting\ResultReportingTemplate;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\Platform\Session\Request;

class ResultExporterComponent extends Manager
{

    public function run()
    {
        if (! $this->is_allowed(self::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $type = Request::get(self::PARAM_EXPORT_TYPE);

        $template = new ResultReportingTemplate($this);

        $file_path = TemplateRenditionImplementation::launch(
            $this,
            $template,
            TemplateRendition::FORMAT_PDF,
            TemplateRendition::VIEW_BASIC);

        $file_properties = FileProperties::from_path($file_path);

        Filesystem::file_send_for_download(
            $file_path,
            true,
            $file_properties->get_name_extension(),
            $file_properties->get_type());
        exit();
    }

    public function show_all()
    {
        return true;
    }

    /**
     *
     * @return int
     */
    public function get_current_block()
    {
        return null;
    }

    public function get_current_view()
    {
        return null;
    }
}
