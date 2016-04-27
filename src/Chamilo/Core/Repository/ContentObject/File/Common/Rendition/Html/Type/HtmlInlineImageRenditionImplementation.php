<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Type;

use Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineRenditionImplementation;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class HtmlInlineImageRenditionImplementation extends HtmlInlineRenditionImplementation
{

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineRenditionImplementation::render()
     */
    public function render($parameters)
    {
        $html = array();
        $object = $this->get_content_object();

        if (!in_array($object->get_extension(), array('jpg', 'jpeg', 'gif', 'png', 'svg', 'bmp')))
        {
            $rendition =
                new HtmlInlineDefaultRenditionImplementation($this->get_context(), $this->get_content_object());

            $html[] = $rendition->render($parameters);
            $html[] = $this->renderActions('btn-info');
        }
        else
        {

            $name = $object->get_filename();

            $url = \Chamilo\Core\Repository\Manager:: get_document_downloader_url(
                $object->get_id(),
                $object->calculate_security_code()
            );

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

            $url = $this->getDownloadUrl();
            $label = $object->get_filename() . ' (' . Filesystem :: format_file_size($object->get_filesize()) . ')';

            $html[] =
                '<a href="' . $url . '"><img title="' . Translation:: get('DownloadFile', array('LABEL' => $label)) .
                '" src="' . $url . '&display=1"
                        alt="' . $parameters[self :: PARAM_ALT] . '"
                        title="' . $parameters[self :: PARAM_ALT] . '"
                        style="' . $styles_string . '"></a><br /><br />';
        }

        return implode(PHP_EOL, $html);
    }
}
