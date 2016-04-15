<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Form;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
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
    const PARAM_ANONYMOUS_FEEDBACK = 'anonymous_feedback';
    const PARAM_ENABLE_USER_RESULT_EXPORT = 'enable_user_results_export';

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

        parent :: __construct(self :: FORM_NAME, 'post', $this->viewer->get_url());

        $this->add_elements();
    }

    function setDefaults($defaultValues = null, $filter = null)
    {
        $defaultValues[self :: PARAM_SUBSCRIPTION_DEADLINE] = date(
            'Y-m-d',
            $defaultValues[self :: PARAM_SUBSCRIPTION_DEADLINE]);
        parent :: setDefaults($defaultValues, $filter);
    }

    private function add_elements()
    {
        // if($this->viewer->get_course_setting('allow_self_subscribe'))
        // {
        // //TODO add subscription group
        // $this->addElement(
        // 'checkbox',
        // self :: PARAM_DIRECT_SUBSCRIBE_AVAILABLE,
        // Translation :: get('DirectSubscribeAvailable')
        // );
        // $this->addElement(
        // 'checkbox',
        // self :: PARAM_UNSUBSCRIBE_AVAILABLE,
        // Translation :: get('UnsubscribeAvailable')
        // );
        // $this->add_datepicker(
        // self :: PARAM_SUBSCRIPTION_DEADLINE,
        // Translation :: get('SubscriptionDeadline'),
        // false
        // );
        // $this->add_textfield(
        // self :: PARAM_MIN_GROUP_MEMBERS,
        // Translation :: get('MinimumAmountOfGroupMembers'),false
        // );
        // $this->add_textfield(
        // self :: PARAM_MAX_GROUP_MEMBERS,
        // Translation :: get('MaximumAmountOfGroupMembers'),false
        // );
        // }
        $this->addElement(
            'checkbox',
            self :: PARAM_ANONYMOUS_FEEDBACK,
            Translation :: get(
                (string) StringUtilities :: getInstance()->createString(self :: PARAM_ANONYMOUS_FEEDBACK)->upperCamelize()));

        $this->addElement(
            'checkbox',
            self :: PARAM_ENABLE_USER_RESULT_EXPORT,
            Translation :: get(
                (string) StringUtilities :: getInstance()->createString(self :: PARAM_ENABLE_USER_RESULT_EXPORT)->upperCamelize()));

        // //TODO add filter group
        // $this->addElement(
        // 'checkbox',
        // self :: PARAM_FILTER_MIN_MAX,
        // Translation :: get('FilterMinMaxScores')
        // );
        $this->addElement('checkbox', self :: PARAM_FILTER_SELF, Translation :: get('FilterSelfAssessment'));

        $this->addElement(
            'style_submit_button',
            FormValidator :: PARAM_SUBMIT,
            Translation :: get('Submit', null, Utilities :: COMMON_LIBRARIES));

        $this->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\PeerAssessment', true) .
                     'Settings.js'));
    }
}
