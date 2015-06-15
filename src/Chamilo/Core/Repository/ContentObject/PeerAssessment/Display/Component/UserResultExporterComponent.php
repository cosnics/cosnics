<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component;

use Chamilo\Core\Reporting\ReportingExporter;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Reporting\UserResultReportingTemplate;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

class UserResultExporterComponent extends Manager
{

    public function run()
    {
        $settings = $this->get_settings($this->get_publication_id());

        if ($settings['enable_user_results_export'])
        {
            if (! $this->is_allowed(self :: EDIT_RIGHT) &&
                 $this->get_user()->get_id() != Request :: get(self :: PARAM_USER))
            {
                throw new NotAllowedException();
            }

            $type = Request :: get(self :: PARAM_EXPORT_TYPE);

            $template = new UserResultReportingTemplate($this);

            $exporter = ReportingExporter :: Factory($type, $template);
            $exporter->export();
        }
    }
}
