<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Form;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Storage\DataClass\PeerAssessment;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
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
    const FORM_TYPE_EDIT = 1;
    const FORM_TYPE_CREATE = 2;

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
    public function __construct($viewer)
    {
        $this->viewer = $viewer;
        
        parent::__construct(self::FORM_NAME, 'post', $this->viewer->get_url());
        
        $this->add_general();
        $this->add_buttons();
    }

    private function add_general()
    {
        $this->add_textfield(self::PARAM_TITLE, Translation::get('Title', null, Utilities::COMMON_LIBRARIES));
        
        $value = Configuration::getInstance()->get_setting(
            array(\Chamilo\Core\Repository\Manager::context(), 'description_required'));
        
        $required = ($value == 1) ? true : false;
        $name = Translation::get('Description', array(), ClassnameUtilities::getInstance()->getNamespaceFromObject($this));
        $this->add_html_editor(self::PARAM_DESCRIPTION, $name, $required);
        
        $this->add_timewindow(
            self::PARAM_START_DATE, 
            self::PARAM_END_DATE, 
            Translation::get('StartDate'), 
            Translation::get('EndDate'), 
            false);
        
        // only display weight when scores are given
        $root_content_object = $this->viewer->get_root_content_object();
        $assessment_type = $root_content_object->get_assessment_type();
        if ($assessment_type == PeerAssessment::TYPE_BOTH || $assessment_type == PeerAssessment::TYPE_SCORES)
        {
            $this->add_textfield(self::PARAM_WEIGHT, Translation::get('Weight'));
        }
        else
        {
            $this->addElement('hidden', self::PARAM_WEIGHT, 0);
        }
        
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
