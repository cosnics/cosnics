<?php
namespace Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html\Extension;

use Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html\HtmlInlineRenditionImplementation;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class HtmlInlinePdfRenditionImplementation extends HtmlInlineRenditionImplementation
{

    public function render($parameters)
    {
        $object = $this->get_content_object();
        $url = \Chamilo\Core\Repository\Manager:: get_document_downloader_url(
                $object->get_id(),
                $object->calculate_security_code()
            ) . '&display=1';

        $viewerPath = Path::getInstance()->getResourcesPath(Utilities::COMMON_LIBRARIES, true) .
            'Javascript/Plugin/PDFJS/web/viewer.html';

        $url = $viewerPath . '?file=' . urlencode($url);

        $html = array();

        $html[] = '<div class="pull-right">';
        $html[] = '<a href="' . $url . '" target="_blank">';
        $html[] = '<input type="button" class="btn btn-default" value="' . Translation::getInstance()->getTranslation(
                'OpenInFullScreen', null, 'Chamilo\Core\Repository\ContentObject\File'
            ) . '" />';
        $html[] = '</div>';
        $html[] = '</a>';
        $html[] = '<div class="clearfix"></div>';

        $html[] = '<div style="margin-top: 20px; border: 1px solid grey;"><iframe border="0" style="border: 0;"
                width="100%" height="500"  src="' . $url . '"></iframe></div>';

        return implode(PHP_EOL, $html);
    }
}
