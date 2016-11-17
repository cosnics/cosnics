<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component\Attempt;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component\ViewerComponent;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class PeerAssessmentAttemptForm extends FormValidator
{
    const FORM_NAME = 'peer_assessment_attempt_form';
    const PARAM_TITLE = 'title';
    const PARAM_DESCRIPTION = 'description';
    const PARAM_START_DATE = 'start_date';
    const PARAM_END_DATE = 'end_date';
    const PARAM_CLOSED = 'closed';
    const PARAM_WEIGHT = 'weight';

    /**
     *
     * @var PeerAssessmentDisplayViewerComponent
     */
    private $viewer;

    /**
     * Constructor
     * 
     * @param PeerAssessmentDisplayViewerComponent $viewer
     */
    function __construct(ViewerComponent $viewer)
    {
        $this->viewer = $viewer;
        
        parent::__construct(self::FORM_NAME, 'post', $this->viewer->get_url());
        
        $this->add_general();
        $this->add_buttons();
    }

    private function add_general()
    {
        $this->add_textfield(self::PARAM_TITLE, Translation::get('Title', null, Utilities::COMMON_LIBRARIES));
        $this->add_html_editor(
            self::PARAM_DESCRIPTION, 
            Translation::get('Description', null, Utilities::COMMON_LIBRARIES));
        $this->add_timewindow(
            self::PARAM_START_DATE, 
            self::PARAM_END_DATE, 
            Translation::get('StartDate'), 
            Translation::get('EndDate'), 
            false);
        $this->add_textfield(self::PARAM_WEIGHT, Translation::get('Weight'));
        
        $this->addElement('hidden', 'id');
        $this->addElement('hidden', 'publication_id');
    }

    private function add_buttons()
    {
        $this->addElement(
            'style_submit_button', 
            FormValidator::PARAM_SUBMIT, 
            Translation::get('Submit', null, Utilities::COMMON_LIBRARIES));
    }
}