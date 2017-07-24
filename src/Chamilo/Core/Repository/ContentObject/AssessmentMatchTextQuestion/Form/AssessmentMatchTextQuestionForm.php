<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchTextQuestion\Form;

use Chamilo\Core\Repository\ContentObject\AssessmentMatchTextQuestion\Storage\DataClass\AssessmentMatchTextQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchTextQuestion\Storage\DataClass\AssessmentMatchTextQuestionOption;
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
 * @package repository.lib.content_object.match_text_question
 */
class AssessmentMatchTextQuestionForm extends ContentObjectForm
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
        
        $this->addElement(
            'checkbox', 
            AssessmentMatchTextQuestion::PROPERTY_USE_WILDCARDS, 
            Translation::get('UseWildcards'));
        
        $this->addElement('checkbox', AssessmentMatchTextQuestion::PROPERTY_IGNORE_CASE, Translation::get('IgnoreCase'));
        
        $this->addElement(
            'html', 
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath(
                    'Chamilo\Core\Repository\ContentObject\AssessmentMatchTextQuestion', 
                    true) . 'AssessmentMatchTextQuestion.js'));
    }

    public function setDefaults($defaults = array ())
    {
        if (! $this->isSubmitted())
        {
            $object = $this->get_content_object();
            $defaults[AssessmentMatchTextQuestion::PROPERTY_HINT] = $object->get_hint();
            
            if ($object->get_number_of_options() != 0)
            {
                $options = $object->get_options();
                
                foreach ($options as $index => $option)
                {
                    $defaults[AssessmentMatchTextQuestionOption::PROPERTY_VALUE][$index] = $option->get_value();
                    $defaults[AssessmentMatchTextQuestionOption::PROPERTY_SCORE][$index] = $option->get_score() ? $option->get_score() : 1;
                    $defaults[AssessmentMatchTextQuestionOption::PROPERTY_FEEDBACK][$index] = $option->get_feedback();
                }
                
                $defaults[AssessmentMatchTextQuestion::PROPERTY_USE_WILDCARDS] = $object->get_use_wildcards();
                $defaults[AssessmentMatchTextQuestion::PROPERTY_IGNORE_CASE] = $object->get_ignore_case();
            }
            else
            {
                $defaults[AssessmentMatchTextQuestion::PROPERTY_USE_WILDCARDS] = true;
                $defaults[AssessmentMatchTextQuestion::PROPERTY_IGNORE_CASE] = true;
                
                $number_of_options = (int) Session::retrieve('match_number_of_options');
                
                for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
                {
                    $defaults[AssessmentMatchTextQuestionOption::PROPERTY_SCORE][$option_number] = 1;
                }
            }
        }
        
        parent::setDefaults($defaults);
    }

    public function create_content_object()
    {
        $this->set_content_object(new AssessmentMatchTextQuestion());
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
        
        $object->set_hint($values[AssessmentMatchTextQuestion::PROPERTY_HINT]);
        $object->set_use_wildcards($values[AssessmentMatchTextQuestion::PROPERTY_USE_WILDCARDS]);
        $object->set_ignore_case($values[AssessmentMatchTextQuestion::PROPERTY_IGNORE_CASE]);
        
        $this->add_options_to_object();
    }

    private function add_options_to_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        
        $options = array();
        
        foreach ($values[AssessmentMatchTextQuestionOption::PROPERTY_VALUE] as $option_id => $value)
        {
            $score = $values[AssessmentMatchTextQuestionOption::PROPERTY_SCORE][$option_id];
            $feedback = $values[AssessmentMatchTextQuestionOption::PROPERTY_FEEDBACK][$option_id];
            
            $options[] = new AssessmentMatchTextQuestionOption($value, $score, $feedback);
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
                    AssessmentMatchTextQuestionOption::PROPERTY_VALUE . '[' . $option_number . ']', 
                    Translation::get('Answer'), 
                    array('class' => 'form-control', 'style' => 'height: 80px;'));
                
                $renderer->setElementTemplate(
                    '<div class="option-answer-field" data-element="' . AssessmentMatchTextQuestionOption::PROPERTY_VALUE .
                         '[' . $option_number . ']' . '">{element}</div>', 
                        AssessmentMatchTextQuestionOption::PROPERTY_VALUE . '[' . $option_number . ']');

                // Score
                $this->addElement(
                    'text',
                    AssessmentMatchTextQuestionOption::PROPERTY_SCORE . '[' . $option_number . ']',
                    Translation::get('Score'),
                    'size="2"  class="input_numeric form-control"');

                $renderer->setElementTemplate(
                    '<div class="option-score-field assessment_match_question_score_container form-inline" data-element="' .
                    AssessmentMatchTextQuestionOption::PROPERTY_SCORE . '[' . $option_number . ']' .
                    '"><label>{label}:</label> {element}</div>',
                    AssessmentMatchTextQuestionOption::PROPERTY_SCORE . '[' . $option_number . ']');

                // Feedback
                $this->add_html_editor(
                    AssessmentMatchTextQuestionOption::PROPERTY_FEEDBACK . '[' . $option_number . ']', 
                    Translation::get('Feedback'), 
                    false, 
                    $htmlEditorOptions);

                $renderer->setElementTemplate(
                    '<div class="option-feedback-field form-assessment-extra-container" data-element="' .
                         AssessmentMatchTextQuestionOption::PROPERTY_FEEDBACK . '[' . $option_number . ']' .
                         '"><label>{label}</label>{element}</div>', 
                        AssessmentMatchTextQuestionOption::PROPERTY_FEEDBACK . '[' . $option_number . ']');
                
                $this->addElement('html', '</td>');
                
                $this->addElement('html', '<td class="table-cell-action cell-stat-x3 text-right">');
                
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
                     '" class="option-action option-remove fa fa-trash ' . $removeClass . '"></span>';
                
                $this->addElement('html', implode('<br />' . PHP_EOL, $actionButtons));
                
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
                Translation::get('AddMatchTextOption'), 
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
            AssessmentMatchTextQuestion::PROPERTY_HINT, 
            Translation::get('Hint', array(), ClassnameUtilities::getInstance()->getNamespaceFromObject($this)), 
            false, 
            $htmlEditorOptions);
    }
}
