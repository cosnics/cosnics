<?php
namespace Chamilo\Libraries\File\Export\Pdf;

use Cezpdf;
use Chamilo\Libraries\File\Export\Export;
use Chamilo\Libraries\File\Path;
use HTML2PDF;

/**
 * Exports data to Pdf
 */
class PdfExport extends Export
{
    const EXPORT_TYPE = 'pdf';

    public function render_data()
    {
        $data = $this->get_data();
        if (is_array($data))
        {
            require_once Path::getInstance()->getPluginPath() . 'ezpdf/class.ezpdf.php';
            $pdf = & new Cezpdf();
            $pdf->selectFont(Path::getInstance()->getPluginPath() . 'ezpdf/fonts/Helvetica.afm');
            foreach ($data as $datapair)
            {
                $title = $datapair['key'];
                $table_data = $datapair['data'];
                $pdf->ezTable($table_data, null, $title, array('fontSize' => 5));
            }
            return $pdf->ezOutput();
        }
        else
        {
            $pdf = new HTML2PDF('p', 'A4', 'en');
            $pdf->WriteHTML($pdf->getHtmlFromPage($data));
            return $pdf->Output('', 'S');
        }
    }

    public function get_type()
    {
        return self::EXPORT_TYPE;
    }
}
