<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Form;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Storage\DataClass\PeerAssessment;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Chamilo\Libraries\Format\Tabs\DynamicFormTabsRenderer;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class PeerAssessmentViewerForm extends FormValidator
{
    const FORM_NAME = 'peer_assessment_viewer_form';
    const PARAM_SCORES = 'scores';
    const PARAM_FEEDBACK = 'feedback';
    const DISPLAY_TYPE_OVERVIEW = 'overview';
    const DISPLAY_TYPE_USER = 'user';
    const DISPLAY_TYPE_INDICATOR = 'indicator';
    const SCORES = 'scores';

    /**
     *
     * @var PeerAssessmentDisplayViewerComponent
     */
    private $viewer;

    private $users = array();

    private $validation_errors = array();

    private $indicators;

    private $processor;

    /**
     * Constructor
     * 
     * @param PeerAssessmentDisplayViewerComponent $viewer
     */
    public function __construct($viewer)
    {
        parent::__construct(self::FORM_NAME, self::FORM_METHOD_POST, $viewer->get_url());
        
        $this->viewer = $viewer;
        
        $this->get_users();
        $this->get_indicators();
        $root_content_object = $this->viewer->get_root_content_object();
        $assessment_type = $root_content_object->get_assessment_type();
        $this->processor = $root_content_object->get_result_processor();
        
        if (count($this->users) > 0)
        {
            $this->render_overview($assessment_type);
            $this->add_buttons();
        }
    }

    private function add_buttons()
    {
        $this->addElement(
            'style_submit_button', 
            FormValidator::PARAM_SUBMIT, 
            Translation::get('Submit', null, Utilities::COMMON_LIBRARIES));
    }

    private function render_overview($assessment_type)
    {
        // there should be users and indicators, otherwise don't render
        $params = array(
            \Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Manager::PARAM_ACTION => Manager::ACTION_VIEW_USER_ATTEMPT_STATUS);
        
        if (count($this->indicators) === 0 && $assessment_type != PeerAssessment::TYPE_FEEDBACK)
            $this->viewer->redirect(Translation::get('NoIndicators'), 1, $params);
        if (count($this->users) <= 1)
            $this->viewer->redirect(Translation::get('OnlyOneUser'), 1, $params);
            
            // TODO check for scores/feedback/both
            // TODO display images on tabs
        
        $tabs = new DynamicFormTabsRenderer('', $this);
        
        if ($assessment_type == PeerAssessment::TYPE_SCORES || $assessment_type == PeerAssessment::TYPE_BOTH)
        { // render the scores tab
            $tabs->add_tab(new DynamicFormTab('scores', Translation::get('Scores'), null, 'render_scores_matrix'));
        }
        if ($assessment_type == PeerAssessment::TYPE_FEEDBACK || $assessment_type == PeerAssessment::TYPE_BOTH)
        {
            // render the feedback tab
            $tabs->add_tab(
                new DynamicFormTab('feedback', Translation::get('Feedback'), null, 'render_feedback_matrix'));
        }
        $tabs->render();
    }

    /**
     * Renders the scores matrix
     * 
     * @return string The html
     * @todo create css to style the table headers (add skewed headers, ...)
     */
    public function render_scores_matrix()
    {
        $renderer = $this->defaultRenderer();
        
        $table_header = array();
        $table_header[] = '<table class="table table-striped table-bordered table-hover table-data peer-assessment-scores-matrix" style="width: auto">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th></th>'; // upper left cell is empty
                                       
        // loop over the indicators to build the header cells
        foreach ($this->indicators as $i)
        {
            $table_header[] = '<th title="' . trim(htmlentities(strip_tags($i->get_description()))) .
                 '" style="word-wrap: break-all">' . $i->get_title() . '</th>';
        }
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode(PHP_EOL, $table_header));
        
        // loop over the users to build the rows
        foreach ($this->users as $u)
        {
            // dump($u);
            $group = array();
            foreach ($this->viewer->get_indicators() as $i)
            {
                $element_name = 'scores[' . $u->get_id() . '][' . $i->get_id() . ']';
                $group[] = $this->create_textfield(
                    $element_name, 
                    null, 
                    array('style' => 'width: 50px; text-align: center'));
            }
            $this->addGroup(
                $group, 
                'scores_' . $u->get_firstname() . '_' . $u->get_lastname(), 
                $u->get_firstname() . ' ' . $u->get_lastname(), 
                '', 
                false);
            $renderer->setElementTemplate(
                '<tr><td>{label}</td>{element}</tr>', 
                'scores_' . $u->get_firstname() . '_' . $u->get_lastname());
            $renderer->setGroupElementTemplate(
                '<td style="width: 80px; padding: 0; text-align: center">{element}</td>', 
                'scores_' . $u->get_firstname() . '_' . $u->get_lastname());
            if (! $this->processor->allow_empty_scores())
                $this->addGroupRule(
                    'scores_' . $u->get_firstname() . '_' . $u->get_lastname(), 
                    'empty value', 
                    'required');
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode(PHP_EOL, $table_footer));
    }

    /**
     * Renders the feedback matrix
     * 
     * @return string The html
     */
    public function render_feedback_matrix()
    {
        // there should be users
        if (count($this->users) <= 1)
            return;
        
        $renderer = $this->defaultRenderer();
        
        $table_header = array();
        $table_header[] = '<table class="table table-striped table-bordered table-hover table-data peer-assessment-feedback-matrix" style="width: auto">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th></th>'; // upper left cell is empty
        $table_header[] = '<th>' . Translation::get('feedback') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode(PHP_EOL, $table_header));
        
        // loop over the users to build the rows
        foreach ($this->users as $u)
        {
            $group = array();
            $group[] = $this->createElement('textarea', 'feedback[' . $u->get_id() . ']');
            $this->addGroup(
                $group, 
                'feedback_' . $u->get_firstname() . '_' . $u->get_lastname(), 
                $u->get_firstname() . ' ' . $u->get_lastname(), 
                '', 
                false);
            $renderer->setElementTemplate(
                '<tr><td>{label}</td>{element}</tr>', 
                'feedback_' . $u->get_firstname() . '_' . $u->get_lastname());
            $renderer->setGroupElementTemplate(
                '<td>{element}</td>', 
                'feedback_' . $u->get_firstname() . '_' . $u->get_lastname());
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode(PHP_EOL, $table_footer));
    }

    public function validate()
    {
        $success = true;
        $vals = $this->getSubmitValues();
        
        $this->get_indicators();
        $allowed_scores = $this->processor->get_allowed_scores();
        
        $highest_allowed = array_pop($allowed_scores);
        $lowest_allowed = array_shift($allowed_scores);
        
        foreach ($vals['scores'] as $user_id => $indicator_scores)
        {
            foreach ($indicator_scores as $indicator_id => $score)
            {
                // check format - only int allowed
                $numeric = is_numeric($score);
                $float = is_float(1 + $score); // convert string to float by adding int and check if it succeeded
                
                if ((! $numeric || $float) && ! empty($score))
                {
                    $this->validation_errors[] = Translation::get('WrongValue') . ' ' .
                         $this->users[$user_id]->get_firstname() . ' ' . Translation::get('And') . ' ' .
                         $this->indicators[$indicator_id]->get_title() . "<br/>";
                    $success = false;
                }
                
                if ($highest_allowed < $score)
                {
                    $this->validation_errors[] = Translation::get('ScoreTooHigh') . ' ' .
                         $this->users[$user_id]->get_firstname() . ' ' . Translation::get('And') . ' ' .
                         $this->indicators[$indicator_id]->get_title() . "<br/>";
                    $success = false;
                }
                elseif ($score < $lowest_allowed)
                {
                    $this->validation_errors[] = Translation::get('ScoreTooLow') . ' ' .
                         $this->users[$user_id]->get_firstname() . ' ' . Translation::get('And') . ' ' .
                         $this->indicators[$indicator_id]->get_title() . "<br/>";
                    $success = false;
                }
            }
        }
        $success &= parent::validate();
        
        return $success;
    }

    public function get_users()
    {
        $group = $this->viewer->get_user_group($this->viewer->get_user_id());
        
        if ($group)
        {
            $user_array = $this->viewer->get_group_users($group->get_id());
            
            foreach ($user_array as $user)
            {
                $this->users[$user->get_id()] = $user;
            }
        }
    }

    public function get_indicators()
    {
        foreach ($this->viewer->get_indicators() as $indicator)
        {
            $this->indicators[$indicator->get_id()] = $indicator;
        }
    }

    public function get_validation_errors()
    {
        foreach ($this->validation_errors as $error)
        {
            $message .= $error;
        }
        return $message;
    }

    public function has_validation_errors()
    {
        return count($this->validation_errors) > 0 ? true : false;
    }
}
