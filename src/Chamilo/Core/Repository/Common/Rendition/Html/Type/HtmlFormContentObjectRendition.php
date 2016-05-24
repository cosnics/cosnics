<?php
namespace Chamilo\Core\Repository\Common\Rendition\Html\Type;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Common\Rendition\Html\HtmlContentObjectRendition;
use Chamilo\Libraries\Format\Form\FormValidator;

class HtmlFormContentObjectRendition extends HtmlContentObjectRendition
{
    const FORM_NAME = 'content_rendition_form';

    public function render()
    {
        $object = $this->get_content_object();
        $html = array();
        $html[] = '<div class="panel panel-default">';

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">' . $object->get_icon_image() . ' ' . $object->get_title() . '</h3>';
        $html[] = '</div>';

        $html[] = ContentObjectRenditionImplementation :: launch(
            $object,
            ContentObjectRendition :: FORMAT_HTML,
            ContentObjectRendition :: VIEW_DESCRIPTION,
            $this->get_context());

        $html[] = '</div>';

        $formValidator = new FormValidator(self :: FORM_NAME);
        $formValidator->addElement('html', implode(PHP_EOL, $html));

        return $formValidator->toHtml();
    }
}
