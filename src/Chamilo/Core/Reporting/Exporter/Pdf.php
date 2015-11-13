<?php
namespace Chamilo\Core\Reporting\Exporter;

use Chamilo\Core\Reporting\ReportingExporter;
use Chamilo\Libraries\File\Export\Export;

class Pdf extends ReportingExporter
{

    public function export()
    {
        $template = $this->get_template();

        // $data = $template->export();
        // $export = Export :: factory('pdf', $data);
        $export = Export :: factory('pdf', $template);
        $export->set_filename($this->get_file_name());
        $export->send_to_browser();
    }

    public function save()
    {
        $template = $this->get_template();

        // $data = $template->export();
        // $export = Export :: factory('pdf', $data);
        $export = Export :: factory('pdf', $template);
        $export->set_filename($this->get_file_name());
        return $export->render_data();
    }
}
