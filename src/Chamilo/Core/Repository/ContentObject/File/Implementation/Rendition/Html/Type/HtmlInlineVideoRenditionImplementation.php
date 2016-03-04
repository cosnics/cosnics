<?php
namespace Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html\Type;

use Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html\HtmlInlineRenditionImplementation;
use Chamilo\Libraries\File\Path;

class HtmlInlineVideoRenditionImplementation extends HtmlInlineRenditionImplementation
{
    const DEFAULT_HEIGHT = 500;
    const DEFAULT_WIDTH = 500;

    public function render($parameters)
    {
        $object = $this->get_content_object();
        $url = Path :: getInstance()->getBasePath(true) .
             \Chamilo\Core\Repository\Manager :: get_document_downloader_url(
                $object->get_id(),
                $object->calculate_security_code()) . '&display=1';

        $height = $parameters[self :: PARAM_HEIGHT] ? $parameters[self :: PARAM_HEIGHT] : self :: DEFAULT_HEIGHT;
        $width = $parameters[self :: PARAM_WIDTH] ? $parameters[self :: PARAM_WIDTH] : self :: DEFAULT_WIDTH;

        $html = array();

        $html[] = '<object classid="clsid:9BE31822-FDAD-461B-AD51-BE1D1C159921" codebase="http://www.videolan.org/" height="' .
             $parameters[self :: PARAM_HEIGHT] . '" width="' . $parameters[self :: PARAM_WIDTH] . '">';
        $html[] = '	<param name="showstatusbar" value="true" />';
        $html[] = '	<param name="showgotobar" value="true" />';
        $html[] = '	<param name="showaudiocontrols" value="true" />';
        $html[] = '	<param name="showtracker" value="true" />';
        $html[] = '	<param name="showpositioncontrols" value="true" />';
        $html[] = '	<param name="showcontrols" value="true" />';
        $html[] = '	<param name="autostart" value="true" />';
        $html[] = '	<param name="url" value="' . $url . '" />';
        $html[] = '	<param name="height" value="' . $height . '" />';
        $html[] = '	<param name="width" value="' . $width . '" />';
        $html[] = '	<param name="allowFullScreen" value="true" />';
        $html[] = '	<embed  autosize="true"
                            autostart="true"
                            height="' . $height . '"
                            pluginspage="http://www.videolan.org/"
                            showaudiocontrols="true"
                            showcontrols="true"
                            showgotobar="true"
                            showpositioncontrols="true"
                            showstatusbar="true"
                            showtracker="true"
                            src="' . $url . '"
                            type="application/x-vlc-plugin"
                            width="' . $width . '">';
        $html[] = '	</embed>';
        $html[] = '</object>';

        return implode(PHP_EOL, $html);
    }
}
