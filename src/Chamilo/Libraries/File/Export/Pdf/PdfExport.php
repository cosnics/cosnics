<?php
namespace Chamilo\Libraries\File\Export\Pdf;

use Cezpdf;
use Chamilo\Libraries\File\Export\Export;
use Chamilo\Libraries\File\Path;
use Spipu\Html2Pdf\Html2Pdf;

/**
 * Exports data to Pdf
 *
 * @package Chamilo\Libraries\File\Export\Pdf
 */
class PdfExport extends Export
{
    const EXPORT_TYPE = 'pdf';

    /**
     * convert the HTML of a real page, to a code adapted to HTML2PDF
     *
     * @param string HTML of a real page
     *
     * @return string HTML adapted to HTML2PDF
     */
    public function getHtmlFromPage($html)
    {
        $html = str_replace('<BODY', '<body', $html);
        $html = str_replace('</BODY', '</body', $html);
        // extract the content
        $res = explode('<body', $html);
        if (count($res) < 2)
        {
            return $html;
        }
        $content = '<page' . $res[1];
        $content = explode('</body', $content);
        $content = $content[0] . '</page>';
        // extract the link tags
        preg_match_all('/<link([^>]*)>/isU', $html, $match);
        foreach ($match[0] as $src)
        {
            $content = $src . '</link>' . $content;
        }
        // extract the css style tags
        preg_match_all('/<style[^>]*>(.*)<\/style[^>]*>/isU', $html, $match);
        foreach ($match[0] as $src)
        {
            $content = $src . $content;
        }

        return $content;
    }

    /**
     *
     * @see \Chamilo\Libraries\File\Export\Export::get_type()
     */
    public function get_type()
    {
        return self::EXPORT_TYPE;
    }

    /**
     *
     * @see \Chamilo\Libraries\File\Export\Export::render_data()
     */
    public function render_data()
    {
        $data = $this->get_data();
        if (is_array($data))
        {
            $pdf = new Cezpdf();
            $pdf->selectFont(
                Path::getInstance()->namespaceToFullPath('Chamilo\Configuration') . 'Plugin/ezpdf/fonts/Helvetica.afm'
            );
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
            $pdf = new Html2Pdf('p', 'A4', 'en');
            $pdf->writeHtml($this->getHtmlFromPage($data));

            return $pdf->output('', 'S');
        }
    }
}
