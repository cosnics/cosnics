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
        $html[] = '<div class="content_object" style="background-image: url(' . $object->get_icon_path() . ');">';
        $html[] = '<div class="title">' . $object->get_title() . '</div>';
        $html[] = ContentObjectRenditionImplementation :: launch(
            $object, 
            ContentObjectRendition :: FORMAT_HTML, 
            ContentObjectRendition :: VIEW_DESCRIPTION, 
            $this->get_context());
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        $formValidator =new FormValidator(self :: FORM_NAME);
        $formValidator->addElement('html', implode(PHP_EOL, $html));
        
        return $formValidator->toHtml();
    }
    
}
