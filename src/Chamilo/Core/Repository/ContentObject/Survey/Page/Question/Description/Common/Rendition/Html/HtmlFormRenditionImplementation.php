<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Description\Common\Rendition\Html;

/**
 *
 * @package repository.content_object.survey_matrix_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class HtmlFormRenditionImplementation extends \Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Common\Rendition\Html\HtmlFormRenditionImplementation
{

    /**
     *
     * @return \Chamilo\Libraries\Format\Form\FormValidator
     */
    public function initialize()
    {
        $formValidator = parent::initialize();
        $renderer = $formValidator->get_renderer();
        $questionId = $this->getQuestionId();
        
        $html = array();
        
        if ($this->get_content_object()->has_description())
        {
            
            $html[] = '<div class="survey">';
            $html[] = '<div class="information">';
            $html[] = $this->get_content_object()->get_description();
            
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div>';
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div>';
            
            $html[] = '<div class="clear"></div>';
        }
        
        $formValidator->addElement('html', implode(PHP_EOL, $html));
        return $formValidator;
    }
}