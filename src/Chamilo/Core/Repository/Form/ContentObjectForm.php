<?php
namespace Chamilo\Core\Repository\Form;

use Chamilo\Core\Metadata\Value\Element\Form\Handler\ElementValueEditorFormHandler;
use Chamilo\Core\Repository\Common\Includes\ContentObjectIncludeParser;
use Chamilo\Core\Repository\Exception\NoTemplateException;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\ContentObjectMetadataValueCreator;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Menu\ContentObjectCategoryMenu;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Architecture\Interfaces\ForcedVersionSupport;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Ehb\Core\Metadata\Service\EntityService;
use Ehb\Core\Metadata\Relation\Service\RelationService;
use Chamilo\Libraries\Format\Tabs\DynamicFormTabsRenderer;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Ehb\Core\Metadata\Service\EntityFormService;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Service\RepositoryEntityService;
use Ehb\Core\Metadata\Schema\Instance\Storage\DataClass\SchemaInstance;

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
     * The content object.
     */
    private $content_object;

    /**
     * Any extra information passed to the form.
     */
    private $extra;

    protected $form_type;

    /**
     * Constructor.
     *
     * @param $form_type int The form type; either ContentObjectForm :: TYPE_CREATE or ContentObjectForm :: TYPE_EDIT.
     * @param $content_object ContentObject The object to create or update.
     * @param $form_name string The name to use in the form tag.
     * @param $method string The method to use ('post' or 'get').
     * @param $action string The URL to which the form should be submitted.
     */
    public function __construct($form_type, $content_object, $form_name, $method = 'post', $action = null, $extra = null,
        $additional_elements, $allow_new_version = true)
    {
        parent :: __construct($form_name, $method, $action);

        $this->form_type = $form_type;
        $this->content_object = $content_object;
        $this->owner_id = $content_object->get_owner_id();
        $this->extra = $extra;
        $this->additional_elements = $additional_elements;
        $this->allow_new_version = $allow_new_version;

        $this->prepareTabs();

        if ($this->form_type != self :: TYPE_COMPARE)
        {
            $this->add_progress_bar(2);
            $this->add_footer();
        }
        $this->setDefaults();
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
        return (string) StringUtilities :: getInstance()->createString($this->get_content_object_type())->upperCamelize();
    }

    public function prepareTabs()
    {
        $tabs_generator = new DynamicFormTabsRenderer($this->getAttribute('name'), $this);

        $tabs_generator->add_tab(
            new DynamicFormTab(
                self :: TAB_CONTENT_OBJECT,
                Translation :: get(
                    (string) StringUtilities :: getInstance()->createString(self :: TAB_CONTENT_OBJECT)->upperCamelize()),
                Theme :: getInstance()->getImagePath('Chamilo\Core\Repository', 'Tab/' . self :: TAB_CONTENT_OBJECT),
                'build_general_form'));

        $entityService = new EntityService();
        $repositoryEntityService = new RepositoryEntityService();
        $schemaInstances = $repositoryEntityService->getSchemaInstancesForContentObject(
            $entityService,
            new RelationService(),
            $this->get_content_object());

        while ($schemaInstance = $schemaInstances->next_result())
        {
            $schema = $schemaInstance->getSchema();
            $tabs_generator->add_tab(
                new DynamicFormTab(
                    'schema-' . $schema->get_id(),
                    $schema->get_name(),
                    Theme :: getInstance()->getImagePath('Chamilo\Core\Repository', 'Tab/' . self :: TAB_METADATA),
                    'build_metadata_form',
                    array($schemaInstance)));
        }

        $tabs_generator->render();
    }

    public function build_general_form()
    {
        if ($this->form_type == self :: TYPE_EDIT || $this->form_type == self :: TYPE_REPLY)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        elseif ($this->form_type == self :: TYPE_COMPARE)
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
     * @param $content_object The content object
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
        $categorymenu = new ContentObjectCategoryMenu($this->get_owner_id());
        $renderer = new OptionsMenuRenderer();
        $categorymenu->render($renderer, 'sitemap');

        return $renderer->toArray();
    }

    /**
     * Adds the metadata form for this type
     */
    public function build_metadata_form(SchemaInstance $schemaInstance)
    {
        $entityFormService = new EntityFormService($schemaInstance, $this->get_content_object(), $this);
        $entityFormService->addElements();
    }

    protected function build_creation_form($htmleditor_options = array(), $in_tab = false)
    {
        if (! $in_tab)
        {
            $this->addElement('category', Translation :: get('GeneralProperties'));
        }

        $this->build_basic_form($htmleditor_options);

        if (! $in_tab)
        {
            $this->addElement('category');
        }
    }

    protected function build_editing_form($htmleditor_options = array(), $in_tab = false)
    {
        $object = $this->content_object;
        $owner = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
            User :: class_name(),
            (int) $this->get_owner_id());

        if (! $in_tab)
        {
            $this->addElement('category', Translation :: get('GeneralProperties'));
        }

        $this->build_basic_form($htmleditor_options);
        if ($object instanceof Versionable && $this->allow_new_version)
        {
            if (! $object->is_external())
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
                        Translation :: get('CreateAsNewVersion'),
                        null,
                        array(
                            'onclick' => 'javascript:showElement(\'' . ContentObject :: PROPERTY_COMMENT . '\')',
                            'class' => 'version'));
                    $this->add_element_hider('begin', ContentObject :: PROPERTY_COMMENT);
                    $this->addElement(
                        'text',
                        ContentObject :: PROPERTY_COMMENT,
                        Translation :: get('VersionComment'),
                        array("size" => "50"));
                    $this->add_element_hider('end', ContentObject :: PROPERTY_COMMENT);
                }
            }
        }
        $this->addElement('hidden', ContentObject :: PROPERTY_ID, null, array('class' => 'content_object_id'));
        $this->addElement(
            'hidden',
            ContentObject :: PROPERTY_MODIFICATION_DATE,
            null,
            array('class' => 'modification_date'));

        $this->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository', true) . 'ContentObjectUpdate.js'));

        if (! $in_tab)
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
                $versions[] = & $this->createElement(
                    'static',
                    null,
                    null,
                    '<span ' .
                         ($i == ($object->get_version_count() - 1) ? 'style="visibility: hidden;"' : 'style="visibility: visible;"') .
                         ' id="A' . $i . '">');
                $versions[] = & $this->createElement(
                    'radio',
                    'object',
                    null,
                    null,
                    $version['id'],
                    'onclick="javascript:showRadio(\'B\',\'' . $i . '\')"');
                $versions[] = & $this->createElement('static', null, null, '</span>');
                $versions[] = & $this->createElement(
                    'static',
                    null,
                    null,
                    '<span ' . ($i == 0 ? 'style="visibility: hidden;"' : 'style="visibility: visible;"') . ' id="B' . $i .
                         '">');
                $versions[] = & $this->createElement(
                    'radio',
                    'compare',
                    null,
                    null,
                    $version['id'],
                    'onclick="javascript:showRadio(\'A\',\'' . $i . '\')"');
                $versions[] = & $this->createElement('static', null, null, '</span>');
                $versions[] = & $this->createElement('static', null, null, $version['html']);

                $this->addGroup($versions, null, null, "\n");
                $i ++;
            }

            $buttons[] = $this->createElement(
                'style_submit_button',
                'submit',
                Translation :: get('CompareVersions'),
                array('class' => 'normal compare'));
            $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        }
    }

    private function build_basic_form($htmleditor_options = array())
    {
        $this->addElement('html', '<div id="message"></div>');
        $this->addElement(
            'hidden',
            ContentObject :: PROPERTY_TEMPLATE_REGISTRATION_ID,
            $this->get_content_object()->get_template_registration_id());
        $this->add_textfield(
            ContentObject :: PROPERTY_TITLE,
            Translation :: get('Title', array(), ClassnameUtilities :: getInstance()->getNamespaceFromObject($this)),
            true,
            array('size' => '100', 'id' => 'title', 'style' => 'width: 95%'));

        if ($this->allows_category_selection())
        {
            $category_group = array();
            $category_group[] = $this->createElement(
                'select',
                ContentObject :: PROPERTY_PARENT_ID,
                Translation :: get('CategoryTypeName'),
                $this->get_categories());
            // $select->setSelected($this->content_object->get_parent_id());
            $category_group[] = $this->createElement(
                'image',
                'add_category',
                Theme :: getInstance()->getCommonImagePath('Action/Add'),
                array('id' => 'add_category', 'style' => 'display:none'));
            $this->addGroup($category_group, null, Translation :: get('CategoryTypeName'));

            $group = array();
            $group[] = $this->createElement('static', null, null, '<div id="' . self :: NEW_CATEGORY . '">');
            $group[] = $this->createElement('static', null, null, Translation :: get('AddNewCategory'));
            $group[] = $this->createElement('text', self :: NEW_CATEGORY);
            $group[] = $this->createElement('static', null, null, '</div>');
            $this->addGroup($group);
        }

        // $this->add_tags_input();

        $value = PlatformSetting :: get('description_required', Manager :: context());
        $required = ($value == 1) ? true : false;
        $name = Translation :: get(
            'Description',
            array(),
            ClassnameUtilities :: getInstance()->getNamespaceFromObject($this));
        $this->add_html_editor(ContentObject :: PROPERTY_DESCRIPTION, $name, $required, $htmleditor_options);
    }

    /**
     * Adds the input field for the content object tags
     */
    protected function add_tags_input()
    {
        $tags = DataManager :: retrieve_content_object_tags_for_user(Session :: get_user_id());

        if ($this->content_object->is_identified())
        {
            $default_tags = DataManager :: retrieve_content_object_tags_for_content_object(
                $this->content_object->get_id());
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
        $namespace = ClassnameUtilities :: getInstance()->getNamespaceFromClassname($type);
        $name = Translation :: get('TypeName', array(), $namespace);
        $img = '<img src="' . $content_object->get_icon_path(Theme :: ICON_MINI) . '" title="' . htmlentities($name) .
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
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository', true) . 'ContentObjectFormUpload.js'));
        // added platform option 'omit_content_object_title_check'
        // when NULL (platform option not set) or FALSE (platform option set to false)
        // check title duplicates of content objects; when it is both set and true,
        // omit this check. (this way, the platform setting is unobtrusive).
        if (PlatformSetting :: get('omit_content_object_title_check', __NAMESPACE__) != 1)
        {
            $this->addElement(
                'html',
                ResourceManager :: get_instance()->get_resource_html(
                    Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository', true) .
                         'ContentObjectFormCheck.js'));
        }

        $buttons = array();

        // should not call your button submit as it is a function on the
        // javascrip file
        switch ($this->form_type)
        {
            case self :: TYPE_COMPARE :
                $buttons[] = $this->createElement(
                    'style_submit_button',
                    'submit_button',
                    Translation :: get('Compare', null, Utilities :: COMMON_LIBRARIES),
                    array('class' => 'normal compare'));
                break;
            case self :: TYPE_CREATE :
                $buttons[] = $this->createElement(
                    'style_submit_button',
                    'submit_button',
                    Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES),
                    array('class' => 'positive'));
                break;
            case self :: TYPE_EDIT :
                $buttons[] = $this->createElement(
                    'style_submit_button',
                    'submit_button',
                    Translation :: get('Update', null, Utilities :: COMMON_LIBRARIES),
                    array('class' => 'positive update'));
                break;
            case self :: TYPE_REPLY :
                $buttons[] = $this->createElement(
                    'style_submit_button',
                    'submit_button',
                    Translation :: get('Reply', null, Utilities :: COMMON_LIBRARIES),
                    array('class' => 'positive send'));
                break;
            default :
                $buttons[] = $this->createElement(
                    'style_submit_button',
                    'submit_button',
                    Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES),
                    array('class' => 'positive'));
                break;
        }

        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal empty'));
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

            $html[] = '<script type="text/javascript">';
            $html[] = 'var support_attachments = true';
            $html[] = '</script>';
            $this->addElement('html', implode(PHP_EOL, $html));
            if ($this->form_type != self :: TYPE_REPLY)
            {
                $attached_objects = $object->get_attachments();
                $attachments = Utilities :: content_objects_for_element_finder($attached_objects);
            }
            else
            {
                $attachments = array();
            }

            $url = Path :: getInstance()->getBasePath(true) .
                 'index.php?application=Chamilo\\Core\\Repository\\Ajax&go=xml_feed';
            $locale = array();
            $locale['Display'] = Translation :: get('AddAttachments');
            $locale['Searching'] = Translation :: get('Searching', null, Utilities :: COMMON_LIBRARIES);
            $locale['NoResults'] = Translation :: get('NoResults', null, Utilities :: COMMON_LIBRARIES);
            $locale['Error'] = Translation :: get('Error', null, Utilities :: COMMON_LIBRARIES);
            $hidden = true;

            $this->addElement(
                'html',
                ResourceManager :: get_instance()->get_resource_html(
                    Path :: getInstance()->getJavascriptPath('Chamilo\Libraries', true) .
                         'Plugin/Uploadify/jquery.uploadify.min.js'));
            $this->addElement(
                'html',
                ResourceManager :: get_instance()->get_resource_html(
                    Path :: getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'CollapseHorizontal.js'));

            $this->addElement(
                'category',
                '<a href="#">' . Translation :: get('Attachments') . '</a>',
                'content_object_attachments collapsible collapsed');

            $this->addElement('static', 'uploadify', Translation :: get('UploadDocument'), '<div id="uploadify"></div>');
            $elem = $this->addElement(
                'element_finder',
                'attachments',
                Translation :: get('SelectAttachment'),
                $url,
                $locale,
                $attachments);
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
                $this->addElement('category', Translation :: get('AdditionalProperties'));
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
        $defaults[ContentObject :: PROPERTY_ID] = $content_object->get_id();
        $defaults[ContentObject :: PROPERTY_MODIFICATION_DATE] = $content_object->get_modification_date();
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $content_object->get_parent_id();
        $defaults[ContentObject :: PROPERTY_TEMPLATE_REGISTRATION_ID] = $content_object->get_template_registration_id();

        if ($this->form_type == self :: TYPE_REPLY)
        {
            $defaults[ContentObject :: PROPERTY_TITLE] = Translation :: get(
                'ReplyShort',
                null,
                Utilities :: COMMON_LIBRARIES) . ' ' . $content_object->get_title();
        }
        else
        {
            $defaults[ContentObject :: PROPERTY_TITLE] = $defaults[ContentObject :: PROPERTY_TITLE] == null ? $content_object->get_title() : $defaults[ContentObject :: PROPERTY_TITLE];
            $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $content_object->get_description();
        }

        if ($content_object instanceof ForcedVersionSupport && $this->form_type == self :: TYPE_EDIT)
        {
            $defaults['version'] = 1;
        }

        parent :: setDefaults($defaults);
    }

    public function setParentDefaults($defaults)
    {
        parent :: setDefaults($defaults);
    }

    public function set_values($defaults)
    {
        parent :: setDefaults($defaults);
    }

    public function create_content_object()
    {
        $values = $this->exportValues();

        $object = $this->content_object;
        $object->set_owner_id($this->get_owner_id());
        $object->set_template_registration_id(
            $values[ContentObject :: PROPERTY_TEMPLATE_REGISTRATION_ID] ? $values[ContentObject :: PROPERTY_TEMPLATE_REGISTRATION_ID] : null);
        $object->set_title($values[ContentObject :: PROPERTY_TITLE]);
        $desc = $values[ContentObject :: PROPERTY_DESCRIPTION] ? $values[ContentObject :: PROPERTY_DESCRIPTION] : '';
        $object->set_description($desc);
        if ($this->allows_category_selection())
        {
            $this->set_category_from_values($object, $values);
        }

        $object->create();

        if ($object->has_errors())
        {
            return null;
        }

        $tags = explode(',', $values[TagsFormBuilder :: PROPERTY_TAGS]);
        DataManager :: set_tags_for_content_objects($tags, array($object->get_id()), Session :: get_user_id());

        $values = $this->exportValues();

        $metadata_form_handler = new ElementValueEditorFormHandler(
            new ContentObjectMetadataValueCreator($this->content_object));
        $metadata_form_handler->handle_form($values);

        // Process includes
        ContentObjectIncludeParser :: parse_includes($this);

        // Process attachments
        if ($object instanceof AttachmentSupport)
        {
            $object->attach_content_objects($values['attachments']['lo'], ContentObject :: ATTACHMENT_NORMAL);
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
        $parent_id = $values[ContentObject :: PROPERTY_PARENT_ID];
        $new_category_name = $values[self :: NEW_CATEGORY];

        if (! StringUtilities :: getInstance()->isNullOrEmpty($new_category_name, true))
        {
            $new_category = $this->create_new_category($new_category_name, $parent_id);
            if ($new_category)
            {
                $parent_id = $new_category->get_id();
            }
        }

        $object->set_parent_id($parent_id);
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
        $new_category = new RepositoryCategory();
        $new_category->set_name($category_name);
        $new_category->set_parent($parent_id);
        $new_category->set_user_id($this->get_owner_id());
        $new_category->set_type(RepositoryCategory :: TYPE_NORMAL);

        if (! $new_category->create())
        {
            return null;
        }
        else
        {
            return $new_category;
        }
    }

    public function update_content_object()
    {
        $object = $this->content_object;
        $values = $this->exportValues();

        $object->set_title($values[ContentObject :: PROPERTY_TITLE]);

        $desc = $values[ContentObject :: PROPERTY_DESCRIPTION] ? $values[ContentObject :: PROPERTY_DESCRIPTION] : '';
        $object->set_description($desc ? $desc : '');

        $move = false;
        if ($this->allows_category_selection())
        {
            $old_parent_id = $object->get_parent_id();
            $this->set_category_from_values($object, $values);

            if ($old_parent_id != $object->get_parent_id())
            {
                if ($object->move_allowed($object->get_parent_id()))
                {
                    $move = true;
                }
                else
                {
                    $object->set_parent_id($old_parent_id);

                    /*
                     * TODO: Make this more meaningful, e.g. by returning error constants instead of booleans, like
                     * ContentObjectForm :: SUCCESS (not implemented).
                     */

                    return self :: RESULT_ERROR;
                }
            }
        }

        if (isset($values['version']) && $values['version'] == 1)
        {
            $object->set_comment(nl2br($values[ContentObject :: PROPERTY_COMMENT]));
            $result = $object->version();

            $versions = DataManager :: retrieve_content_object_versions($object);
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

            if ($move)
            {
                $object->move($object->get_parent_id());
            }
        }

        if ($object->has_errors())
        {
            return false;
        }

        $tags = explode(',', $values[TagsFormBuilder :: PROPERTY_TAGS]);
        DataManager :: set_tags_for_content_objects($tags, array($object->get_id()), Session :: get_user_id());

        \Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Storage\DataManager :: truncate_metadata_values_for_content_object(
            $object->get_id());

        $metadata_form_handler = new ElementValueEditorFormHandler(
            new ContentObjectMetadataValueCreator($this->content_object));
        $metadata_form_handler->handle_form($values);

        // Process includes
        ContentObjectIncludeParser :: parse_includes($this);

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
                $object->detach_content_object($attached_object_id->get_id(), ContentObject :: ATTACHMENT_NORMAL);
            }
            $object->attach_content_objects($values['attachments']['lo'], ContentObject :: ATTACHMENT_NORMAL);
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
        return ($this->form_type == self :: TYPE_CREATE || $this->form_type == self :: TYPE_REPLY ||
             $this->form_type == self :: TYPE_EDIT) && Session :: get_user_id() == $this->get_owner_id();
    }

    /**
     * Creates a form object to manage an content object.
     *
     * @param $form_type int The form type; either ContentObjectForm :: TYPE_CREATE or ContentObjectForm :: TYPE_EDIT.
     * @param $content_object ContentObject The object to create or update.
     * @param $form_name string The name to use in the form tag.
     * @param $method string The method to use ('post' or 'get').
     * @param $action string The URL to which the form should be submitted.
     * @return ContentObjectForm
     */
    public static function factory($form_type, $content_object, $form_name, $method = 'post', $action = null, $extra = null,
        $additional_elements = array(), $allow_new_version = true, $form_variant = null)
    {
        $type = $content_object->get_type();

        $base_class_name = $content_object->package() . '\Form\\' . $content_object->class_name(false);

        if ($form_variant)
        {
            $class = $base_class_name . StringUtilities :: getInstance()->createString($form_variant)->upperCamelize() .
                 'Form';
        }
        else
        {
            $class = $base_class_name . 'Form';
        }

        return new $class(
            $form_type,
            $content_object,
            $form_name,
            $method,
            $action,
            $extra,
            $additional_elements,
            $allow_new_version);
    }

    /**
     * Validates this form
     *
     * @see FormValidator::validate
     */
    public function validate()
    {
        if ($this->isSubmitted() && $this->form_type == self :: TYPE_COMPARE)
        {
            $values = $this->exportValues();
            if (! isset($values['object']) || ! isset($values['compare']))
            {
                return false;
            }
        }

        return parent :: validate();
    }

    /**
     * Adds an example box
     */
    protected function add_example_box()
    {
        $this->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath('Chamilo\Libraries', true) . 'CollapseHorizontal.js'));

        $this->addElement(
            'category',
            '<a href="#">' . Translation :: get('Instructions', null, Utilities :: COMMON_LIBRARIES) . '</a>',
            'content_object_attachments collapsible collapsed');

        $this->addElement(
            'html',
            '<div>' . Translation :: get(
                'InstructionsText',
                null,
                ClassnameUtilities :: getInstance()->getNamespaceFromClassname(get_class($this))) . '</div>');

        $this->addElement('category');
    }
}
