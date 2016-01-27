<?php
namespace Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html\Extension;

use Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html\HtmlInlineRenditionImplementation;
use Chamilo\Libraries\File\Path;

class HtmlInlineMp3RenditionImplementation extends HtmlInlineRenditionImplementation
{

    public function render($parameters)
    {
        $object = $this->get_content_object();
        $url = \Chamilo\Core\Repository\Manager :: get_document_downloader_url(
            $object->get_id(),
            $object->calculate_security_code()) . '&display=1';

        $html = array();

        $html[] = '<object type="application/x-shockwave-flash" data="' .
             Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\File', true) .
             'Plugin/Mp3Player/player_mp3_maxi.swf" width="300" height="20">';
        $html[] = '<param name="movie" value="' .
             Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\File', true) .
             'Plugin/Mp3Player/player_mp3_maxi.swf" />';
        $html[] = '<param name="bgcolor" value="#9f0616" />';
        $html[] = '<param name="FlashVars" value="mp3=' . urlencode($url) .
             '&amp;width=300&amp;showstop=1&amp;showinfo=1&amp;showvolume=1&amp;volumewidth=40&amp;bgcolor=9f0616&amp;bgcolor1=c00c2a&amp;bgcolor2=9f0616&amp;sliderovercolor=abb0b3&amp;buttonovercolor=abb0b3" />';
        $html[] = '</object>';

        return implode(PHP_EOL, $html);
    }
}
