<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component\Settings;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component\ViewerComponent;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class PeerAssessmentSettingsForm extends FormValidator
{
    const FORM_NAME = 'peer_assessment_settings_form';
    const PARAM_DIRECT_SUBSCRIBE_AVAILABLE = 'direct_subscribe_available';
    const PARAM_UNSUBSCRIBE_AVAILABLE = 'unsubscribe_available';
    const PARAM_SUBSCRIPTION_DEADLINE = 'subscription_deadline';
    const PARAM_MIN_GROUP_MEMBERS = 'min_group_members';
    const PARAM_MAX_GROUP_MEMBERS = 'max_group_members';
    const PARAM_FILTER_MIN_MAX = 'filter_min_max';
    const PARAM_FILTER_SELF = 'filter_self';

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
        
        $this->add_elements();
    }

    private function add_elements()
    {
        // TODO add subscription group
        $this->addElement(
            'checkbox', 
            self::PARAM_DIRECT_SUBSCRIBE_AVAILABLE, 
            Translation::get('DirectSubscribeAvailable'));
        $this->addElement('checkbox', self::PARAM_UNSUBSCRIBE_AVAILABLE, Translation::get('UnsubscribeAvailable'));
        $this->add_datepicker(self::PARAM_SUBSCRIPTION_DEADLINE, Translation::get('SubscriptionDeadline'), false);
        $this->add_textfield(self::PARAM_MIN_GROUP_MEMBERS, Translation::get('MinimumAmountOfGroupMembers'));
        $this->add_textfield(self::PARAM_MAX_GROUP_MEMBERS, Translation::get('MaximumAmountOfGroupMembers'));
        
        // TODO add filter group
        $this->addElement('checkbox', self::PARAM_FILTER_MIN_MAX, Translation::get('FilterMinMaxScores'));
        $this->addElement('checkbox', self::PARAM_FILTER_SELF, Translation::get('FilterSelfAssessment'));
        
        $this->addElement(
            'style_submit_button', 
            FormValidator::PARAM_SUBMIT, 
            Translation::get('Submit', null, Utilities::COMMON_LIBRARIES));
    }
}