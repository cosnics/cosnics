<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Description\Storage\DataClass\Description;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Common\Rendition\HtmlRenditionImplementation;
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
    const DATA_NODE_ID = 'data-node_id';

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

    /**
     *
     * @var string
     */
    private $prefix;

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
        
        if (! $question instanceof Description)
        {
            $html = array();
            
            // $html[] = '<div class="question" >';
            
            $html[] = '<div class="title">';
            $html[] = '<div class="number">';
            $html[] = '<div class="bevel">';
            $html[] = $this->getQuestionNr() . '.';
            $html[] = '</div>';
            $html[] = '</div>';
            $html[] = '</div>';
            
            $html[] = '<div class="title">';
            $html[] = '<div class="text">';
            $html[] = '<div class="bevel">';
            $html[] = $question->get_question();
            $html[] = '</div>';
            $html[] = '</div>';
            $html[] = '</div>';
            
            $html[] = '<div class="instruction">';
            if ($question->has_instruction())
            {
                $html[] = $question->get_instruction();
            }
            $html[] = '</div>';
            
            $html[] = '<div class="answer">';
            $formValidator->addElement('html', implode(PHP_EOL, $html));
        }
        else
        {
            $html[] = '<div class="clear"></div>';
        }
        
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
    public function getNodeId()
    {
        if ($this->getComplexContentObjectPathNode())
        {
            $nodeId = $this->getComplexContentObjectPathNode()->get_id();
        }
        else
        {
            $nodeId = 0;
        }
        return $nodeId;
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

    /**
     *
     * @return int
     */
    public function getQuestionNr()
    {
        if ($this->getComplexContentObjectPathNode())
        {
            $questionNr = $this->getComplexContentObjectPathNode()->get_question_nr();
        }
        else
        {
            $questionNr = 1;
        }
        
        return $questionNr;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    public function getPrefix()
    {
        if ($this->prefix)
        {
            return $this->prefix;
        }
        else
        {
            return null;
        }
    }

    public function getAttributes()
    {
        $attributes = array();
        $attributes[self :: DATA_NODE_ID] = $this->getNodeId();
        return $attributes;
    }
}