<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Type;

use Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineRenditionImplementation;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Utilities\Utilities;

class HtmlInlineDefaultRenditionImplementation extends HtmlInlineRenditionImplementation
{

    public function render($parameters)
    {
        $object = $this->get_content_object();
        $name = $object->get_filename();

        $url = \Chamilo\Core\Repository\Manager :: get_document_downloader_url(
            $object->get_id(),
            $object->calculate_security_code());

        $text = '<div><a href="' . Utilities :: htmlentities($url) . '">' . Utilities :: htmlentities($name) . '</a> (' . Filesystem :: format_file_size(
            $object->get_filesize()) . ')</div>';

        $html = '<div style="position: relative; margin: 10px auto; margin-left: -350px; width: 700px;
				left: 50%; right: 50%; border-width: 1px; border-style: solid;
				background-color: #E5EDF9; border-color: #4171B5; padding: 15px; text-align:center;">' . $text . '</div>';

        return $html;
    }
}
