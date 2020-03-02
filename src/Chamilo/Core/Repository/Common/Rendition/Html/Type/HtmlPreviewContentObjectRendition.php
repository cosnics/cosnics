<?php
namespace Chamilo\Core\Repository\Common\Rendition\Html\Type;

use Chamilo\Core\Repository\Common\Rendition\Html\HtmlContentObjectRendition;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\Repository\Common\Rendition\Html\Type
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HtmlPreviewContentObjectRendition extends HtmlContentObjectRendition
{
    public function render()
    {
        $glyph = new FontAwesomeGlyph('image', array('fa-5x'), null, 'fas');
        $html = array();

        $html[] = '<div class="no-preview">';
        $html[] = $glyph->render();
        $html[] = '<br />';
        $html[] = $this->get_text();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function get_text()
    {
        return '<h1>' . Translation::get('NoPreviewAvailable') . '</h1>';
    }
}
