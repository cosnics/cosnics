<?php
namespace Chamilo\Core\Reporting\Exporter;

use Chamilo\Core\Reporting\ReportingExporter;
use Chamilo\Libraries\File\Export\Export;

class Excel extends ReportingExporter
{

    public function export()
    {
        $template = $this->get_template();
        
        // $data = $template->export();
        // $export = Export::factory('excel', $data);
        $export = Export::factory('excel', $template);
        $export->set_filename($this->get_file_name());
        $export->send_to_browser();
    }

    public function save()
    {
        $template = $this->get_template();
        
        // $data = $template->export();
        // $export = Export::factory('excel', $data);
        $export = Export::factory('excel', $template);
        $export->set_filename($this->get_file_name());
        return $export->render_data();
    }
}
