<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchNumericQuestion\Form;

use Chamilo\Core\Repository\ContentObject\AssessmentMatchNumericQuestion\Storage\DataClass\AssessmentMatchNumericQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchNumericQuestion\Storage\DataClass\AssessmentMatchNumericQuestionOption;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\AssessmentMatchNumericQuestion\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class AssessmentMatchNumericQuestionForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent::build_creation_form($this->getDescriptionHtmlEditorOptions());
        $this->buildBasicQuestionForm();
    }

    protected function build_editing_form()
    {
        parent::build_editing_form($this->getDescriptionHtmlEditorOptions());
        $this->buildBasicQuestionForm();
    }

    protected function getDescriptionHtmlEditorOptions()
    {
        $htmlEditorOptions = array();
        $htmlEditorOptions['width'] = '100%';
        $htmlEditorOptions['height'] = '100';
        $htmlEditorOptions['collapse_toolbar'] = false;
        $htmlEditorOptions['show_tags'] = false;
        $htmlEditorOptions['toolbar_set'] = 'RepositoryQuestion';
        
        return $htmlEditorOptions;
    }

    protected function buildBasicQuestionForm()
    {
        $this->add_options();
        
        $this->addElement('category', Translation::get('Configuration'));
        
        $selectOptions = array();
        $selectOptions[AssessmentMatchNumericQuestion::TOLERANCE_TYPE_ABSOLUTE] = Translation::get('Absolute');
        $selectOptions[AssessmentMatchNumericQuestion::TOLERANCE_TYPE_RELATIVE] = Translation::get('Relative');
        
        $this->addElement(
            'select', 
            AssessmentMatchNumericQuestion::PROPERTY_TOLERANCE_TYPE, 
            Translation::get('ToleranceType'), 
            $selectOptions);
        
        $this->addElement(
            'html', 
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath(
                    'Chamilo\Core\Repository\ContentObject\AssessmentMatchNumericQuestion', 
                    true) . 'AssessmentMatchNumericQuestion.js'));
    }

    public function setDefaults($defaults = array ())
    {
        if (! $this->isSubmitted())
        {
            $object = $this->get_content_object();
            $defaults[AssessmentMatchNumericQuestion::PROPERTY_HINT] = $object->get_hint();
            
            if ($object->get_number_of_options() != 0)
            {
                $options = $object->get_options();
                
                foreach ($options as $index => $option)
                {
                    $defaults[AssessmentMatchNumericQuestionOption::PROPERTY_VALUE][$index] = $option->get_value();
                    $defaults[AssessmentMatchNumericQuestionOption::PROPERTY_TOLERANCE][$index] = $option->get_tolerance() ? $option->get_tolerance() : 0;
                    $defaults[AssessmentMatchNumericQuestionOption::PROPERTY_SCORE][$index] = !is_null($option->get_score()) ? $option->get_score() : 1;
                    $defaults[AssessmentMatchNumericQuestionOption::PROPERTY_FEEDBACK][$index] = $option->get_feedback();
                }
                
                $defaults[AssessmentMatchNumericQuestion::PROPERTY_TOLERANCE_TYPE] = $object->get_tolerance_type();
            }
            else
            {
                $number_of_options = (int) Session::retrieve('match_number_of_options');
                
                for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
                {
                    $defaults[AssessmentMatchNumericQuestionOption::PROPERTY_SCORE][$option_number] = 1;
                    $defaults[AssessmentMatchNumericQuestionOption::PROPERTY_TOLERANCE][$option_number] = 0;
                }
            }
        }
        
        parent::setDefaults($defaults);
    }

    public function create_content_object()
    {
        $this->set_content_object(new AssessmentMatchNumericQuestion());
        $this->processSubmittedData();
        
        return parent::create_content_object();
    }

    public function update_content_object()
    {
        $this->processSubmittedData();
        return parent::update_content_object();
    }

    public function processSubmittedData()
    {
        $values = $this->exportValues();
        $object = $this->get_content_object();
        
        $object->set_hint($values[AssessmentMatchNumericQuestion::PROPERTY_HINT]);
        $object->set_tolerance_type($values[AssessmentMatchNumericQuestion::PROPERTY_TOLERANCE_TYPE]);
        
        $this->add_options_to_object();
    }

    private function add_options_to_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        
        $options = array();
        
        foreach ($values[AssessmentMatchNumericQuestionOption::PROPERTY_VALUE] as $option_id => $value)
        {
            $tolerance = $values[AssessmentMatchNumericQuestionOption::PROPERTY_TOLERANCE][$option_id];
            $score = $values[AssessmentMatchNumericQuestionOption::PROPERTY_SCORE][$option_id];
            $feedback = $values[AssessmentMatchNumericQuestionOption::PROPERTY_FEEDBACK][$option_id];
            
            $options[] = new AssessmentMatchNumericQuestionOption($value, $tolerance, $score, $feedback);
        }
        
        $object->set_options($options);
    }

    public function validate()
    {
        $extraOptionRequested = Request::post('add');
        $removedOptions = Request::post('remove');
        
        if (isset($extraOptionRequested) || isset($removedOptions))
        {
            return false;
        }
        
        return parent::validate();
    }

    public function setOptionsSessionValues()
    {
        if (! $this->isSubmitted())
        {
            Session::unregister('match_number_of_options');
            Session::unregister('match_skip_options');
        }
        
        Session::registerIfNotSet('match_number_of_options', 1);
        Session::registerIfNotSet('match_skip_options', array());
        
        $extraOptionRequested = Request::post('add');
        $removedOptions = Request::post('remove');
        
        if (isset($extraOptionRequested))
        {
            Session::register('match_number_of_options', (Session::retrieve('match_number_of_options') + 1));
        }
        
        if (isset($removedOptions))
        {
            $indexes = array_keys($removedOptions);
            $skippedOptions = Session::retrieve('match_skip_options');
            $skippedOptions[] = $indexes[0];
            
            Session::register('match_skip_options', $skippedOptions);
        }
        
        $object = $this->get_content_object();
        
        if (! $this->isSubmitted() && $object->get_number_of_options() != 0)
        {
            Session::register('match_number_of_options', $object->get_number_of_options());
        }
    }

    /**
     * Adds the form-fields to the form to provide the possible options for this match question
     */
    private function add_options()
    {
        $renderer = $this->get_renderer();
        $this->setOptionsSessionValues();
        
        $number_of_options = (int) Session::retrieve('match_number_of_options');
        $skippedOptions = Session::retrieve('match_skip_options');
        
        $this->addElement('category', Translation::get('PossibleAnswers'));
        $this->addElement(
            'hidden', 
            'match_number_of_options', 
            $number_of_options, 
            array('id' => 'match_number_of_options'));
        
        $htmlEditorOptions = array();
        $htmlEditorOptions['width'] = '100%';
        $htmlEditorOptions['height'] = '65';
        $htmlEditorOptions['collapse_toolbar'] = true;
        $htmlEditorOptions['show_tags'] = false;
        $htmlEditorOptions['toolbar_set'] = 'RepositoryQuestion';
        
        $this->addElement('html', '<table class="table table-assessment-question-form"><tbody>');
        
        $optionLabelCounter = 1;
        
        for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (! in_array($option_number, $skippedOptions))
            {
                $this->addElement('html', '<tr data-option-id="' . $option_number . '">');
                $this->addElement(
                    'html', 
                    '<td class="table-cell-selection cell-stat-x3">' . $optionLabelCounter . '.</td>');
                
                $this->addElement('html', '<td>');
                
                // Answer
                $this->addElement(
                    'textarea', 
                    AssessmentMatchNumericQuestionOption::PROPERTY_VALUE . '[' . $option_number . ']', 
                    Translation::get('Answer'), 
                    array('class' => 'form-control', 'style' => 'height: 80px;'));
                
                $renderer->setElementTemplate(
                    '<div class="option-answer-field" data-element="' .
                         AssessmentMatchNumericQuestionOption::PROPERTY_VALUE . '[' . $option_number . ']' .
                         '">{element}</div>', 
                        AssessmentMatchNumericQuestionOption::PROPERTY_VALUE . '[' . $option_number . ']');
                
                // Score
                $this->addElement(
                    'text', 
                    AssessmentMatchNumericQuestionOption::PROPERTY_SCORE . '[' . $option_number . ']', 
                    Translation::get('Score'), 
                    'size="2"  class="input_numeric form-control"');
                
                $renderer->setElementTemplate(
                    '<div class="option-score-field assessment_match_question_score_container form-inline" data-element="' .
                         AssessmentMatchNumericQuestionOption::PROPERTY_SCORE . '[' . $option_number . ']' .
                         '"><label>{label}:</label> {element}</div>', 
                        AssessmentMatchNumericQuestionOption::PROPERTY_SCORE . '[' . $option_number . ']');

                // Feedback
                $this->add_html_editor(
                    AssessmentMatchNumericQuestionOption::PROPERTY_FEEDBACK . '[' . $option_number . ']',
                    Translation::get('Feedback'),
                    false,
                    $htmlEditorOptions);

                $renderer->setElementTemplate(
                    '<div class="option-feedback-field form-assessment-extra-container" data-element="' .
                    AssessmentMatchNumericQuestionOption::PROPERTY_FEEDBACK . '[' . $option_number . ']' .
                    '"><label>{label}</label>{element}</div>',
                    AssessmentMatchNumericQuestionOption::PROPERTY_FEEDBACK . '[' . $option_number . ']');

                // Tolerance
                $this->addElement(
                    'text', 
                    AssessmentMatchNumericQuestionOption::PROPERTY_TOLERANCE . '[' . $option_number . ']', 
                    Translation::get('Tolerance'), 
                    'size="2"  class="input_numeric form-control"');
                
                $renderer->setElementTemplate(
                    '<div class="option-tolerance-field form-assessment-extra-container form-inline" data-element="' .
                         AssessmentMatchNumericQuestionOption::PROPERTY_TOLERANCE . '[' . $option_number . ']' .
                         '"><label>{label}:</label> {element}</div>', 
                        AssessmentMatchNumericQuestionOption::PROPERTY_TOLERANCE . '[' . $option_number . ']');
                
                $this->addElement('html', '</td>');
                
                $this->addElement('html', '<td class="table-cell-action cell-stat text-right">');
                
                $actionButtons = array();
                
                if ($number_of_options - count($skippedOptions) > 1)
                {
                    $removeClass = 'text-danger';
                }
                else
                {
                    $removeClass = 'text-muted';
                }
                
                $actionButtons[] = '<span data-option-id="' . $option_number .
                     '" class="option-action option-feedback fa fa-comment text-primary"></span>';
                $actionButtons[] = '<span data-option-id="' . $option_number .
                     '" class="option-action option-tolerance fa fa-magnet text-primary"></span>';
                $actionButtons[] = '<span data-option-id="' . $option_number .
                     '" class="option-action option-remove fa fa-trash ' . $removeClass . '"></span>';
                
                $this->addElement('html', implode('&nbsp;&nbsp;', $actionButtons));
                
                $this->addElement('html', '</td>');
                $this->addElement('html', '</tr>');
                
                $optionLabelCounter ++;
            }
        }
        
        $this->addElement('html', '</tbody></table>');
        
        $this->addOptionsButtons();
    }

    protected function addOptionsButtons()
    {
        $buttonToolBar = new ButtonToolBar();
        
        $buttonToolBar->addItem(
            new Button(
                Translation::get('AddMatchNumericOption'), 
                new BootstrapGlyph('plus'), 
                ' ', 
                Button::DISPLAY_ICON_AND_LABEL, 
                false, 
                'btn-primary add-option'));
        
        $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);
        
        $this->addElement('html', $buttonToolBarRenderer->render());
    }

    public function prepareTabs()
    {
        $this->addDefaultTab();
        $this->addHintTab();
        $this->addInstructionsTab();
        $this->addMetadataTabs();
    }

    public function addHintTab()
    {
        $this->getTabsGenerator()->add_tab(
            new DynamicFormTab(
                'add-hint', 
                Translation::get('AddHint'), 
                new FontAwesomeGlyph('magic', array('ident-sm')), 
                'buildHintForm'));
    }

    public function buildHintForm()
    {
        $htmlEditorOptions = array();
        $htmlEditorOptions['width'] = '100%';
        $htmlEditorOptions['height'] = '100';
        $htmlEditorOptions['collapse_toolbar'] = true;
        $htmlEditorOptions['show_tags'] = false;
        $htmlEditorOptions['toolbar_set'] = 'RepositoryQuestion';
        
        $this->add_html_editor(
            AssessmentMatchNumericQuestion::PROPERTY_HINT, 
            Translation::get('Hint', array(), ClassnameUtilities::getInstance()->getNamespaceFromObject($this)), 
            false, 
            $htmlEditorOptions);
    }
}
