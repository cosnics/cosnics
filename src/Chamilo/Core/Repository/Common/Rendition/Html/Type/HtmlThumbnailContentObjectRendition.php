<?php
namespace Chamilo\Core\Repository\Common\Rendition\Html\Type;

use Chamilo\Libraries\Format\Theme;

class HtmlThumbnailContentObjectRendition extends HtmlPreviewContentObjectRendition
{

    public function get_class()
    {
        return 'no_thumbnail';
    }

    public function get_image()
    {
        return Theme :: getInstance()->getCommonImage('NoThumbnail');
    }

    public function get_text()
    {
        return '';
    }
}
