<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Form;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

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
            $defaults[Assignment::PROPERTY_VISIBILITY_SUBMISSIONS] = $object->get_visibility_submissions();
            $defaults[Assignment::PROPERTY_ALLOW_LATE_SUBMISSIONS] = $object->get_allow_late_submissions();
            $defaults[Assignment::PROPERTY_START_TIME] = $object->get_start_time();
            $defaults[Assignment::PROPERTY_END_TIME] = $object->get_end_time();

            if (!is_null($object->get_visibility_feedback()))
            {
                $defaults[Assignment::PROPERTY_VISIBILTY_FEEDBACK] = $object->get_visibility_feedback();
            }
            else
            {
                $defaults[Assignment::PROPERTY_VISIBILTY_FEEDBACK] = Assignment::VISIBILITY_FEEDBACK_AFTER_SUBMISSION;
            }

            $defaults[Assignment::PROPERTY_AUTOMATIC_FEEDBACK_TEXT] = $object->get_automatic_feedback_text();
            $defaults[Assignment::PROPERTY_SELECT_ATTACHMENT] = array();

            $co_ids = explode(',', $object->get_automatic_feedback_co_ids());

            if ($co_ids)
            {
                $condition = new InCondition(
                    new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID),
                    $co_ids,
                    ContentObject::get_table_name()
                );
                $attached_objects = \Chamilo\Core\Repository\Storage\DataManager::retrieve_active_content_objects(
                    ContentObject::class_name(),
                    new DataClassRetrievesParameters($condition)
                )->as_array();
                $defaults[Assignment::PROPERTY_SELECT_ATTACHMENT] = Utilities::content_objects_for_element_finder(
                    $attached_objects
                );
            }

            $active = $this->getElement(Assignment::PROPERTY_SELECT_ATTACHMENT);
            if ($active)
            {
                if ($active->_elements[0])
                {
                    $active->_elements[0]->setValue(serialize($defaults[Assignment::PROPERTY_SELECT_ATTACHMENT]));
                }
            }

            $defaults[Assignment::PROPERTY_ALLOWED_TYPES] = explode(',', $object->get_allowed_types());
        }
        else
        {
            $defaults[Assignment::PROPERTY_VISIBILITY_SUBMISSIONS] = 0;
            $defaults[Assignment::PROPERTY_ALLOW_LATE_SUBMISSIONS] = 1;
            $defaults[Assignment::PROPERTY_VISIBILTY_FEEDBACK] = Assignment::VISIBILITY_FEEDBACK_AFTER_SUBMISSION;
            $defaults[Assignment::PROPERTY_ALLOWED_TYPES] = array(File::class_name());
            $defaults[Assignment::PROPERTY_START_TIME] = time();
            $defaults[Assignment::PROPERTY_END_TIME] = strtotime('+1 day', time());
        }

        parent::setDefaults($defaults);
    }

    protected function build_creation_form()
    {
        parent::build_creation_form();
        $this->build_form();
    }

    protected function build_editing_form()
    {
        parent::build_editing_form();
        $this->build_form();
    }

    /**
     * Override the default attachments form to not show it on the default location
     */
    protected function add_attachments_form()
    {

    }

    private function build_form()
    {
        $this->addElement('category', Translation::get('Properties', null, Utilities::COMMON_LIBRARIES));

        // Start and end time
        $this->add_timewindow(
            Assignment::PROPERTY_START_TIME,
            Assignment::PROPERTY_END_TIME,
            Translation::get('StartTime'),
            Translation::get('EndTime')
        );

        // Visibilty submissions
        $choices = array();
        $choices[] = $this->createElement(
            'radio',
            Assignment::PROPERTY_VISIBILITY_SUBMISSIONS,
            null,
            Translation::get('VisibleOthers'),
            1
        );
        $choices[] = $this->createElement(
            'radio',
            Assignment::PROPERTY_VISIBILITY_SUBMISSIONS,
            null,
            Translation::get('InvisibleOthers'),
            0
        );
        $this->addGroup($choices, null, Translation::get('VisibilitySubmissions'), '', false);

        // Allow late submissions
        $choices = array();
        $choices[] = $this->createElement(
            'radio',
            Assignment::PROPERTY_ALLOW_LATE_SUBMISSIONS,
            null,
            Translation::get('AllowLateYes'),
            1
        );
        $choices[] = $this->createElement(
            'radio',
            Assignment::PROPERTY_ALLOW_LATE_SUBMISSIONS,
            null,
            Translation::get('AllowLateNo'),
            0
        );
        $this->addGroup($choices, null, Translation::get('AllowLateSubmissions'), '', false);

        // Allowed content types for submissions
        $types = $this->get_allowed_content_object_types();

//        $advanced_select = $this->createElement(
//            'select',
//            Assignment::PROPERTY_ALLOWED_TYPES,
//            Translation::get('AllowedContentTypes'),
//            $types,
//            array(
//                'multiple' => 'true',
//                'class' => 'advanced_select_question',
//                'size' => (count($types) > 10 ? 10 : count($types))
//            )
//        );

        $types = new AdvancedElementFinderElementTypes();
        $types->add_element_type(
            new AdvancedElementFinderElementType(
                'assignment', Translation::get('AllowedContentTypes'),
                'Chamilo\\Core\\Repository\\ContentObject\\Assignment\\Ajax', 'AllowedTypesXmlFeed'
            )
        );

        $this->addElement(
            'advanced_element_finder', Assignment::PROPERTY_ALLOWED_TYPES, Translation::get('AllowedContentTypes'),
            $types
        );

//        $this->addElement($advanced_select);

        $this->addRule(
            Assignment::PROPERTY_ALLOWED_TYPES,
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
            'required'
        );

        $this->addElement('category');

        parent::add_attachments_form();

        // Automatic feedback
        $this->addElement('category', Translation::get('AutomaticFeedback'));

        // attachment uploader and selector
        $url = Path::getInstance()->getBasePath(true) .
            'index.php?application=Chamilo%5CCore%5CRepository%5CAjax&go=XmlFeed';
        $locale = array();
        $locale['Display'] = Translation::get('AddAttachments');
        $locale['Searching'] = Translation::get('Searching', null, Utilities::COMMON_LIBRARIES);
        $locale['NoResults'] = Translation::get('NoResults', null, Utilities::COMMON_LIBRARIES);
        $locale['Error'] = Translation::get('Error', null, Utilities::COMMON_LIBRARIES);

        $calculator = new Calculator(
            \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User::class_name(),
                (int) $this->get_owner_id()
            )
        );

        $uploadUrl = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Ajax\Manager::context(),
                \Chamilo\Core\Repository\Ajax\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Ajax\Manager::ACTION_IMPORT_FILE
            )
        );

        $dropZoneParameters = array(
            'name' => 'select_attachment_importer',
            'maxFilesize' => $calculator->getMaximumUploadSize(),
            'uploadUrl' => $uploadUrl->getUrl(),
            'successCallbackFunction' => 'chamilo.core.repository.importFeedbackAttachment.processUploadedFile',
            'sendingCallbackFunction' => 'chamilo.core.repository.importFeedbackAttachment.prepareRequest',
            'removedfileCallbackFunction' => 'chamilo.core.repository.importFeedbackAttachment.deleteUploadedFile'
        );

        $this->addFileDropzone('select_attachment_importer', $dropZoneParameters, true);

        $this->addElement(
            'html',
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath(\Chamilo\Core\Repository\Manager::context(), true) .
                'Plugin/jquery.file.upload.import.js'
            )
        );

        $this->addElement(
            'html',
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath(Assignment::package(), true) . 'UploadifyFeedback.js'
            )
        );

        $this->addElement(
            'element_finder',
            Assignment::PROPERTY_SELECT_ATTACHMENT,
            Translation::get('SelectFeedbackAttachment'),
            $url,
            $locale,
            array()
        );

        $this->add_html_editor(Assignment::PROPERTY_AUTOMATIC_FEEDBACK_TEXT, Translation::get('Text'), false);

        $choices = array();
        $choices[] = $this->createElement(
            'radio',
            Assignment::PROPERTY_VISIBILTY_FEEDBACK,
            null,
            Translation::get('AfterEndDate'),
            0
        );
        $choices[] = $this->createElement(
            'radio',
            Assignment::PROPERTY_VISIBILTY_FEEDBACK,
            null,
            Translation::get('AfterSubmission'),
            1
        );
        $this->addGroup($choices, null, Translation::get('VisibiltyFeedback'), '', false);

        $this->addElement('category');
    }

    // Inherited
    public function create_content_object()
    {
        $object = new Assignment();
        $values = $this->exportValues();
        $object->set_start_time(DatetimeUtilities::time_from_datepicker($values[Assignment::PROPERTY_START_TIME]));
        $object->set_end_time(DatetimeUtilities::time_from_datepicker($values[Assignment::PROPERTY_END_TIME]));
        $object->set_visibility_submissions($values[Assignment::PROPERTY_VISIBILITY_SUBMISSIONS]);
        $object->set_allow_late_submissions($values[Assignment::PROPERTY_ALLOW_LATE_SUBMISSIONS]);

        $cos = null;

        foreach ($values[Assignment::PROPERTY_SELECT_ATTACHMENT]['lo'] as $co)
        {
            if ($cos == null)
            {
                $cos = $co;
            }
            else
            {
                $cos .= ',' . $co;
            }
        }
        $object->set_automatic_feedback_co_ids($cos);
        $object->set_automatic_feedback_text($values[Assignment::PROPERTY_AUTOMATIC_FEEDBACK_TEXT]);
        $object->set_allowed_types(implode(',', $values[Assignment::PROPERTY_ALLOWED_TYPES]));
        // Check if automatic feedback given
        if ($cos != null || $values[Assignment::PROPERTY_AUTOMATIC_FEEDBACK_TEXT] != '')
        {
            $object->set_visibility_feedback($values[Assignment::PROPERTY_VISIBILTY_FEEDBACK]);
        }
        else
        {
            $object->set_visibility_feedback(null);
        }

        $this->set_content_object($object);

        return parent::create_content_object();
    }

    public function update_content_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        $object->set_start_time(DatetimeUtilities::time_from_datepicker($values[Assignment::PROPERTY_START_TIME]));
        $object->set_end_time(DatetimeUtilities::time_from_datepicker($values[Assignment::PROPERTY_END_TIME]));
        $object->set_visibility_submissions($values[Assignment::PROPERTY_VISIBILITY_SUBMISSIONS]);
        $object->set_allow_late_submissions($values[Assignment::PROPERTY_ALLOW_LATE_SUBMISSIONS]);

        $cos = null;

        foreach ($values[Assignment::PROPERTY_SELECT_ATTACHMENT]['lo'] as $co)
        {
            if ($cos == null)
            {
                $cos = $co;
            }
            else
            {
                $cos .= ',' . $co;
            }
        }
        $object->set_automatic_feedback_co_ids($cos);
        $object->set_automatic_feedback_text($values[Assignment::PROPERTY_AUTOMATIC_FEEDBACK_TEXT]);
        $object->set_allowed_types(implode(',', $values[Assignment::PROPERTY_ALLOWED_TYPES]));
        // Check if automatic feedback given
        if ($cos != null || $values[Assignment::PROPERTY_AUTOMATIC_FEEDBACK_TEXT] != '')
        {
            $object->set_visibility_feedback($values[Assignment::PROPERTY_VISIBILTY_FEEDBACK]);
        }
        else
        {
            $object->set_visibility_feedback(null);
        }

        $this->set_content_object($object);

        return parent::update_content_object();
    }

    public function get_allowed_content_object_types()
    {
        $configuration = Configuration::getInstance();

        $types = array();

        $integrationPackages = $configuration->getIntegrationRegistrations(
            'Chamilo\Core\Repository\ContentObject\Assignment'
        );
        foreach ($integrationPackages as $basePackage => $integrationPackageData)
        {
            if ($integrationPackageData['status'] != Registration::STATUS_ACTIVE)
            {
                continue;
            }

            $types[] = $basePackage;
        }

        $return_types = array();
        foreach ($types as $index => $type)
        {
            $typeName = ClassnameUtilities::getInstance()->getPackageNameFromNamespace($type);
            $typeClass = $type . '\Storage\DataClass\\' . $typeName;

            if (!\Chamilo\Configuration\Configuration::getInstance()->isRegisteredAndActive($type))
            {
                unset($types[$index]);
                continue;
            }

            $return_types[$typeClass] = Translation::get('TypeName', array(), $type);
        }

        asort($return_types);

        return $return_types;
    }
}
