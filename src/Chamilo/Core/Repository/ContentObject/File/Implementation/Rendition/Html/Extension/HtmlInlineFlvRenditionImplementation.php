<?php
namespace Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html\Extension;

use Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html\HtmlInlineRenditionImplementation;
use Chamilo\Libraries\File\Path;

class HtmlInlineFlvRenditionImplementation extends HtmlInlineRenditionImplementation
{

    public function render($parameters)
    {
        $object = $this->get_content_object();
        $url = Path :: getInstance()->getBasePath(true) .
             \Chamilo\Core\Repository\Manager :: get_document_downloader_url($object->get_id()) . '&display=1';
        
        $html = array();
        
        $html[] = '<object codebase="http://www.videolan.org/" height="' . $parameters[self :: PARAM_HEIGHT] .
             '" width="' . $parameters[self :: PARAM_WIDTH] . '">';
        $html[] = '	<param name="showstatusbar" value="true" />';
        $html[] = '	<param name="showgotobar" value="true" />';
        $html[] = '	<param name="showaudiocontrols" value="true" />';
        $html[] = '	<param name="showtracker" value="true" />';
        $html[] = '	<param name="showpositioncontrols" value="true" />';
        $html[] = '	<param name="showcontrols" value="true" />';
        $html[] = '	<param name="autostart" value="true" />';
        $html[] = '	<param name="url" value="' . $url . '" />';
        $html[] = '	<param name="height" value="' . $parameters[self :: PARAM_HEIGHT] . '" />';
        $html[] = '	<param name="width" value="' . $parameters[self :: PARAM_WIDTH] . '" />';
        $html[] = '	<embed  autosize="true"
                            autostart="true"
                            height="' . $parameters[self :: PARAM_HEIGHT] . '"
                            pluginspage="http://www.videolan.org/"
                            showaudiocontrols="true"
                            showcontrols="true"
                            showgotobar="true"
                            showpositioncontrols="true"
                            showstatusbar="true"
                            showtracker="true"
                            src="' . $url . '"
                            type="video/x-flv"
                            width="' . $parameters[self :: PARAM_WIDTH] . '">';
        $html[] = '	</embed>';
        $html[] = '</object>';
        
        return implode(PHP_EOL, $html);
    }
}
