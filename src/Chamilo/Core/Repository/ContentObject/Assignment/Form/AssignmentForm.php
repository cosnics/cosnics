<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Form;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.content_object.assignment.php This class represents a form to create or update assignments
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class AssignmentForm extends ContentObjectForm
{

    /**
     * Override the default attachments form to not show it on the default location
     */
    protected function add_attachments_form()
    {

    }

    protected function build_creation_form($htmleditor_options = [], $in_tab = false)
    {
        parent::build_creation_form();
        $this->build_form();
    }

    protected function build_editing_form($htmleditor_options = [], $in_tab = false)
    {
        parent::build_editing_form();
        $this->build_form();
    }

    private function build_form()
    {
        $this->addElement('category', Translation::get('Properties', null, StringUtilities::LIBRARIES));

        // Start and end time
        $this->add_timewindow(
            Assignment::PROPERTY_START_TIME, Assignment::PROPERTY_END_TIME, Translation::get('StartTime'),
            Translation::get('EndTime')
        );

        // Visibilty submissions
        $choices = [];
        $choices[] = $this->createElement(
            'radio', Assignment::PROPERTY_VISIBILITY_SUBMISSIONS, null, Translation::get('VisibleOthers'), 1
        );
        $choices[] = $this->createElement(
            'radio', Assignment::PROPERTY_VISIBILITY_SUBMISSIONS, null, Translation::get('InvisibleOthers'), 0
        );
        $this->addGroup($choices, null, Translation::get('VisibilitySubmissions'), '', false);

        // Allow late submissions
        $choices = [];
        $choices[] = $this->createElement(
            'radio', Assignment::PROPERTY_ALLOW_LATE_SUBMISSIONS, null, Translation::get('AllowLateYes'), 1
        );
        $choices[] = $this->createElement(
            'radio', Assignment::PROPERTY_ALLOW_LATE_SUBMISSIONS, null, Translation::get('AllowLateNo'), 0
        );
        $this->addGroup($choices, null, Translation::get('AllowLateSubmissions'), '', false);

        $types = new AdvancedElementFinderElementTypes();
        $types->add_element_type(
            new AdvancedElementFinderElementType(
                'assignment', Translation::get('AllowedContentTypes'),
                'Chamilo\Core\Repository\ContentObject\Assignment\Ajax', 'AllowedTypesFeed'
            )
        );

        $this->addElement(
            'advanced_element_finder', Assignment::PROPERTY_ALLOWED_TYPES, Translation::get('AllowedContentTypes'),
            $types
        );

        parent::add_attachments_form();

        // Automatic feedback
        $this->addElement('category', Translation::get('AutomaticFeedback'));

        // attachment uploader and selector
        $calculator = new Calculator(
            \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                User::class, (int) $this->get_owner_id()
            )
        );

        $uploadUrl = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Ajax\Manager::CONTEXT,
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
            'html', ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath(Manager::CONTEXT, true) . 'Plugin/jquery.file.upload.import.js'
        )
        );

        $this->addElement(
            'html', ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath(Assignment::CONTEXT, true) . 'UploadifyFeedback.js'
        )
        );

        $types = new AdvancedElementFinderElementTypes();
        $types->add_element_type(
            new AdvancedElementFinderElementType(
                'content_objects', Translation::get('ContentObjects'), 'Chamilo\Core\Repository\Ajax',
                'AttachmentContentObjectsFeed'
            )
        );

        $this->addElement(
            'advanced_element_finder', Assignment::PROPERTY_SELECT_ATTACHMENT,
            Translation::get('SelectFeedbackAttachment'), $types
        );

        $this->add_html_editor(Assignment::PROPERTY_AUTOMATIC_FEEDBACK_TEXT, Translation::get('Text'), false);

        $choices = [];
        $choices[] = $this->createElement(
            'radio', Assignment::PROPERTY_VISIBILTY_FEEDBACK, null, Translation::get('AfterEndDate'), 0
        );
        $choices[] = $this->createElement(
            'radio', Assignment::PROPERTY_VISIBILTY_FEEDBACK, null, Translation::get('AfterSubmission'), 1
        );
        $this->addGroup($choices, null, Translation::get('VisibiltyFeedback'), '', false);
    }

    public function create_content_object()
    {
        $object = new Assignment();
        $values = $this->exportValues();
        $object->set_start_time(DatetimeUtilities::getInstance()->timeFromDatepicker($values[Assignment::PROPERTY_START_TIME]));
        $object->set_end_time(DatetimeUtilities::getInstance()->timeFromDatepicker($values[Assignment::PROPERTY_END_TIME]));
        $object->set_visibility_submissions($values[Assignment::PROPERTY_VISIBILITY_SUBMISSIONS]);
        $object->set_allow_late_submissions($values[Assignment::PROPERTY_ALLOW_LATE_SUBMISSIONS]);

        $cos = null;

        foreach ($values[Assignment::PROPERTY_SELECT_ATTACHMENT]['content_object'] as $co)
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
        $this->setAllowedTypes($object, $values[Assignment::PROPERTY_ALLOWED_TYPES]['']);
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

    // Inherited

    /**
     * Sets the allowed types based on the given identifiers
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param $allowedTypeIdentifiers
     */
    protected function setAllowedTypes(Assignment $assignment, $allowedTypeIdentifiers)
    {
        $configuration = Configuration::getInstance();

        $integrationPackages = $configuration->getIntegrationRegistrations(
            'Chamilo\Core\Repository\ContentObject\Assignment'
        );

        $allowedTypes = [];

        foreach ($integrationPackages as $basePackage => $integrationPackageData)
        {
            $typeName = ClassnameUtilities::getInstance()->getPackageNameFromNamespace($basePackage);
            $typeClass = $basePackage . '\Storage\DataClass\\' . $typeName;

            if (in_array($integrationPackageData[Registration::PROPERTY_ID], $allowedTypeIdentifiers))
            {
                $allowedTypes[] = $typeClass;
            }
        }

        $assignment->set_allowed_types(implode(',', $allowedTypes));
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     *
     * @throws \HTML_QuickForm_Error
     */
    protected function setDefaultAllowedContentObjects(Assignment $assignment)
    {
        $allowedTypes = $assignment->get_allowed_types();

        $allowedTypeClasses =
            empty($allowedTypes) ? ['Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File'] :
                explode(',', $allowedTypes);

        $configuration = Configuration::getInstance();

        $integrationPackages = $configuration->getIntegrationRegistrations(
            'Chamilo\Core\Repository\ContentObject\Assignment'
        );

        $defaultElements = new AdvancedElementFinderElements();

        foreach ($integrationPackages as $basePackage => $integrationPackageData)
        {
            $typeName = ClassnameUtilities::getInstance()->getPackageNameFromNamespace($basePackage);
            $typeClass = $basePackage . '\Storage\DataClass\\' . $typeName;

            if (in_array($typeClass, $allowedTypeClasses))
            {
                $allowedTypeTranslation = Translation::getInstance()->getTranslation('TypeName', [], $basePackage);

                $glyph = new NamespaceIdentGlyph(
                    $basePackage, true, false, false, IdentGlyph::SIZE_MINI, array('fa-fw')
                );

                $defaultElements->add_element(
                    new AdvancedElementFinderElement(
                        $integrationPackageData['id'], $glyph->getClassNamesString(), $allowedTypeTranslation,
                        $allowedTypeTranslation
                    )
                );
            }
        }

        $element = $this->getElement(Assignment::PROPERTY_ALLOWED_TYPES);
        $element->setDefaultValues($defaultElements);
    }

    public function setDefaults($defaults = [], $filter = null)
    {
        /** @var Assignment $object */
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

            $active = $this->getElement(Assignment::PROPERTY_SELECT_ATTACHMENT);

            if ($active)
            {
                $contentObjectIdentifiers = $object->get_automatic_feedback_co_ids();
                $condition = new InCondition(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                    $contentObjectIdentifiers
                );

                $attachments = DataManager::retrieve_active_content_objects(
                    ContentObject::class, new DataClassRetrievesParameters($condition)
                );

                $defaultAttachments = new AdvancedElementFinderElements();

                foreach ($attachments as $attachment)
                {
                    $defaultAttachments->add_element(
                        new AdvancedElementFinderElement(
                            'content_object_' . $attachment->getId(),
                            $attachment->getGlyph(IdentGlyph::SIZE_MINI, true, array('fa-fw'))->getClassNamesString(),
                            $attachment->get_title(), $attachment->get_type_string()
                        )
                    );
                }

                $element = $this->getElement(Assignment::PROPERTY_SELECT_ATTACHMENT);
                $element->setDefaultValues($defaultAttachments);
            }
        }
        else
        {
            $defaults[Assignment::PROPERTY_VISIBILITY_SUBMISSIONS] = 0;
            $defaults[Assignment::PROPERTY_ALLOW_LATE_SUBMISSIONS] = 1;
            $defaults[Assignment::PROPERTY_VISIBILTY_FEEDBACK] = Assignment::VISIBILITY_FEEDBACK_AFTER_SUBMISSION;
            $defaults[Assignment::PROPERTY_ALLOWED_TYPES] = array(File::class);
            $defaults[Assignment::PROPERTY_START_TIME] = time();
            $defaults[Assignment::PROPERTY_END_TIME] = strtotime('+1 day', time());
        }

        $this->setDefaultAllowedContentObjects($object);

        parent::setDefaults($defaults);
    }

    public function update_content_object()
    {
        /** @var Assignment $object */

        $object = $this->get_content_object();
        $values = $this->exportValues();
        $object->set_start_time(DatetimeUtilities::getInstance()->timeFromDatepicker($values[Assignment::PROPERTY_START_TIME]));
        $object->set_end_time(DatetimeUtilities::getInstance()->timeFromDatepicker($values[Assignment::PROPERTY_END_TIME]));
        $object->set_visibility_submissions($values[Assignment::PROPERTY_VISIBILITY_SUBMISSIONS]);
        $object->set_allow_late_submissions($values[Assignment::PROPERTY_ALLOW_LATE_SUBMISSIONS]);

        $cos = null;

        foreach ($values[Assignment::PROPERTY_SELECT_ATTACHMENT]['content_object'] as $co)
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
        $this->setAllowedTypes($object, $values[Assignment::PROPERTY_ALLOWED_TYPES]['']);
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
}
