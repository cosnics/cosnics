<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Form;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Service\RegistrationConsulter;
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
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
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

    public function setDefaults($defaultValues = array(), $filter = null)
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

            $defaults[Assignment::PROPERTY_PAGE_TEMPLATE] = $object->getPageTemplate();
            $defaults[Assignment::PROPERTY_LAST_ENTRY_AS_TEMPLATE] = $object->useLastEntryAsTemplate();
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

        $this->setDefaultAllowedContentObjects($object);

        parent::setDefaults($defaults);
    }

    protected function build_creation_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_creation_form($htmleditor_options, $in_tab);
        $this->build_form();
    }

    protected function build_editing_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_editing_form($htmleditor_options, $in_tab);
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

        $isRegistered = $this->getRegistrationConsulter()->isContextRegistered(
            'Chamilo\\Application\\Weblcms\\Tool\\Implementation\\ExamAssignment'
        );

        if ($isRegistered)
        {
            $this->addElement(
                'html', '<div class="row form-row" style="margin-top: 20px;">' .
                '<div class="col-xs-12 col-sm-8 col-md-9 col-lg-10 col-sm-push-4 col-md-push-3 col-lg-push-2">' .
                '<div class="alert alert-info">' . Translation::get('ExamAssignmentWarning') . '</div></div></div>'
            );
        }

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

        $this->addElement('html', '<div class="page_template" style="display:none;">');

        $this->addElement('category', Translation::get('PageTemplate'));

        $this->add_html_editor(Assignment::PROPERTY_PAGE_TEMPLATE, Translation::get('PageTemplate'), false);
        $this->addElement(
            'checkbox', Assignment::PROPERTY_LAST_ENTRY_AS_TEMPLATE, Translation::get('UseLastEntryAsTemplate')
        );

        $this->addElement(
            'html',
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath(Assignment::package(), true) . 'PageTemplate.js'
            )
        );

        $this->addElement('html', '</div>');

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

//    /**
//     * @return RegistrationConsulter|object
//     */
//    protected function getRegistrationConsulter()
//    {
//        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
//
//        return $container->get(RegistrationConsulter::class);
//    }

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

        $object->setPageTemplate($values[Assignment::PROPERTY_PAGE_TEMPLATE]);
        $object->setUseLastEntryAsTemplate(boolval($values[Assignment::PROPERTY_LAST_ENTRY_AS_TEMPLATE]));

        $this->set_content_object($object);

        return parent::create_content_object();
    }

    public function update_content_object()
    {
        /** @var Assignment $object */

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

        $object->setPageTemplate($values[Assignment::PROPERTY_PAGE_TEMPLATE]);
        $object->setUseLastEntryAsTemplate(boolval($values[Assignment::PROPERTY_LAST_ENTRY_AS_TEMPLATE]));

        $this->set_content_object($object);

        return parent::update_content_object();
    }

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

        $allowedTypeClasses = empty($allowedTypes) ?
            ['Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File'] :
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
                $allowedTypeTranslation = Translation::getInstance()->getTranslation('TypeName', array(), $basePackage);

                $typeCssClass =
                    strtolower(ClassnameUtilities::getInstance()->getPackageNameFromNamespace($basePackage));

                $defaultElements->add_element(
                    new AdvancedElementFinderElement(
                        $integrationPackageData['id'],
                        'type type_' . $typeCssClass,
                        $allowedTypeTranslation,
                        $allowedTypeTranslation
                    )
                );
            }
        }

        $element = $this->getElement(Assignment::PROPERTY_ALLOWED_TYPES);
        $element->setDefaultValues($defaultElements);
    }
}
