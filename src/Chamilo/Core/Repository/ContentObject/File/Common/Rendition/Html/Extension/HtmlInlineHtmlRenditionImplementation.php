<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension;

use Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineRenditionImplementation;

class HtmlInlineHtmlRenditionImplementation extends HtmlInlineRenditionImplementation
{

    public function render($parameters)
    {
        $object = $this->get_content_object();
        $url = \Chamilo\Core\Repository\Manager :: get_document_downloader_url(
            $object->get_id(),
            $object->calculate_security_code()) . '&display=1';

        $html = array();

        $html[] = '<div style="border: 1px solid grey;"><iframe border="0" style="border: 0;"
                width="100%" height="500"  src="' . $url . '"></iframe></div><br />';

        $html[] = $this->renderDownloadAction();

        return implode(PHP_EOL, $html);
    }
}
