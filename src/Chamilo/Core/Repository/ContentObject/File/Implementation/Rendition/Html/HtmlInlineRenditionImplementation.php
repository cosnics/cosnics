<?php
namespace Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\Utilities\StringUtilities;

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

    public function render($parameters)
    {
        $object = $this->get_content_object();

        $class = __NAMESPACE__ . '\Extension\HtmlInline' .
             (string) StringUtilities :: getInstance()->createString($object->get_extension())->upperCamelize() .
             'RenditionImplementation';

        if (! class_exists($class))
        {
            $document_type = $object->determine_type();
            $class = __NAMESPACE__ . '\Type\HtmlInline' .
                 (string) StringUtilities :: getInstance()->createString($document_type)->upperCamelize() .
                 'RenditionImplementation';
        }

        $rendition = new $class($this->get_context(), $this->get_content_object());
        return $rendition->render($parameters);
    }
}
