<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Type;

use Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineRenditionImplementation;

class HtmlInlineDefaultRenditionImplementation extends HtmlInlineRenditionImplementation
{

    public function render($parameters)
    {
        return $this->renderDownloadAction('btn-info');
    }
}
