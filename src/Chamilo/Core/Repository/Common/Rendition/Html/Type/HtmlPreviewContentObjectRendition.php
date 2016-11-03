<?php
namespace Chamilo\Core\Repository\Common\Rendition\Html\Type;

use Chamilo\Core\Repository\Common\Rendition\Html\HtmlContentObjectRendition;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class HtmlPreviewContentObjectRendition extends HtmlContentObjectRendition
{

    public function get_class()
    {
        return 'no_preview';
    }

    public function get_image()
    {
        return Theme :: getInstance()->getCommonImage('Preview');
    }

    public function get_text()
    {
        return '<h1>' . Translation :: get('NoPreviewAvailable') . '</h1>';
    }

    public function render()
    {
        $html = array();

        $html[] = '<div class="no-preview">';
        $html[] = '<span class="glyphicon glyphicon-picture"></span>';
        $html[] = '<br />';
        $html[] = $this->get_text();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
