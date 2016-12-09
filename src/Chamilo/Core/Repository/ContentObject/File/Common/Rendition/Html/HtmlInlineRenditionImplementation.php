<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\File\Common\Rendition\HtmlRenditionImplementation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class HtmlInlineRenditionImplementation extends HtmlRenditionImplementation
{
    const DEFAULT_HEIGHT = 768;
    const DEFAULT_WIDTH = 1024;
    const PARAM_WIDTH = 'width';
    const PARAM_HEIGHT = 'height';
    const PARAM_BORDER = 'border';
    const PARAM_MARGIN_HORIZONTAL = 'margin-horizontal';
    const PARAM_MARGIN_VERTICAL = 'margin-vertical';
    const PARAM_ALIGN = 'align';
    const PARAM_ALT = 'alt';
    const PARAM_STYLE = 'style';

    /**
     * @param array $parameters
     *
     * @return string
     */
    public function render($parameters)
    {
        return $this->renderInline($parameters);
    }
}
