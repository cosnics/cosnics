<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Renderer\ReportRenderer;
use Chamilo\Libraries\File\Path;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResultExporterComponent extends Manager
{

    public function run()
    {
        $this->validateAccess();

        $requests = $this->getEphorusRequestsFromRequest();
        $request = $requests[0];

        $this->getReportExporter()->exportRequestReport($request);
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service\ReportExporter
     */
    protected function getReportExporter()
    {
        return $this->getService('chamilo.application.weblcms.tool.implementation.ephorus.service.report_exporter');
    }
}