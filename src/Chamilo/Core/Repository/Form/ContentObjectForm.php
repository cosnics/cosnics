<?php
namespace Chamilo\Core\Repository\Form;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Core\Metadata\Relation\Service\RelationService;
use Chamilo\Core\Metadata\Service\EntityFormService;
use Chamilo\Core\Metadata\Service\EntityService;
use Chamilo\Core\Metadata\Service\InstanceFormService;
use Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance;
use Chamilo\Core\Repository\Common\Includes\ContentObjectIncludeParser;
use Chamilo\Core\Repository\Exception\NoTemplateException;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Menu\ContentObjectCategoryMenu;
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
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Architecture\Interfaces\ForcedVersionSupport;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Chamilo\Libraries\Format\Tabs\DynamicFormTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: content_object_form.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib
 */

/**
 * A form to create and edit a ContentObject.
 */
abstract class ContentObjectForm extends FormValidator
{
    /**
     * ***************************************************************************************************************
     * Tabs *
     * **************************************************************************************************************
     */
    const TAB_CONTENT_OBJECT = 'ContentObject';
    const TAB_METADATA = 'Metadata';
    const TAB_ADD_METADATA = 'AddMetadata';

    /**
     * ***************************************************************************************************************
     * Constants *
     * **************************************************************************************************************
     */
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const TYPE_COMPARE = 3;
    const TYPE_REPLY = 4;
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';
    const NEW_CATEGORY = 'new_category';

    private $allow_new_version;

    private $owner_id;

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    private $workspace;

    /**
     * The content object.
     */
    private $content_object;

    /**
     * Any extra information passed to the form.
     */
    private $extra;

    protected $form_type;

    /**
     *
     * @var DynamicFormTabsRenderer
     */
    private $tabsGenerator;

    /**
     * Constructor.
     *
     * @param $form_type int The form type; either ContentObjectForm :: TYPE_CREATE or ContentObjectForm :: TYPE_EDIT.
     * @param $content_object ContentObject The object to create or update.
     * @param $form_name string The name to use in the form tag.
     * @param $method string The method to use ('post' or 'get').
     * @param $action string The URL to which the form should be submitted.
     */
    public function __construct(
        $form_type, WorkspaceInterface $workspace, $content_object, $form_name, $method = 'post',
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

        $this->prepareTabs();
        $this->getTabsGenerator()->render();

        if ($this->form_type != self::TYPE_COMPARE)
        {
            $this->add_progress_bar(2);
            $this->add_footer();
        }
        $this->setDefaults();
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
     * Returns the ID of the owner of the content object being created or edited.
     *
     * @return int The ID.
     */
    protected function get_owner_id()
    {
        return $this->owner_id;
    }

    /**
     * Sets the ID of the owner of the content object being created or edited.
     *
     * @param int The owner id.
     */
    protected function set_owner_id($owner_id)
    {
        $this->owner_id = $owner_id;
    }

    /**
     * Returns the content object associated with this form.
     *
     * @return ContentObject The content object, or null if none.
     */
    public function get_content_object()
    {
        return $this->content_object;
    }

    protected function get_content_object_type()
    {
        return $this->content_object->get_type();
    }

    protected function get_content_object_class()
    {
        return (string) StringUtilities::getInstance()->createString($this->get_content_object_type())->upperCamelize();
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

    public function prepareTabs()
    {
        $this->addDefaultTab();
        $this->addMetadataTabs();
    }

    public function addDefaultTab()
    {
        $typeName = $this->get_content_object()->get_template_registration()->get_template()->translate('TypeName');
        $typeLogo = Theme::getInstance()->getImagePath(
            $this->get_content_object()->package(),
            'Logo/' . ($this->get_content_object()->get_template_registration_id() ? 'Template/' .
                $this->get_content_object()->get_template_registration()->get_name() . '/' : '') . '22'
        );

        $this->getTabsGenerator()->add_tab(
            new DynamicFormTab(self::TAB_CONTENT_OBJECT, $typeName, $typeLogo, 'build_general_form')
        );
    }

    public function addMetadataTabs()
    {
        $relationService = new RelationService();
        $entityService = new EntityService();

        $entityFactory = DataClassEntityFactory::getInstance();
        $entity = $entityFactory->getEntity($this->get_content_object()->class_name());

        $availableSchemaIds = $entityService->getAvailableSchemaIdsForEntityType($relationService, $entity);

        if (count($availableSchemaIds) > 0)
        {
            $entity = $entityFactory->getEntity(
                $this->get_content_object()->class_name(),
                $this->get_content_object()->get_id()
            );
            $schemaInstances = $entityService->getSchemaInstancesForEntity(new RelationService(), $entity);

            while ($schemaInstance = $schemaInstances->next_result())
            {
                $schema = $schemaInstance->getSchema();
                $this->getTabsGenerator()->add_tab(
                    new DynamicFormTab(
                        'schema-' . $schemaInstance->get_id(),
                        $schema->get_name(),
                        new FontAwesomeGlyph('info-circle', array('ident-sm')),
                        'build_metadata_form',
                        array($schemaInstance)
                    )
                );
            }

            $this->getTabsGenerator()->add_tab(
                new DynamicFormTab(
                    'add-schema',
                    Translation::get('AddMetadataSchema'),
                    new FontAwesomeGlyph('plus', array('ident-sm')),
                    'build_metadata_choice_form'
                )
            );
        }
    }

    public function build_general_form()
    {
        if ($this->form_type == self::TYPE_EDIT || $this->form_type == self::TYPE_REPLY)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self::TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        elseif ($this->form_type == self::TYPE_COMPARE)
        {
            $this->build_version_compare_form();
        }

        $this->add_attachments_form();
        $this->add_additional_elements();
    }

    /**
     *
     * @throws NoTemplateException
     * @return use core\repository\common\template\Template
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
     *
     * @return use core\repository\common\template\TemplateConfiguration
     */
    protected function get_content_object_template_configuration()
    {
        return $this->get_content_object_template()->get_configuration();
    }

    /**
     * Sets the content object associated with this form.
     *
     * @param ContentObject $content_object
     */
    protected function set_content_object($content_object)
    {
        $this->content_object = $content_object;
    }

    public function get_form_type()
    {
        return $this->form_type;
    }

    /**
     * Gets the categories defined in the user's repository.
     *
     * @return array The categories.
     */
    public function get_categories()
    {
        $categorymenu = new ContentObjectCategoryMenu($this->get_workspace());
        $renderer = new OptionsMenuRenderer();
        $categorymenu->render($renderer, 'sitemap');

        return $renderer->toArray();
    }

    /**
     * Adds the metadata form for this type
     */
    public function build_metadata_form(SchemaInstance $schemaInstance)
    {
        $entity = DataClassEntityFactory::getInstance()->getEntityFromDataClass($this->get_content_object());

        $entityFormService = new EntityFormService(
            $schemaInstance,
            $entity,
            $this,
            $this->get_content_object()->get_owner()
        );
        $entityFormService->addElements();
        $entityFormService->setDefaults();
    }

    public function build_metadata_choice_form()
    {
        $relationService = new RelationService();
        $entityService = new EntityService();
        $entity = DataClassEntityFactory::getInstance()->getEntity(
            $this->get_content_object()->class_name(),
            $this->get_content_object()->get_id()
        );

        $instanceFormService = new InstanceFormService($entity, $this);
        $instanceFormService->addElements($entityService, $relationService);
    }

    protected function build_creation_form($htmleditor_options = array(), $in_tab = false)
    {
        if (!$in_tab)
        {
            $this->addElement('category', Translation::get('GeneralProperties'));
        }

        $this->build_basic_form($htmleditor_options);

        if (!$in_tab)
        {
            $this->addElement('category');
        }
    }

    protected function build_editing_form($htmleditor_options = array(), $in_tab = false)
    {
        $object = $this->content_object;

        $owner =
            \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(User::class_name(), (int) $this->get_owner_id());

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
                    $this->addElement('hidden', 'version', null, array('class' => 'version'));
                }
                else
                {
                    $this->add_element_hider('script_block');
                    $this->addElement(
                        'checkbox',
                        'version',
                        Translation::get('CreateAsNewVersion'),
                        null,
                        array(
                            'onclick' => 'javascript:showElement(\'' . ContentObject::PROPERTY_COMMENT . '\')',
                            'class' => 'version'
                        )
                    );
                    $this->add_element_hider('begin', ContentObject::PROPERTY_COMMENT);
                    $this->addElement(
                        'text',
                        ContentObject::PROPERTY_COMMENT,
                        Translation::get('VersionComment'),
                        array("size" => "50")
                    );
                    $this->add_element_hider('end', ContentObject::PROPERTY_COMMENT);
                }
            }
        }
        $this->addElement('hidden', ContentObject::PROPERTY_ID, null, array('class' => 'content_object_id'));
        $this->addElement(
            'hidden',
            ContentObject::PROPERTY_MODIFICATION_DATE,
            null,
            array('class' => 'modification_date')
        );

        $this->addElement(
            'html',
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository', true) . 'ContentObjectUpdate.js'
            )
        );

        if (!$in_tab)
        {
            $this->addElement('category');
        }
    }

    /**
     * Builds a form to compare learning object versions.
     */
    private function build_version_compare_form()
    {
        $renderer = $this->defaultRenderer();
        $form_template = <<<EOT

<form {attributes}>
{content}
	<div class="clear">
		&nbsp;
	</div>
</form>

EOT;
        $renderer->setFormTemplate($form_template);
        $element_template = <<<EOT
	<div>
			<!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}
	</div>

EOT;
        $renderer->setElementTemplate($element_template);

        if (isset($this->extra['version_data']))
        {
            $object = $this->content_object;
            $this->add_element_hider('script_radio', $object);

            $i = 0;

            $radios = array();

            foreach ($this->extra['version_data'] as $version)
            {
                $versions = array();
                $versions[] = &$this->createElement(
                    'static',
                    null,
                    null,
                    '<span ' .
                    ($i == ($object->get_version_count() - 1) ? 'style="visibility: hidden;"' :
                        'style="visibility: visible;"') .
                    ' id="A' . $i . '">'
                );
                $versions[] = &$this->createElement(
                    'radio',
                    'object',
                    null,
                    null,
                    $version['id'],
                    'onclick="javascript:showRadio(\'B\',\'' . $i . '\')"'
                );
                $versions[] = &$this->createElement('static', null, null, '</span>');
                $versions[] = &$this->createElement(
                    'static',
                    null,
                    null,
                    '<span ' . ($i == 0 ? 'style="visibility: hidden;"' : 'style="visibility: visible;"') . ' id="B' .
                    $i .
                    '">'
                );
                $versions[] = &$this->createElement(
                    'radio',
                    'compare',
                    null,
                    null,
                    $version['id'],
                    'onclick="javascript:showRadio(\'A\',\'' . $i . '\')"'
                );
                $versions[] = &$this->createElement('static', null, null, '</span>');
                $versions[] = &$this->createElement('static', null, null, $version['html']);

                $this->addGroup($versions, null, null, "\n");
                $i ++;
            }

            $buttons[] = $this->createElement(
                'style_button',
                'submit',
                Translation::get('CompareVersions'),
                null,
                null,
                'transfer'
            );
            $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        }
    }

    private function build_basic_form($htmleditor_options = array())
    {
        $this->addElement('html', '<div id="message"></div>');
        $this->addElement(
            'hidden',
            ContentObject::PROPERTY_TEMPLATE_REGISTRATION_ID,
            $this->get_content_object()->get_template_registration_id()
        );
        $this->add_textfield(
            ContentObject::PROPERTY_TITLE,
            Translation::get('Title', array(), ClassnameUtilities::getInstance()->getNamespaceFromObject($this)),
            true,
            array('id' => 'title', 'class' => 'form-control')
        );

        if ($this->allows_category_selection())
        {
            $category_group = array();
            $category_group[] = $this->createElement(
                'select',
                ContentObject::PROPERTY_PARENT_ID,
                Translation::get('CategoryTypeName'),
                $this->get_categories(),
                array('class' => 'form-control', 'id' => "parent_id")
            );

            $category_group[] = $this->createElement(
                'image',
                'add_category',
                Theme::getInstance()->getCommonImagePath('Action/Add'),
                array('id' => 'add_category', 'style' => 'display:none')
            );
            $this->addGroup($category_group, 'category_form_group', Translation::get('CategoryTypeName'), null, false);

            $this->setInlineElementTemplate('category_form_group');

            $group = array();
            $group[] = $this->createElement('static', null, null, '<div id="' . self::NEW_CATEGORY . '">');
            $group[] = $this->createElement('static', null, null, Translation::get('AddNewCategory'));
            $group[] = $this->createElement(
                'text',
                self::NEW_CATEGORY,
                null,
                array(
                    'data-workspace-type' => $this->get_workspace()->getWorkspaceType(),
                    'data-workspace-id' => $this->get_workspace()->getId()
                )
            );
            $group[] = $this->createElement('static', null, null, '</div>');
            $this->addGroup($group);
        }

        $value = Configuration::getInstance()->get_setting(array(Manager::context(), 'description_required'));
        $required = ($value == 1) ? true : false;
        $name =
            Translation::get('Description', array(), ClassnameUtilities::getInstance()->getNamespaceFromObject($this));
        $this->add_html_editor(ContentObject::PROPERTY_DESCRIPTION, $name, $required, $htmleditor_options);
    }

    /**
     * Adds the input field for the content object tags
     */
    protected function add_tags_input()
    {
        $tags = DataManager::retrieve_content_object_tags_for_user(Session::get_user_id());

        if ($this->content_object->is_identified())
        {
            $default_tags = DataManager::retrieve_content_object_tags_for_content_object(
                $this->content_object->get_id()
            );
        }

        $tags_form_builder = new TagsFormBuilder($this);
        $tags_form_builder->build_form($tags, $default_tags);
    }

    /**
     * Gets the html with an image of the content object type and the type name
     */
    private function get_content_object_type_html()
    {
        $content_object = $this->get_content_object();
        $type = $content_object->get_type();
        $namespace = ClassnameUtilities::getInstance()->getNamespaceFromClassname($type);
        $name = Translation::get('TypeName', array(), $namespace);
        $img = '<img src="' . $content_object->get_icon_path(Theme::ICON_MINI) . '" title="' . htmlentities($name) .
            '"/>';

        return $img . ' <b>' . $name . '</b>';
    }

    /**
     * Adds a footer to the form, including a submit button.
     */
    protected function add_footer()
    {
        // separated uplaod and check behaviour into independent javascript files
        $this->addElement(
            'html',
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository', true) . 'ContentObjectFormUpload.js'
            )
        );
        // added platform option 'omit_content_object_title_check'
        // when NULL (platform option not set) or FALSE (platform option set to false)
        // check title duplicates of content objects; when it is both set and true,
        // omit this check. (this way, the platform setting is unobtrusive).

        $omitContentObjectTitleCheck = Configuration::getInstance()->get_setting(
            array('Chamilo\Core\Repository', 'omit_content_object_title_check')
        );

        if ($omitContentObjectTitleCheck != 1)
        {
            $this->addElement(
                'html',
                ResourceManager::getInstance()->get_resource_html(
                    Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository', true) .
                    'ContentObjectFormCheck.js'
                )
            );
        }

        $this->addElement(
            'html',
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'HeartBeat.js'
            )
        );

        $buttons = array();

        // should not call your button submit as it is a function on the
        // javascrip file
        switch ($this->form_type)
        {
            case self::TYPE_COMPARE :
                $buttons[] = $this->createElement(
                    'style_submit_button',
                    'submit_button',
                    Translation::get('Compare', null, Utilities::COMMON_LIBRARIES),
                    null,
                    null,
                    'transfer'
                );
                break;
            case self::TYPE_CREATE :
                $buttons[] = $this->createElement(
                    'style_submit_button',
                    'submit_button',
                    Translation::get('Create', null, Utilities::COMMON_LIBRARIES)
                );
                break;
            case self::TYPE_EDIT :
                $buttons[] = $this->createElement(
                    'style_submit_button',
                    'submit_button',
                    Translation::get('Update', null, Utilities::COMMON_LIBRARIES),
                    null,
                    null,
                    'arrow-right'
                );
                break;
            case self::TYPE_REPLY :
                $buttons[] = $this->createElement(
                    'style_submit_button',
                    'submit_button',
                    Translation::get('Reply', null, Utilities::COMMON_LIBRARIES),
                    null,
                    null,
                    'envelope'
                );
                break;
            default :
                $buttons[] = $this->createElement(
                    'style_submit_button',
                    'submit_button',
                    Translation::get('Create', null, Utilities::COMMON_LIBRARIES)
                );
                break;
        }

        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES)
        );
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
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
                'name' => 'attachments_importer',
                'maxFilesize' => $calculator->getMaximumUploadSize(),
                'uploadUrl' => $uploadUrl->getUrl(),
                'successCallbackFunction' => 'chamilo.core.repository.importAttachment.processUploadedFile',
                'sendingCallbackFunction' => 'chamilo.core.repository.importAttachment.prepareRequest',
                'removedfileCallbackFunction' => 'chamilo.core.repository.importAttachment.deleteUploadedFile'
            );

            if ($this->form_type != self::TYPE_REPLY)
            {
                $attached_objects = $object->get_attachments();
                $attachments = Utilities::content_objects_for_element_finder($attached_objects);
            }
            else
            {
                $attachments = array();
            }

            $url = Path::getInstance()->getBasePath(true) .
                'index.php?application=Chamilo%5CCore%5CRepository%5CAjax&go=XmlFeed';
            $locale = array();
            $locale['Display'] = Translation::get('AddAttachments');
            $locale['Searching'] = Translation::get('Searching', null, Utilities::COMMON_LIBRARIES);
            $locale['NoResults'] = Translation::get('NoResults', null, Utilities::COMMON_LIBRARIES);
            $locale['Error'] = Translation::get('Error', null, Utilities::COMMON_LIBRARIES);

            $this->addElement(
                'html',
                ResourceManager::getInstance()->get_resource_html(
                    Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'CollapseHorizontal.js'
                )
            );

            $this->addElement(
                'category',
                '<a href="#">' . Translation::get('Attachments') . '</a>',
                'content_object_attachments collapsible collapsed'
            );

            $this->addFileDropzone('attachments_importer', $dropZoneParameters, true);

            $this->addElement(
                'html',
                ResourceManager::getInstance()->get_resource_html(
                    Path::getInstance()->getJavascriptPath(Manager::context(), true) .
                    'Plugin/jquery.file.upload.import.js'
                )
            );

            $elem = $this->addElement(
                'element_finder',
                'attachments',
                Translation::get('SelectAttachment'),
                $url,
                $locale,
                $attachments
            );

            $this->addElement('category');

            if ($id = $object->get_id())
            {
                $elem->excludeElements(array($object->get_id()));
            }
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
                $this->addElement('category');
            }
        }
    }

    /**
     * Sets default values.
     * Traditionally, you will want to extend this method so it sets default for your learning
     * object type's additional properties.
     *
     * @param $defaults array Default values for this form's parameters.
     */
    public function setDefaults($defaults = array())
    {
        $content_object = $this->content_object;
        $defaults[ContentObject::PROPERTY_ID] = $content_object->get_id();
        $defaults[ContentObject::PROPERTY_MODIFICATION_DATE] = $content_object->get_modification_date();

        if (!$this->workspace instanceof PersonalWorkspace)
        {
            $contentObjectRelationService = new ContentObjectRelationService(new ContentObjectRelationRepository());
            $contentObjectRelation =
                $contentObjectRelationService->getContentObjectRelationForWorkspaceAndContentObject(
                    $this->workspace,
                    $content_object
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

        if ($this->form_type == self::TYPE_REPLY)
        {
            $defaults[ContentObject::PROPERTY_TITLE] =
                Translation::get('ReplyShort', null, Utilities::COMMON_LIBRARIES) .
                ' ' . $content_object->get_title();
        }
        else
        {
            $defaults[ContentObject::PROPERTY_TITLE] =
                $defaults[ContentObject::PROPERTY_TITLE] == null ? $content_object->get_title() :
                    $defaults[ContentObject::PROPERTY_TITLE];
            $defaults[ContentObject::PROPERTY_DESCRIPTION] = $content_object->get_description();
        }

        if ($content_object instanceof ForcedVersionSupport && $this->form_type == self::TYPE_EDIT)
        {
            $defaults['version'] = 1;
        }

        parent::setDefaults($defaults);
    }

    public function setParentDefaults($defaults)
    {
        parent::setDefaults($defaults);
    }

    public function set_values($defaults)
    {
        parent::setDefaults($defaults);
    }

    public function create_content_object()
    {
        $values = $this->exportValues();

        $object = $this->content_object;
        $object->set_owner_id($this->get_owner_id());
        $object->set_template_registration_id(
            $values[ContentObject::PROPERTY_TEMPLATE_REGISTRATION_ID] ?
                $values[ContentObject::PROPERTY_TEMPLATE_REGISTRATION_ID] : null
        );
        $object->set_title($values[ContentObject::PROPERTY_TITLE]);
        $desc = $values[ContentObject::PROPERTY_DESCRIPTION] ? $values[ContentObject::PROPERTY_DESCRIPTION] : '';
        $object->set_description($desc);

        if ($this->allows_category_selection() && $this->workspace instanceof PersonalWorkspace)
        {
            $this->set_category_from_values($object, $values);
        }

        $object->create();

        if ($object->has_errors())
        {
            return null;
        }

        if ($this->allows_category_selection() && $this->workspace instanceof Workspace)
        {
            $this->set_category_from_values($object, $values);
        }

        $values = $this->exportValues();

        // Process includes
        ContentObjectIncludeParser::parse_includes($this);

        // Process attachments
        if ($object instanceof AttachmentSupport)
        {
            $object->attach_content_objects($values['attachments']['lo'], ContentObject::ATTACHMENT_NORMAL);
        }

        return $object;
    }

    public function compare_content_object()
    {
        $values = $this->exportValues();
        $ids = array();
        $ids['object'] = $values['object'];
        $ids['compare'] = $values['compare'];

        return $ids;
    }

    /**
     * Sets the category id from the given form values
     *
     * @param ContentObject $object
     * @param string[] $values
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
                    $this->workspace,
                    $object
                );

            if ($contentObjectRelation instanceof WorkspaceContentObjectRelation)
            {
                $contentObjectRelationService->updateContentObjectRelation(
                    $contentObjectRelation,
                    $this->workspace->getId(),
                    $object->get_object_number(),
                    $parent_id
                );
            }
            else
            {
                $contentObjectRelationService->createContentObjectRelation(
                    $this->workspace->getId(),
                    $object->get_object_number(),
                    $parent_id
                );
            }
        }
    }

    /**
     * Creates a new category with a given name and parent id
     *
     * @param string $category_name
     * @param int $parent_id
     *
     * @return RepositoryCategory
     */
    public function create_new_category($category_name, $parent_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_PARENT),
            new StaticConditionVariable($parent_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_TYPE_ID),
            new StaticConditionVariable($this->workspace->getId())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_TYPE),
            new StaticConditionVariable($this->workspace->getWorkspaceType())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_NAME),
            new StaticConditionVariable($category_name)
        );
        $condition = new AndCondition($conditions);

        $existingNewCategory = DataManager::retrieve(
            RepositoryCategory::class_name(),
            new DataClassRetrieveParameters($condition)
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
            $new_category->set_type($this->workspace->getWorkspaceType());

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

    public function update_content_object()
    {
        $object = $this->content_object;
        $values = $this->exportValues();

        $object->set_title($values[ContentObject::PROPERTY_TITLE]);

        $desc = $values[ContentObject::PROPERTY_DESCRIPTION] ? $values[ContentObject::PROPERTY_DESCRIPTION] : '';
        $object->set_description($desc ? $desc : '');

        $move = false;
        if ($this->allows_category_selection())
        {
            $old_parent_id = $object->get_parent_id();
            $this->set_category_from_values($object, $values);
        }

        if (isset($values['version']) && $values['version'] == 1)
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

        if ($object->has_errors())
        {
            return false;
        }

        // Process includes
        ContentObjectIncludeParser::parse_includes($this);

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
            $object->attach_content_objects($values['attachments']['lo'], ContentObject::ATTACHMENT_NORMAL);
        }

        return $result;
    }

    public function is_version()
    {
        $values = $this->exportValues();

        return (isset($values['version']) && $values['version'] == 1);
    }

    protected function allows_category_selection()
    {
        return ($this->form_type == self::TYPE_CREATE || $this->form_type == self::TYPE_REPLY ||
            $this->form_type == self::TYPE_EDIT) && Session::get_user_id() == $this->get_owner_id();
    }

    /**
     * Creates a form object to manage an content object.
     *
     * @param $form_type int The form type; either ContentObjectForm :: TYPE_CREATE or ContentObjectForm :: TYPE_EDIT.
     * @param $content_object ContentObject The object to create or update.
     * @param $form_name string The name to use in the form tag.
     * @param $method string The method to use ('post' or 'get').
     * @param $action string The URL to which the form should be submitted.
     *
     * @return ContentObjectForm
     */
    public static function factory(
        $form_type, WorkspaceInterface $workspace, $content_object, $form_name,
        $method = 'post', $action = null, $extra = null, $additional_elements = array(), $allow_new_version = true,
        $form_variant = null
    )
    {
        $type = $content_object->get_type();

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
            $form_type,
            $workspace,
            $content_object,
            $form_name,
            $method,
            $action,
            $extra,
            $additional_elements,
            $allow_new_version
        );
    }

    /**
     * Validates this form
     *
     * @see FormValidator::validate
     */
    public function validate()
    {
        if ($this->isSubmitted())
        {
            $values = $this->exportValues();

            if ($this->form_type == self::TYPE_COMPARE)
            {
                if (!isset($values['object']) || !isset($values['compare']))
                {
                    return false;
                }
            }
        }

        return parent::validate();
    }

    /**
     *
     * @deprecated Use buildInstructionsForm() now
     */
    protected function add_example_box()
    {
        $this->addElement(
            'html',
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'CollapseHorizontal.js'
            )
        );

        $this->addElement(
            'category',
            '<a href="#">' . Translation::get('Instructions', null, Utilities::COMMON_LIBRARIES) . '</a>',
            'content_object_attachments collapsible collapsed'
        );

        $this->buildInstructionsForm();
    }

    public function addInstructionsTab()
    {
        $instructions = Translation::get(
            'InstructionsText',
            null,
            ClassnameUtilities::getInstance()->getNamespaceFromClassname(get_class($this))
        );

        if ($instructions != 'InstructionsText')
        {
            $this->getTabsGenerator()->add_tab(
                new DynamicFormTab(
                    'view-instructions',
                    Translation::get('ViewInstructions'),
                    new FontAwesomeGlyph('question-circle', array('ident-sm')),
                    'buildInstructionsForm'
                )
            );
        }
    }

    public function buildInstructionsForm()
    {
        $this->addElement(
            'html',
            '<div>' . Translation::get(
                'InstructionsText',
                null,
                ClassnameUtilities::getInstance()->getNamespaceFromClassname(get_class($this))
            ) . '</div>'
        );
    }
}
