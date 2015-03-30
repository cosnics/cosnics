<?php
namespace Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html\Type;

use Chamilo\Core\Repository\ContentObject\File\Implementation\Rendition\Html\HtmlInlineRenditionImplementation;
use Chamilo\Libraries\File\Path;

class HtmlInlineImageRenditionImplementation extends HtmlInlineRenditionImplementation
{

    public function render($parameters)
    {
        $object = $this->get_content_object();
        $name = $object->get_filename();

        $url = \Chamilo\Core\Repository\Manager :: get_document_downloader_url($object->get_id());

        $parameters[self :: PARAM_MARGIN_HORIZONTAL] = (int) $parameters[self :: PARAM_MARGIN_HORIZONTAL];
        $parameters[self :: PARAM_MARGIN_VERTICAL] = (int) $parameters[self :: PARAM_MARGIN_VERTICAL];
        $parameters[self :: PARAM_BORDER] = (int) $parameters[self :: PARAM_BORDER];

        $styles = array();
        $styles['border'] = $parameters[self :: PARAM_BORDER] . 'px solid black;';
        $styles['margin'] = $parameters[self :: PARAM_MARGIN_VERTICAL] . 'px ' .
             $parameters[self :: PARAM_MARGIN_HORIZONTAL] . 'px;';

        if ($parameters[self :: PARAM_ALIGN])
        {
            $styles['float'] = $parameters[self :: PARAM_ALIGN] . ';';
        }

        if ($parameters[self :: PARAM_WIDTH])
        {
            $styles['width'] = $parameters[self :: PARAM_WIDTH] . 'px;';
        }

        if ($parameters[self :: PARAM_HEIGHT])
        {
            $styles['height'] = $parameters[self :: PARAM_HEIGHT] . 'px;';
        }

        $styles_string = '';

        foreach ($styles as $name => $value)
        {
            $styles_string .= $name . ': ' . $value;
        }

        $styles_string .= ' ' . $parameters[self :: PARAM_STYLE];

        return '<img    src="' . $url . '&display=1"
                        alt="' . $parameters[self :: PARAM_ALT] . '"
                        title="' . $parameters[self :: PARAM_ALT] . '"
                        style="' . $styles_string . '">';
    }
}
