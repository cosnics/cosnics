<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Implementation\Rendition\Html;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\Format\Form\FormValidator;

/**
 *
 * @package repository.content_object.survey_matrix_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class HtmlFormRenditionImplementation extends HtmlRenditionImplementation
{
    const FORM_NAME = 'question_content_object_rendition_form';

    /**
     *
     * @var FormValidator
     */
    private $formValidator;

    /**
     *
     * @var ComplexContentObjectPathNode
     */
    private $complexContentObjectPathNode;

    function render()
    {
        return $this->initialize()->toHtml();
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Form\FormValidator
     */
    public function initialize()
    {
        $formValidator = $this->getFormValidator();
        $renderer = $formValidator->get_renderer();
        $question = $this->get_content_object();
        
        $html = array();
        $html[] = '<div class="question" >';
        $html[] = '<div class="title">';
        $html[] = '<div class="text">';
        $html[] = '<div class="bevel">';
        $html[] = $question->get_question();
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        $html[] = '<div class="instruction">';
        if ($question->has_instruction())
        {
            $html[] = $question->get_instruction();
        }
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        $html[] = '<div class="answer">';
        
        $html[] = '<div class="clear"></div>';
        $formValidator->addElement('html', implode(PHP_EOL, $html));
        
        return $formValidator;
    }

    /**
     *
     * @param FormValidator $formValidator
     */
    public function setFormValidator(FormValidator $formValidator)
    {
        if (! isset($this->formValidator))
        {
            $this->formValidator = $formValidator;
        }
    }

    public function getFormValidator()
    {
        if (! isset($this->formValidator))
        {
            return new FormValidator(self :: FORM_NAME);
        }
        
        return $this->formValidator;
    }

    /**
     *
     * @return the $complexContentObjectPathNode
     */
    public function getComplexContentObjectPathNode()
    {
        return $this->complexContentObjectPathNode;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Survey\ComplexContentObjectPathNode $complexContentObjectPathNode
     */
    public function setComplexContentObjectPathNode(ComplexContentObjectPathNode $complexContentObjectPathNode)
    {
        $this->complexContentObjectPathNode = $complexContentObjectPathNode;
    }

    /**
     *
     * @return int
     */
    public function getQuestionId()
    {
        if ($this->getComplexContentObjectPathNode())
        {
            $complexQuestion = $this->getComplexContentObjectPathNode()->get_complex_content_object_item();
            $questionId = $complexQuestion->getId();
        }
        else
        {
            $questionId = $this->get_content_object()->getId();
        }
        return $questionId;
    }
}