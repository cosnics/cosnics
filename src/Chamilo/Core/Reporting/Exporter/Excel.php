<?php
namespace Chamilo\Core\Reporting\Exporter;

use Chamilo\Core\Reporting\ReportingExporter;

class Excel extends ReportingExporter
{

    public function export()
    {
        $this->getExporter('Excel')->send_to_browser($this->get_file_name(), $this->get_template());
    }

    public function save()
    {
        return $this->getExporter('Excel')->render_data($this->get_template());
    }
}
