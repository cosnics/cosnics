<?php
namespace Chamilo\Core\Repository\Form;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Core\Metadata\Service\EntityFormService;
use Chamilo\Core\Metadata\Service\EntityService;
use Chamilo\Core\Metadata\Service\InstanceFormService;
use Chamilo\Core\Metadata\Service\InstanceService;
use Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance;
use Chamilo\Core\Repository\Common\Includes\ContentObjectIncludeParser;
use Chamilo\Core\Repository\Exception\NoTemplateException;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Menu\ContentObjectCategoryMenu;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregator;
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Architecture\Interfaces\ForcedVersionSupport;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Chamilo\Libraries\Format\Tabs\DynamicFormTabsRenderer;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * @package Chamilo\Core\Repository\Form
 */
abstract class ContentObjectForm extends FormValidator
{
    const NEW_CATEGORY = 'new_category';
    const PROPERTY_ATTACHMENTS = 'attachments';
    const PROPERTY_VERSION = 'version';

    const RESULT_ERROR = 'ObjectUpdateFailed';
    const RESULT_SUCCESS = 'ObjectUpdated';

    const TAB_ADD_METADATA = 'AddMetadata';
    const TAB_CONTENT_OBJECT = 'ContentObject';
    const TAB_METADATA = 'Metadata';

    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;

    /**
     * @var integer
     */
    protected $form_type;

    /**
     * @var string
     */
    protected $selectedTabIdentifier;

    /**
     * @var boolean
     */
    private $allow_new_version;

    /**
     * @var integer
     */
    private $owner_id;

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    private $workspace;

    /**
     * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    private $content_object;

    /**
     * Any extra information passed to the form.
     */
    private $extra;

    /**
     * @var \HTML_QuickForm_element[]
     */
    private $additional_elements;

    /**
     *
     * @var DynamicFormTabsRenderer
     */
    private $tabsGenerator;

    /**
     * @param integer $form_type
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspace
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $content_object
     * @param string $form_name
     * @param string $method
     * @param string $action
     * @param string[] $extra
     * @param \HTML_QuickForm_element[] $additional_elements
     * @param boolean $allow_new_version
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Exception
     */
    public function __construct(
        $form_type, WorkspaceInterface $workspace, $content_object, $form_name, $method = self::FORM_METHOD_POST,
        $action = null, $extra = null, $additional_elements, $allow_new_version = true
    )
    {
        parent::__construct($form_name, $method, $action);

        $this->form_type = $form_type;
        $this->workspace = $workspace;
        $this->content_object = $content_object;
        $this->owner_id = $content_object->get_owner_id();
        $this->extra = $extra;
        $this->additional_elements = $additional_elements;
        $this->allow_new_version = $allow_new_version;

        if ($form_type == self::TYPE_EDIT &&
            !$this->getPublicationAggregator()->canContentObjectBeEdited($content_object->getId()))
        {
            throw new NotAllowedException();
        }

        $this->prepareTabs();
        $this->getTabsGenerator()->render();

        $this->add_footer();

        $this->setDefaults();
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function addDefaultTab()
    {
        $typeName = $this->get_content_object()->get_template_registration()->get_template()->translate('TypeName');

        $this->getTabsGenerator()->add_tab(
            new DynamicFormTab(
                self::TAB_CONTENT_OBJECT, $typeName, $this->get_content_object()->getGlyph(), 'build_general_form'
            )
        );
    }

    public function addInstructionsTab()
    {
        $instructions = Translation::get(
            'InstructionsText', null, ClassnameUtilities::getInstance()->getNamespaceFromClassname(get_class($this))
        );

        if ($instructions != 'InstructionsText')
        {
            $this->getTabsGenerator()->add_tab(
                new DynamicFormTab(
                    'view-instructions', Translation::get('ViewInstructions'),
                    new FontAwesomeGlyph('question-circle', array('fa-lg'), null, 'fas'), 'buildInstructionsForm'
                )
            );
        }
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function addMetadataTabs()
    {
        $entityService = $this->getEntityService();

        $entityFactory = $this->getDataClassEntityFactory();
        $entity = $entityFactory->getEntity($this->get_content_object()->class_name());

        $availableSchemaIds = $entityService->getAvailableSchemaIdsForEntityType($entity);

        if (count($availableSchemaIds) > 0)
        {
            $entity = $entityFactory->getEntity(
                $this->get_content_object()->class_name(), $this->get_content_object()->get_id()
            );
            $schemaInstances = $entityService->getSchemaInstancesForEntity($entity);

            foreach($schemaInstances as $schemaInstance)
            {
                $schema = $schemaInstance->getSchema();
                $this->getTabsGenerator()->add_tab(
                    new DynamicFormTab(
                        'schema-' . $schemaInstance->get_id(), $schema->get_name(),
                        new FontAwesomeGlyph('info-circle', array('fa-lg'), null, 'fas'), 'build_metadata_form',
                        array($schemaInstance)
                    )
                );
            }

            $this->getTabsGenerator()->add_tab(
                new DynamicFormTab(
                    'add-schema', Translation::get('AddMetadataSchema'),
                    new FontAwesomeGlyph('plus', array('fa-lg'), null, 'fas'), 'build_metadata_choice_form'
                )
            );
        }
    }

    /**
     * Adds additional elements to the form
     */
    protected function add_additional_elements()
    {
        if (count($this->additional_elements) > 0)
        {
            $count = 0;
            foreach ($this->additional_elements as $element)
            {
                if ($element->getType() != 'hidden')
                {
                    $count ++;
                }
            }

            if ($count > 0)
            {
                $this->addElement('category', Translation::get('AdditionalProperties'));
                foreach ($this->additional_elements as $element)
                {
                    $this->addElement($element);
                }
            }
        }
    }

    /**
     * Adds the attachments form
     */
    protected function add_attachments_form()
    {
        $object = $this->content_object;

        if ($object instanceof AttachmentSupport)
        {
            $calculator = new Calculator(
                \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                    User::class, (int) $this->get_owner_id()
                )
            );

            $uploadUrl = new Redirect(
                array(
                    Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Ajax\Manager::context(),
                    \Chamilo\Core\Repository\Ajax\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Ajax\Manager::ACTION_IMPORT_FILE
                )
            );

            $dropZoneParameters = array(
                'name' => 'attachments_importer',
                'maxFilesize' => $calculator->getMaximumUploadSize(),
                'uploadUrl' => $uploadUrl->getUrl(),
                'successCallbackFunction' => 'chamilo.core.repository.importAttachment.processUploadedFile',
                'sendingCallbackFunction' => 'chamilo.core.repository.importAttachment.prepareRequest',
                'removedfileCallbackFunction' => 'chamilo.core.repository.importAttachment.deleteUploadedFile'
            );

            $this->addElement(
                'category', Translation::get('Attachments')
            );

            $this->addFileDropzone('attachments_importer', $dropZoneParameters, true);

            $this->addElement(
                'html', ResourceManager::getInstance()->getResourceHtml(
                Path::getInstance()->getJavascriptPath(Manager::context(), true) . 'Plugin/jquery.file.upload.import.js'
            )
            );

            $types = new AdvancedElementFinderElementTypes();
            $types->add_element_type(
                new AdvancedElementFinderElementType(
                    'content_objects', Translation::get('ContentObjects'), 'Chamilo\Core\Repository\Ajax',
                    'AttachmentContentObjectsFeed', array('exclude_content_object_ids' => array($object->getId()))
                )
            );

            $this->addElement(
                'advanced_element_finder', self::PROPERTY_ATTACHMENTS, Translation::get('SelectAttachment'), $types
            );
        }
    }

    /**
     * Adds a footer to the form, including a submit button.
     */
    protected function add_footer()
    {
        // separated upload and check behaviour into independent javascript files
        $this->addElement(
            'html', ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository', true) . 'ContentObjectFormUpload.min.js'
        )
        );

        $omitContentObjectTitleCheck = Configuration::getInstance()->get_setting(
            array('Chamilo\Core\Repository', 'omit_content_object_title_check')
        );

        if ($omitContentObjectTitleCheck != 1)
        {
            $this->addElement(
                'html', ResourceManager::getInstance()->getResourceHtml(
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository', true) . 'ContentObjectFormCheck.js'
            )
            );
        }

        $this->addElement(
            'html', ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'HeartBeat.js'
        )
        );

        // should not call your button submit as it is a function on the
        // javascrip file
        switch ($this->form_type)
        {
            case self::TYPE_EDIT :
                $glyphName = 'arrow-right';
                $buttonVariable = 'Update';
                break;
            default :
                $glyphName = 'check';
                $buttonVariable = 'Create';
                break;
        }

        $buttons = [];

        $buttons[] = $this->createElement(
            'style_submit_button', 'submit_button',
            Translation::get($buttonVariable, null, Utilities::COMMON_LIBRARIES), null, null,
            new FontAwesomeGlyph($glyphName)
        );

        $buttons[] = $this->createElement(
            'style_reset_button', 'reset', Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * @return boolean
     */
    protected function allows_category_selection()
    {
        return ($this->form_type == self::TYPE_CREATE || $this->form_type == self::TYPE_EDIT) &&
            Session::get_user_id() == $this->get_owner_id();
    }

    public function buildInstructionsForm()
    {
        $this->addElement(
            'html', '<div>' . Translation::get(
                'InstructionsText', null, ClassnameUtilities::getInstance()->getNamespaceFromClassname(get_class($this))
            ) . '</div>'
        );
    }

    /**
     * @param string[] $htmleditor_options
     *
     * @throws \Exception
     */
    private function build_basic_form($htmleditor_options = [])
    {
        $this->addElement('html', '<div id="message"></div>');
        $this->addElement(
            'hidden', ContentObject::PROPERTY_TEMPLATE_REGISTRATION_ID,
            $this->get_content_object()->get_template_registration_id()
        );
        $this->add_textfield(
            ContentObject::PROPERTY_TITLE,
            Translation::get('Title', [], ClassnameUtilities::getInstance()->getNamespaceFromObject($this)), true,
            array('id' => 'title', 'class' => 'form-control')
        );

        if ($this->allows_category_selection())
        {
            $this->addElement(
                'select', ContentObject::PROPERTY_PARENT_ID, Translation::get('CategoryTypeName'),
                $this->get_categories(), array('class' => 'form-control', 'id' => "parent_id")
            );

            $category_group = [];

            $category_group[] = $this->createElement('static', null, null, '<div class="input-group">');

            $category_group[] = $this->createElement(
                'static', null, null,
                '<span class="input-group-addon">' . Translation::get('AddNewCategory') . '</span>'
            );

            $category_group[] = $this->createElement(
                'text', self::NEW_CATEGORY, null, array(
                    'class' => 'form-control',
                    'data-workspace-type' => $this->get_workspace()->getWorkspaceType(),
                    'data-workspace-id' => $this->get_workspace()->getId()
                )
            );
            $category_group[] = $this->createElement('static', null, null, '</div>');

            $this->addGroup($category_group, 'category_form_group', null, ' ', false);
        }

        $value = Configuration::getInstance()->get_setting(array(Manager::context(), 'description_required'));
        $required = $value == 1;
        $name =
            Translation::get('Description', [], ClassnameUtilities::getInstance()->getNamespaceFromObject($this));
        $this->add_html_editor(ContentObject::PROPERTY_DESCRIPTION, $name, $required, $htmleditor_options);
    }

    /**
     * @param string[] $htmleditor_options
     * @param boolean $in_tab
     *
     * @throws \Exception
     */
    protected function build_creation_form($htmleditor_options = [], $in_tab = false)
    {
        if (!$in_tab)
        {
            $this->addElement('category', Translation::get('GeneralProperties'));
        }

        $this->build_basic_form($htmleditor_options);
    }

    /**
     * @param string[] $htmleditor_options
     * @param boolean $in_tab
     *
     * @throws \Exception
     */
    protected function build_editing_form($htmleditor_options = [], $in_tab = false)
    {
        $object = $this->content_object;

        $owner =
            \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(User::class, (int) $this->get_owner_id());

        if (!$in_tab)
        {
            $this->addElement('category', Translation::get('GeneralProperties'));
        }

        $this->build_basic_form($htmleditor_options);

        if ($object instanceof Versionable && $this->allow_new_version)
        {
            if (!$object->is_external())
            {
                if ($object instanceof ForcedVersionSupport)
                {
                    $this->addElement('hidden', self::PROPERTY_VERSION, null, array('class' => 'version'));
                }
                else
                {
                    $this->addElement(
                        'checkbox', self::PROPERTY_VERSION, Translation::get('CreateAsNewVersion'), null,
                        array('class' => 'version')
                    );

                    $this->addElement('html', '<div class="content-object-version-comment hidden">');
                    $this->addElement(
                        'text', ContentObject::PROPERTY_COMMENT, Translation::get('VersionComment'),
                        array('size' => '50', 'class' => 'form-control')
                    );

                    $this->addElement('html', '</div>');
                }
            }
        }

        $this->addElement('hidden', ContentObject::PROPERTY_ID, null, array('class' => 'content_object_id'));
        $this->addElement(
            'hidden', ContentObject::PROPERTY_MODIFICATION_DATE, null, array('class' => 'modification_date')
        );

        $this->addElement(
            'html', ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository', true) . 'ContentObjectUpdate.js'
        )
        );
    }

    /**
     * @throws \Exception
     */
    public function build_general_form()
    {
        if ($this->form_type == self::TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self::TYPE_CREATE)
        {
            $this->build_creation_form();
        }

        $this->add_attachments_form();
        $this->add_additional_elements();
    }

    public function build_metadata_choice_form()
    {
        $entity = $this->getDataClassEntityFactory()->getEntity(
            $this->get_content_object()->class_name(), $this->get_content_object()->get_id()
        );

        $this->getInstanceFormService()->addElements($this, $entity);
    }

    /**
     * @param \Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance $schemaInstance
     */
    public function build_metadata_form(SchemaInstance $schemaInstance)
    {
        $entity = $this->getDataClassEntityFactory()->getEntityFromDataClass($this->get_content_object());

        $entityFormService = $this->getEntityFormService();
        $entityFormService->addElements($this, $schemaInstance, $entity, $this->get_content_object()->get_owner());
        $entityFormService->setDefaults($schemaInstance, $entity, $this, $this->get_content_object()->get_owner());
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function create_content_object()
    {
        $values = $this->exportValues();

        $object = $this->content_object;
        $object->set_owner_id($this->get_owner_id());
        $object->set_template_registration_id(
            $values[ContentObject::PROPERTY_TEMPLATE_REGISTRATION_ID] ?: null
        );
        $object->set_title($values[ContentObject::PROPERTY_TITLE]);
        $desc = $values[ContentObject::PROPERTY_DESCRIPTION] ?: '';
        $object->set_description($desc);

        if ($this->allows_category_selection() && $this->workspace instanceof PersonalWorkspace)
        {
            $this->set_category_from_values($object, $values);
        }

        $object->create();

        if ($object->hasErrors())
        {
            return null;
        }

        if ($this->allows_category_selection() && $this->workspace instanceof Workspace)
        {
            $this->set_category_from_values($object, $values);
        }

        $values = $this->exportValues();

        // Process includes
        ContentObjectIncludeParser::parse_includes($this->get_content_object(), $this->get_html_editors());

        // Process attachments
        if ($object instanceof AttachmentSupport)
        {
            $object->attach_content_objects(
                $values[self::PROPERTY_ATTACHMENTS]['content_object'], ContentObject::ATTACHMENT_NORMAL
            );
        }

        return $object;
    }

    /**
     * @param string $category_name
     * @param integer $parent_id
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function create_new_category($category_name, $parent_id)
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_PARENT),
            new StaticConditionVariable($parent_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_TYPE_ID),
            new StaticConditionVariable($this->workspace->getId())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_TYPE),
            new StaticConditionVariable($this->workspace->getWorkspaceType())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_NAME),
            new StaticConditionVariable($category_name)
        );
        $condition = new AndCondition($conditions);

        $existingNewCategory = DataManager::retrieve(
            RepositoryCategory::class, new DataClassRetrieveParameters($condition)
        );

        if ($existingNewCategory instanceof RepositoryCategory)
        {
            return $existingNewCategory;
        }
        else
        {

            $new_category = new RepositoryCategory();
            $new_category->set_name($category_name);
            $new_category->set_parent($parent_id);
            $new_category->set_type_id($this->workspace->getId());
            $new_category->setType($this->workspace->getWorkspaceType());

            if (!$new_category->create())
            {
                return null;
            }
            else
            {
                return $new_category;
            }
        }
    }

    /**
     * @param integer $form_type
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspace
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $content_object
     * @param string $form_name
     * @param string $method
     * @param string $action
     * @param string[] $extra
     * @param \HTML_QuickForm_element[] $additional_elements
     * @param boolean $allow_new_version
     * @param string $form_variant
     *
     * @return mixed
     */
    public static function factory(
        $form_type, WorkspaceInterface $workspace, $content_object, $form_name, $method = self::FORM_METHOD_POST,
        $action = null, $extra = null, $additional_elements = [], $allow_new_version = true, $form_variant = null
    )
    {
        $base_class_name = $content_object->package() . '\Form\\' . $content_object->class_name(false);

        if ($form_variant)
        {
            $class = $base_class_name . StringUtilities::getInstance()->createString($form_variant)->upperCamelize() .
                'Form';
        }
        else
        {
            $class = $base_class_name . 'Form';
        }

        return new $class(
            $form_type, $workspace, $content_object, $form_name, $method, $action, $extra, $additional_elements,
            $allow_new_version
        );
    }

    /**
     * @return \Chamilo\Core\Metadata\Entity\DataClassEntityFactory
     */
    public function getDataClassEntityFactory()
    {
        return $this->getService(DataClassEntityFactory::class);
    }

    /**
     * @return \Chamilo\Core\Metadata\Service\EntityFormService
     */
    private function getEntityFormService()
    {
        return $this->getService(EntityFormService::class);
    }

    /**
     * @return \Chamilo\Core\Metadata\Service\EntityService
     */
    private function getEntityService()
    {
        return $this->getService(EntityService::class);
    }

    /**
     * @return \Chamilo\Core\Metadata\Service\InstanceFormService
     */
    private function getInstanceFormService()
    {
        return $this->getService(InstanceFormService::class);
    }

    /**
     * @return \Chamilo\Core\Metadata\Service\InstanceService
     */
    private function getInstanceService()
    {
        return $this->getService(InstanceService::class);
    }

    /**
     * @return \Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface
     */
    protected function getPublicationAggregator()
    {
        return $this->getService(PublicationAggregator::class);
    }

    /**
     * @return string
     */
    public function getSelectedTabIdentifier()
    {
        return $this->selectedTabIdentifier;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Tabs\DynamicFormTabsRenderer
     */
    public function getTabsGenerator()
    {
        if (!isset($this->tabsGenerator))
        {
            $this->tabsGenerator = new DynamicFormTabsRenderer(Manager::TABS_CONTENT_OBJECT, $this);
        }

        return $this->tabsGenerator;
    }

    /**
     * @return string[]
     */
    public function get_categories()
    {
        $categorymenu = new ContentObjectCategoryMenu($this->get_workspace());
        $renderer = new OptionsMenuRenderer();
        $categorymenu->render($renderer, 'sitemap');

        return $renderer->toArray();
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function get_content_object()
    {
        return $this->content_object;
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $content_object
     */
    protected function set_content_object($content_object)
    {
        $this->content_object = $content_object;
    }

    /**
     * @return string
     */
    protected function get_content_object_class()
    {
        return (string) StringUtilities::getInstance()->createString($this->get_content_object_type())->upperCamelize();
    }

    /**
     * @return \Chamilo\Core\Repository\Common\Template\Template
     * @throws \Chamilo\Core\Repository\Exception\NoTemplateException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function get_content_object_template()
    {
        $template_registration = $this->get_content_object()->get_template_registration();

        if ($template_registration instanceof TemplateRegistration)
        {
            return $template_registration->get_template();
        }
        else
        {
            throw new NoTemplateException();
        }
    }

    /**
     * @return \Chamilo\Core\Repository\Common\Template\TemplateConfiguration
     * @throws \Chamilo\Core\Repository\Exception\NoTemplateException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function get_content_object_template_configuration()
    {
        return $this->get_content_object_template()->get_configuration();
    }

    /**
     * @return string
     */
    protected function get_content_object_type()
    {
        return $this->content_object->getType();
    }

    /**
     * @return integer
     */
    public function get_form_type()
    {
        return $this->form_type;
    }

    /**
     * @return integer
     */
    protected function get_owner_id()
    {
        return $this->owner_id;
    }

    /**
     * @param integer $owner_id
     */
    protected function set_owner_id($owner_id)
    {
        $this->owner_id = $owner_id;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    public function get_workspace()
    {
        return $this->workspace;
    }

    /**
     * @return boolean
     */
    public function is_version()
    {
        $values = $this->exportValues();

        return (isset($values[self::PROPERTY_VERSION]) && $values[self::PROPERTY_VERSION] == 1);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function prepareTabs()
    {
        $this->addDefaultTab();
        $this->addMetadataTabs();
    }

    /**
     * @param string[] $defaults
     * @param mixed $filter
     *
     * @throws \Exception
     */
    public function setDefaults($defaults = [], $filter = null)
    {
        $content_object = $this->content_object;
        $defaults[ContentObject::PROPERTY_ID] = $content_object->get_id();
        $defaults[ContentObject::PROPERTY_MODIFICATION_DATE] = $content_object->get_modification_date();

        if (!$this->workspace instanceof PersonalWorkspace)
        {
            $contentObjectRelationService = new ContentObjectRelationService(new ContentObjectRelationRepository());
            $contentObjectRelation =
                $contentObjectRelationService->getContentObjectRelationForWorkspaceAndContentObject(
                    $this->workspace, $content_object
                );

            if ($contentObjectRelation)
            {
                $defaults[ContentObject::PROPERTY_PARENT_ID] = $contentObjectRelation->getCategoryId();
            }
        }

        if (!array_key_exists(ContentObject::PROPERTY_PARENT_ID, $defaults))
        {
            $defaults[ContentObject::PROPERTY_PARENT_ID] = $content_object->get_parent_id();
        }

        $defaults[ContentObject::PROPERTY_TEMPLATE_REGISTRATION_ID] = $content_object->get_template_registration_id();

        $defaults[ContentObject::PROPERTY_TITLE] =
            $defaults[ContentObject::PROPERTY_TITLE] == null ? $content_object->get_title() :
                $defaults[ContentObject::PROPERTY_TITLE];
        $defaults[ContentObject::PROPERTY_DESCRIPTION] = $content_object->get_description();

        if ($content_object instanceof ForcedVersionSupport && $this->form_type == self::TYPE_EDIT)
        {
            $defaults[self::PROPERTY_VERSION] = 1;
        }

        if ($content_object instanceof AttachmentSupport)
        {
            $attachments = $content_object->get_attachments();

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

            $element = $this->getElement(self::PROPERTY_ATTACHMENTS);
            $element->setDefaultValues($defaultAttachments);
        }

        parent::setDefaults($defaults);
    }

    /**
     * @param string[] $defaults
     *
     * @throws \Exception
     */
    public function setParentDefaults($defaults)
    {
        parent::setDefaults($defaults);
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $object
     * @param string[] $values
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function set_category_from_values($object, $values)
    {
        $parent_id = $values[ContentObject::PROPERTY_PARENT_ID];
        $new_category_name = $values[self::NEW_CATEGORY];

        if (!StringUtilities::getInstance()->isNullOrEmpty($new_category_name, true))
        {
            $new_category = $this->create_new_category($new_category_name, $parent_id);
            if ($new_category)
            {
                $parent_id = $new_category->get_id();
            }
        }

        if ($this->workspace instanceof PersonalWorkspace)
        {
            $object->set_parent_id($parent_id);
        }
        else
        {
            $contentObjectRelationService = new ContentObjectRelationService(new ContentObjectRelationRepository());
            $contentObjectRelation =
                $contentObjectRelationService->getContentObjectRelationForWorkspaceAndContentObject(
                    $this->workspace, $object
                );

            if ($contentObjectRelation instanceof WorkspaceContentObjectRelation)
            {
                $contentObjectRelationService->updateContentObjectRelation(
                    $contentObjectRelation, $this->workspace->getId(), $object->get_object_number(), $parent_id
                );
            }
            else
            {
                $contentObjectRelationService->createContentObjectRelation(
                    $this->workspace->getId(), $object->get_object_number(), $parent_id
                );
            }
        }
    }

    /**
     * @param string[] $defaults
     *
     * @throws \Exception
     */
    public function set_values($defaults)
    {
        parent::setDefaults($defaults);
    }

    /**
     * @return boolean
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function update_content_object()
    {
        $object = $this->content_object;
        $values = $this->exportValues();

        $object->set_title($values[ContentObject::PROPERTY_TITLE]);

        $desc = $values[ContentObject::PROPERTY_DESCRIPTION] ?: '';
        $object->set_description($desc ?: '');

        $move = false;
        if ($this->allows_category_selection())
        {
            $old_parent_id = $object->get_parent_id();
            $this->set_category_from_values($object, $values);
        }

        if (isset($values[self::PROPERTY_VERSION]) && $values[self::PROPERTY_VERSION] == 1)
        {
            $object->set_comment(nl2br($values[ContentObject::PROPERTY_COMMENT]));
            $result = $object->version();

            $versions = DataManager::retrieve_content_object_versions($object);
            foreach ($versions as $version)
            {
                if ($version->get_parent_id() != $object->get_parent_id())
                {
                    $version->move($object->get_parent_id());
                }
            }
        }
        else
        {
            $result = $object->update();
        }

        if ($object->hasErrors())
        {
            return false;
        }

        // Process includes
        ContentObjectIncludeParser::parse_includes($this->get_content_object(), $this->get_html_editors());

        // $include_parser->parse_editors();

        // Process attachments
        if ($object instanceof AttachmentSupport)
        {
            /*
             * TODO: Make this faster by providing a function that matches the existing IDs against the ones that need
             * to be added, and attaches and detaches accordingly.
             */
            foreach ($object->get_attachments() as $attached_object_id)
            {
                $object->detach_content_object($attached_object_id->get_id(), ContentObject::ATTACHMENT_NORMAL);
            }
            $object->attach_content_objects(
                $values[self::PROPERTY_ATTACHMENTS]['content_object'], ContentObject::ATTACHMENT_NORMAL
            );
        }

        $user = new User();
        $user->setId($this->get_owner_id());

        $this->selectedTabIdentifier = $this->getInstanceService()->updateInstances(
            $user, $object, (array) $values[InstanceService::PROPERTY_METADATA_ADD_SCHEMA]
        );

        $entity = $this->getDataClassEntityFactory()->getEntityFromDataClass($object);
        $this->getEntityService()->updateEntitySchemaValues(
            $user, $entity, $values[EntityService::PROPERTY_METADATA_SCHEMA]
        );

        return $result;
    }
}
