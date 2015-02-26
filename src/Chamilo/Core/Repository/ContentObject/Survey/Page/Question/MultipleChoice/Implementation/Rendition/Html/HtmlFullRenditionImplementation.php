<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Implementation\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Storage\DataClass\ComplexMultipleChoice;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package repository.content_object.survey_multiple_choice_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class HtmlFullRenditionImplementation extends HtmlRenditionImplementation
{

    private $formvalidator = null;

    function render()
    {
        $rendition = ContentObjectRenditionImplementation :: factory(
            $this->get_content_object(), 
            $this->get_format(), 
            ContentObjectRendition :: VIEW_FORM, 
            $this);
        
        $rendition->render($this->get_formvalidator(), $this->get_complex_content_object_item());
        
        $html[] = ContentObjectRendition :: launch($this);
        $html[] = '<h4>' . Translation :: get('QuestionPreview') . '</h4>';
        $html[] = '<div style="border: 1px solid whitesmoke; padding: 10px; margin-bottom: 10px;">';
        $html[] = $this->get_formvalidator()->toHtml();
        $html[] = '</div>';
        
        return implode("\n", $html);
    }

    private function get_complex_content_object_item()
    {
        $complex_content_object_item = new ComplexMultipleChoice();
        $complex_content_object_item->set_id(1);
        $complex_content_object_item->set_ref_object($this->get_content_object());
        return $complex_content_object_item;
    }

    private function get_formvalidator()
    {
        if (! $this->formvalidator)
        {
            $this->formvalidator = new FormValidator('mc_question_preview');
        }
        return $this->formvalidator;
    }
}
?>