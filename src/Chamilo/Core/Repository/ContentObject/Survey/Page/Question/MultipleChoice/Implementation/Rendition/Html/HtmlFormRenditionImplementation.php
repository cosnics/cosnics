<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Implementation\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Storage\DataClass\ComplexMultipleChoice;
use Chamilo\Libraries\Format\Form\FormValidator;

/**
 *
 * @package repository.content_object.survey_multiple_choice_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class HtmlFormRenditionImplementation extends HtmlRenditionImplementation
{

    function render(FormValidator $formvalidator, ComplexMultipleChoice $complex_content_object_item, 
        $answer = null)
    {
        $display_type = ucfirst($this->get_content_object()->get_display_type());
        
        $rendition = ContentObjectRenditionImplementation :: factory(
            $this->get_content_object(), 
            $this->get_format(), 
            ContentObjectRendition :: VIEW_FORM . $display_type, 
            $this->get_context());
        
        $rendition->render($formvalidator, $complex_content_object_item, $answer);
    }
}
?>