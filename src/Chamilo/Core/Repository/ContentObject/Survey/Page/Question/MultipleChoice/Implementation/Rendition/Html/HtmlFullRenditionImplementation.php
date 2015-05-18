<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Implementation\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Storage\DataClass\MultipleChoice;

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
        $formValidator = new FormValidator('mc_full_rendition');
             
        $formRendition = ContentObjectRenditionImplementation :: factory(
            $this->get_content_object(),
            ContentObjectRendition :: FORMAT_HTML,
            ContentObjectRendition :: VIEW_FORM,
            MultipleChoice :: package());
        
        $formRendition->setFormValidator($formValidator);
        $formValidator = $formRendition->initialize();
        
        $html[] = ContentObjectRendition :: launch($this);
        $html[] = '<h4>' . Translation :: get('QuestionPreview') . '</h4>';
        $html[] = '<div style="border: 1px solid whitesmoke; padding: 10px; margin-bottom: 10px;">';
        $html[] = $formValidator->toHtml();
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }

}
?>