<?php
namespace Chamilo\Core\Repository\ContentObject\Office365Video\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\Office365Video\Common\Rendition\HtmlRenditionImplementation;

class HtmlInlineRenditionImplementation extends HtmlRenditionImplementation
{
    const PARAM_WIDTH = 'width';
    const PARAM_HEIGHT = 'height';

    public function render($parameters)
    {
        return $this->get_video_element($parameters[self::PARAM_WIDTH], $parameters[self::PARAM_HEIGHT]);
    }
}
