<?php
namespace Chamilo\Core\Reporting\Exporter;

use Chamilo\Core\Reporting\ReportingExporter;

class Pdf extends ReportingExporter
{

    public function export()
    {
        $this->getExporter('Pdf')->send_to_browser($this->get_file_name(), $this->get_template());
    }

    public function save()
    {
        return $this->getExporter('Pdf')->render_data($this->get_template());
    }
}
