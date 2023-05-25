<?php
namespace Chamilo\Libraries\File\Export\Pdf;

use Cezpdf;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Export\Export;
use Chamilo\Libraries\File\SystemPathBuilder;
use Spipu\Html2Pdf\Html2Pdf;

/**
 * @package Chamilo\Libraries\File\Export\Pdf
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PdfExport extends Export
{
    protected SystemPathBuilder $systemPathBuilder;

    public function __construct(ConfigurablePathBuilder $configurablePathBuilder, SystemPathBuilder $systemPathBuilder)
    {
        parent::__construct($configurablePathBuilder);

        $this->systemPathBuilder = $systemPathBuilder;
    }

    public function getHtmlFromPage(string $html): string
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
        preg_match_all('/<link([^>]*)>/iU', $html, $match);
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

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }

    /**
     * @throws \Spipu\Html2Pdf\Exception\Html2PdfException
     */
    public function render_data($data): string
    {
        if (is_array($data))
        {
            $pdf = new Cezpdf();
            $pdf->selectFont(
                $this->getSystemPathBuilder()->namespaceToFullPath('Chamilo\Configuration') .
                'Plugin/ezpdf/fonts/Helvetica.afm'
            );
            foreach ($data as $datapair)
            {
                $title = $datapair['key'];
                $table_data = $datapair['data'];
                $pdf->ezTable($table_data, null, $title, ['fontSize' => 5]);
            }

            return $pdf->ezOutput();
        }
        else
        {
            $pdf = new Html2Pdf('p', 'A4', 'en');
            $pdf->writeHTML($this->getHtmlFromPage($data));

            return $pdf->output('', 'S');
        }
    }
}
