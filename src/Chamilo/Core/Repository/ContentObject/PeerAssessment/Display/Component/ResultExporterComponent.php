<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component;

use Chamilo\Core\Reporting\ReportingExporter;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Reporting\ResultReportingTemplate;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

class ResultExporterComponent extends Manager
{

    public function run()
    {
        if (! $this->is_allowed(self :: EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $type = Request :: get(self :: PARAM_EXPORT_TYPE);

        $template = new ResultReportingTemplate($this);

        $exporter = ReportingExporter :: factory($type, $template);
        $exporter->export();
    }
}
