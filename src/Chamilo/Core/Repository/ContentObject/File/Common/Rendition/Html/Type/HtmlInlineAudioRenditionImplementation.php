<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Type;

use Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineRenditionImplementation;
use Chamilo\Libraries\File\Path;

class HtmlInlineAudioRenditionImplementation extends HtmlInlineRenditionImplementation
{

    public function render($parameters)
    {
        $object = $this->get_content_object();
        $url = Path :: getInstance()->getBasePath(true) .
             \Chamilo\Core\Repository\Manager :: get_document_downloader_url(
                $object->get_id(),
                $object->calculate_security_code()) . '&display=1';

        $html = array();

        $html[] = '<object classid="clsid:9BE31822-FDAD-461B-AD51-BE1D1C159921" codebase="http://www.videolan.org/">';
        $html[] = '	<param name="showstatusbar" value="true" />';
        $html[] = '	<param name="showaudiocontrols" value="true" />';
        $html[] = '	<param name="showpositioncontrols" value="true" />';
        $html[] = '	<param name="showcontrols" value="true" />';
        $html[] = '	<param name="autostart" value="true" />';
        $html[] = '	<param name="url" value="' . $url . '" />';
        $html[] = '	<embed  autostart="true"
                            pluginspage="http://www.videolan.org/"
                            showaudiocontrols="true"
                            showcontrols="true"
                            showpositioncontrols="true"
                            showstatusbar="true"
                            src="' . $url . '"
                            type="application/x-vlc-plugin">';
        $html[] = '	</embed>';
        $html[] = '</object>';

        return implode(PHP_EOL, $html);
    }
}
