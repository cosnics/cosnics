<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Implementation\Rendition\Html;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\File\Path;

/**
 *
 * @package repository.content_object.survey_matrix_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class HtmlFormRenditionImplementation extends HtmlRenditionImplementation
{
    const FORM_NAME = 'matching_content_object_rendition_form';

    
    private $match_objects;
    
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
        $formvalidator = $this->getFormValidator();
        $question = $this->get_content_object();
        
        if ($this->getComplexContentObjectPathNode())
        {
            $complex_question = $this->getComplexContentObjectPathNode()->get_complex_content_object_item();
            $questionId = $complex_question->get_id();
        }
        else
        {
            $questionId = $question->get_id();
        }
        
        $this->add_matches($formvalidator, $question);
        $this->add_options($formvalidator, $question, $questionId);
    
        return $formvalidator;
    }
    
    function add_matches($formvalidator, $question)
    {
        $renderer = $formvalidator->get_renderer();
    
        $table_header = array();
        $table_header[] = '<table class="data_table take_survey">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th class="info" >' . Translation :: get('PossibleMatches') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formvalidator->addElement('html', implode(PHP_EOL, $table_header));
    
        $matches = $question->get_matches();
        
        $match_label = 'A';
        $index = 0;
        while ($match = $matches->next_result())
        {
            $this->match_objects[] = $match;
            $group = array();
            $group[] = $formvalidator->createElement('static', null, null, $match_label);
            $group[] = $formvalidator->createElement('static', null, null, $match->get_value());
            $formvalidator->addGroup($group, 'match_' . $match_label, null, '', false);
    
            $renderer->setElementTemplate(
                '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>',
                'match_' . $match_label);
            $renderer->setGroupElementTemplate('<td>{element}</td>', 'match_' . $match_label);
            $match_label ++;
            $index ++;
        }
    
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
            
        $formvalidator->addElement('html', implode(PHP_EOL, $table_footer));
    }
    
    function add_options($formvalidator, $question, $questionId)
    {
        $renderer = $formvalidator->get_renderer();
    
        $table_header = array();
        $table_header[] = '<table class="data_table take_survey">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th class="info" colspan="2">' . Translation :: get('ChooseYourOptionMatch') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formvalidator->addElement('html', implode(PHP_EOL, $table_header));
    
        $options = $question->get_options();
        $matches = $this->match_objects;
    
        $match_options = array();
        $match_label = 'A';
        foreach ($matches as $index => $match)
        {
            $match_options[$match->get_id()] = $match_label;
            $match_label ++;
        }
        
        $option_count = 0;
        while ($option = $options->next_result())
        {
            $option_id = $option->get_id();
            $option_name = $questionId . '_' . $option_id;
            
            $group = array();
            $option_number = ($option_count + 1) . '.';
            $group[] = $formvalidator->createElement('static', null, null, $option_number);
            $group[] = $formvalidator->createElement('static', null, null, $option->get_value());
            $group[] = $formvalidator->createElement('select', $option_name, null, $match_options);
    
            $formvalidator->addGroup($group, 'group_' . $option_name, null, '', false);
            $renderer->setElementTemplate(
                '<tr class="' . ($option_count % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>',
                'group_' . $option_name);
            $renderer->setGroupElementTemplate('<td>{element}</td>', 'group_' . $option_name);
            $option_count ++;
        }
    
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode(PHP_EOL, $table_footer));
        $namespace = ClassnameUtilities :: getInstance()->getNamespaceParent(__NAMESPACE__, 1);
        $formvalidator->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath($namespace, true) . 'MatchingQuestionDisplay.js'));
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
}