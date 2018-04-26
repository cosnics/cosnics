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

        $content_object = $this->getContentObjectRepository()->findById($request->get_content_object_id());

        $html = array();
        $html[] = '<html><head>';
        $html[] = '<style type="text/css">' . file_get_contents(
                Path::getInstance()->getBasePath(true) .
                'application/weblcms/tool/ephorus/ephorus_request/resources/css/report.css'
            ) . '</style>';
        $html[] = '</head><body>';

        $request = $requests[0];

        $html[] = $this->getReportRenderer()->renderRequestReport($request);
        $html[] = '</body></html>';

        $unique_file_name = \Chamilo\Libraries\File\Filesystem::create_unique_name(
            Path::getInstance()->getTemporaryPath(),
            $content_object->get_title() . '.html'
        );

        $full_file_name = Path::getInstance()->getTemporaryPath() . $unique_file_name;
        \Chamilo\Libraries\File\Filesystem::create_dir(dirname($full_file_name));
        \Chamilo\Libraries\File\Filesystem::write_to_file($full_file_name, implode(PHP_EOL, $html));
        \Chamilo\Libraries\File\Filesystem::file_send_for_download($full_file_name, true);
        \Chamilo\Libraries\File\Filesystem::remove($full_file_name);
    }
}