<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Form;

use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 *
 * @package repository.content_object.assignment.php This class represents a form to create or update assignments
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class AssignmentForm extends ContentObjectForm
{

    public function setDefaults($defaults = array())
    {
        $object = $this->get_content_object();

        if ($object->get_id() != null)
        {
            $defaults[Assignment :: PROPERTY_VISIBILITY_SUBMISSIONS] = $object->get_visibility_submissions();
            $defaults[Assignment :: PROPERTY_ALLOW_GROUP_SUBMISSIONS] = $object->get_ALLOW_GROUP_SUBMISSIONS();
            $defaults[Assignment :: PROPERTY_ALLOW_LATE_SUBMISSIONS] = $object->get_allow_late_submissions();
            $defaults[Assignment :: PROPERTY_START_TIME] = $object->get_start_time();
            $defaults[Assignment :: PROPERTY_END_TIME] = $object->get_end_time();

            if (! is_null($object->get_visibility_feedback()))
                $defaults[Assignment :: PROPERTY_VISIBILTY_FEEDBACK] = $object->get_visibility_feedback();
            else
                $defaults[Assignment :: PROPERTY_VISIBILTY_FEEDBACK] = Assignment :: VISIBILITY_FEEDBACK_AFTER_SUBMISSION;

            $defaults[Assignment :: PROPERTY_AUTOMATIC_FEEDBACK_TEXT] = $object->get_automatic_feedback_text();
            $defaults[Assignment :: PROPERTY_SELECT_ATTACHMENT] = array();

            $co_ids = explode(',', $object->get_automatic_feedback_co_ids());

            if ($co_ids)
            {
                $condition = new InCondition(
                    new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID),
                    $co_ids,
                    ContentObject :: get_table_name());
                $attached_objects = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_active_content_objects(
                    ContentObject :: class_name(),
                    new DataClassRetrievesParameters($condition))->as_array();
                $defaults[Assignment :: PROPERTY_SELECT_ATTACHMENT] = Utilities :: content_objects_for_element_finder(
                    $attached_objects);
            }

            $active = $this->getElement(Assignment :: PROPERTY_SELECT_ATTACHMENT);
            if ($active)
            {
                if ($active->_elements[0])
                {
                    $active->_elements[0]->setValue(serialize($defaults[Assignment :: PROPERTY_SELECT_ATTACHMENT]));
                }
            }

            $defaults[Assignment :: PROPERTY_ALLOWED_TYPES] = explode(',', $object->get_allowed_types());
        }
        else
        {
            $defaults[Assignment :: PROPERTY_VISIBILITY_SUBMISSIONS] = 0;
            $defaults[Assignment :: PROPERTY_ALLOW_GROUP_SUBMISSIONS] = 0;
            $defaults[Assignment :: PROPERTY_ALLOW_LATE_SUBMISSIONS] = 1;
            $defaults[Assignment :: PROPERTY_VISIBILTY_FEEDBACK] = Assignment :: VISIBILITY_FEEDBACK_AFTER_SUBMISSION;
            $defaults[Assignment :: PROPERTY_ALLOWED_TYPES] = array(File :: class_name());
            $defaults[Assignment :: PROPERTY_START_TIME] = time();
            $defaults[Assignment :: PROPERTY_END_TIME] = strtotime('+1 day', time());
        }

        parent :: setDefaults($defaults);
    }

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->build_form();
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->build_form();
    }

    private function build_form()
    {
        $this->addElement('category', Translation :: get('Properties', null, Utilities :: COMMON_LIBRARIES));

        // Start and end time
        $this->add_timewindow(
            Assignment :: PROPERTY_START_TIME,
            Assignment :: PROPERTY_END_TIME,
            Translation :: get('StartTime'),
            Translation :: get('EndTime'));

        // Visibilty submissions
        $choices = array();
        $choices[] = $this->createElement(
            'radio',
            Assignment :: PROPERTY_VISIBILITY_SUBMISSIONS,
            null,
            Translation :: get('VisibleOthers'),
            1);
        $choices[] = $this->createElement(
            'radio',
            Assignment :: PROPERTY_VISIBILITY_SUBMISSIONS,
            null,
            Translation :: get('InvisibleOthers'),
            0);
        $this->addGroup($choices, null, Translation :: get('VisibilitySubmissions'), '', false);

        // Assignment type
        $choices = array();
        $choices[] = $this->createElement(
            'radio',
            Assignment :: PROPERTY_ALLOW_GROUP_SUBMISSIONS,
            null,
            Translation :: get('Individual'),
            0);
        $choices[] = $this->createElement(
            'radio',
            Assignment :: PROPERTY_ALLOW_GROUP_SUBMISSIONS,
            null,
            Translation :: get('Group'),
            1);
        $this->addGroup($choices, null, Translation :: get('AssignmentType'), '', false);

        // Allow late submissions
        $choices = array();
        $choices[] = $this->createElement(
            'radio',
            Assignment :: PROPERTY_ALLOW_LATE_SUBMISSIONS,
            null,
            Translation :: get('AllowLateYes'),
            1);
        $choices[] = $this->createElement(
            'radio',
            Assignment :: PROPERTY_ALLOW_LATE_SUBMISSIONS,
            null,
            Translation :: get('AllowLateNo'),
            0);
        $this->addGroup($choices, null, Translation :: get('AllowLateSubmissions'), '', false);

        // Allowed content types for submissions
        $types = $this->get_allowed_content_object_types();
        $advanced_select = $this->createElement(
            'advmultiselect',
            Assignment :: PROPERTY_ALLOWED_TYPES,
            Translation :: get('AllowedContentTypes'),
            $types,
            array('style' => 'width: 200px;', 'class' => 'advanced_select_question'));
        $advanced_select->setButtonAttributes('add', 'class="add"');
        $advanced_select->setButtonAttributes('remove', 'class="remove"');
        $this->addElement($advanced_select);

        $this->addRule(
            Assignment :: PROPERTY_ALLOWED_TYPES,
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES),
            'required');

        $this->addElement('category');

        // Automatic feedback
        $this->addElement('category', Translation :: get('AutomaticFeedback'));

        // attachment uploader and selector
        $url = Path :: getInstance()->getBasePath(true) .
             'index.php?application=Chamilo%5CCore%5CRepository%5CAjax&go=XmlFeed';
        $locale = array();
        $locale['Display'] = Translation :: get('AddAttachments');
        $locale['Searching'] = Translation :: get('Searching', null, Utilities :: COMMON_LIBRARIES);
        $locale['NoResults'] = Translation :: get('NoResults', null, Utilities :: COMMON_LIBRARIES);
        $locale['Error'] = Translation :: get('Error', null, Utilities :: COMMON_LIBRARIES);

        $this->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath('Chamilo\Libraries', true) .
                     'Plugin/Uploadify/jquery.uploadify.min.js'));

        $this->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath(Assignment :: package(), true) . 'UploadifyFeedback.js'));
        $this->addElement(
            'static',
            'uploadify',
            Translation :: get('UploadDocument'),
            '<div id="uploadifyFeedback"></div>');
        $elem = $this->addElement(
            'element_finder',
            Assignment :: PROPERTY_SELECT_ATTACHMENT,
            Translation :: get('SelectAttachment'),
            $url,
            $locale,
            array());
        $elem->setDefaultCollapsed(true);

        $this->addElement(
            'textarea',
            Assignment :: PROPERTY_AUTOMATIC_FEEDBACK_TEXT,
            Translation :: get('Text'),
            array('cols' => '60', 'rows' => '3'));

        $choices = array();
        $choices[] = $this->createElement(
            'radio',
            Assignment :: PROPERTY_VISIBILTY_FEEDBACK,
            null,
            Translation :: get('AfterEndDate'),
            0);
        $choices[] = $this->createElement(
            'radio',
            Assignment :: PROPERTY_VISIBILTY_FEEDBACK,
            null,
            Translation :: get('AfterSubmission'),
            1);
        $this->addGroup($choices, null, Translation :: get('VisibiltyFeedback'), '', false);

        $this->addElement('category');
    }

    // Inherited
    public function create_content_object()
    {
        $object = new Assignment();
        $values = $this->exportValues();
        $object->set_start_time(DatetimeUtilities :: time_from_datepicker($values[Assignment :: PROPERTY_START_TIME]));
        $object->set_end_time(DatetimeUtilities :: time_from_datepicker($values[Assignment :: PROPERTY_END_TIME]));
        $object->set_visibility_submissions($values[Assignment :: PROPERTY_VISIBILITY_SUBMISSIONS]);
        $object->set_allow_group_submissions($values[Assignment :: PROPERTY_ALLOW_GROUP_SUBMISSIONS]);
        $object->set_allow_late_submissions($values[Assignment :: PROPERTY_ALLOW_LATE_SUBMISSIONS]);

        $cos = null;

        foreach ($values[Assignment :: PROPERTY_SELECT_ATTACHMENT]['lo'] as $co)
        {
            if ($cos == null)
                $cos = $co;
            else
                $cos .= ',' . $co;
        }
        $object->set_automatic_feedback_co_ids($cos);
        $object->set_automatic_feedback_text($values[Assignment :: PROPERTY_AUTOMATIC_FEEDBACK_TEXT]);
        $object->set_allowed_types(implode(',', $values[Assignment :: PROPERTY_ALLOWED_TYPES]));
        // Check if automatic feedback given
        if ($cos != null || $values[Assignment :: PROPERTY_AUTOMATIC_FEEDBACK_TEXT] != '')
            $object->set_visibility_feedback($values[Assignment :: PROPERTY_VISIBILTY_FEEDBACK]);
        else
            $object->set_visibility_feedback(null);

        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    public function update_content_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        $object->set_start_time(DatetimeUtilities :: time_from_datepicker($values[Assignment :: PROPERTY_START_TIME]));
        $object->set_end_time(DatetimeUtilities :: time_from_datepicker($values[Assignment :: PROPERTY_END_TIME]));
        $object->set_visibility_submissions($values[Assignment :: PROPERTY_VISIBILITY_SUBMISSIONS]);
        $object->set_allow_group_submissions($values[Assignment :: PROPERTY_ALLOW_GROUP_SUBMISSIONS]);
        $object->set_allow_late_submissions($values[Assignment :: PROPERTY_ALLOW_LATE_SUBMISSIONS]);

        $cos = null;

        foreach ($values[Assignment :: PROPERTY_SELECT_ATTACHMENT]['lo'] as $co)
        {
            if ($cos == null)
                $cos = $co;
            else
                $cos .= ',' . $co;
        }
        $object->set_automatic_feedback_co_ids($cos);
        $object->set_automatic_feedback_text($values[Assignment :: PROPERTY_AUTOMATIC_FEEDBACK_TEXT]);
        $object->set_allowed_types(implode(',', $values[Assignment :: PROPERTY_ALLOWED_TYPES]));
        // Check if automatic feedback given
        if ($cos != null || $values[Assignment :: PROPERTY_AUTOMATIC_FEEDBACK_TEXT] != '')
            $object->set_visibility_feedback($values[Assignment :: PROPERTY_VISIBILTY_FEEDBACK]);
        else
            $object->set_visibility_feedback(null);

        $this->set_content_object($object);
        return parent :: update_content_object();
    }

    public function get_allowed_content_object_types()
    {
        $types = \Chamilo\Core\Repository\Storage\DataManager :: get_registered_types(true);

        $return_types = array();
        foreach ($types as $index => $type)
        {
            $packageClassName = ClassnameUtilities :: getInstance()->getNamespaceParent($type, 3);

            if (! \Chamilo\Configuration\Configuration :: get_instance()->isRegisteredAndActive($packageClassName))
            {
                unset($types[$index]);
                continue;
            }

            $return_types[$type] = Translation :: get('TypeName', array(), $packageClassName);
        }

        asort($return_types);
        return $return_types;
    }
}
