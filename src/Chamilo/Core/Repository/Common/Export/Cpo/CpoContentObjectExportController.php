<?php
namespace Chamilo\Core\Repository\Common\Export\Cpo;

use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\Common\Export\ContentObjectExportController;
use Chamilo\Core\Repository\Common\Export\ContentObjectExportImplementation;
use Chamilo\Core\Repository\Common\Export\ExportParameters;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\ContentObjectAttachment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObjectInclude;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\File\Compression\Filecompression;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use DOMDocument;
use DOMXPath;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository;

class CpoContentObjectExportController extends ContentObjectExportController
{
    const NODE_TYPE_SUB_ITEMS = 'sub_items';
    const NODE_TYPE_ATTACHMENTS = 'attachments';
    const NODE_TYPE_INCLUDES = 'includes';

    /**
     *
     * @var DOMDocument
     */
    private $dom_document;

    /**
     *
     * @var DOMXPath
     */
    private $dom_xpath;

    /**
     *
     * @var string
     */
    private $temporary_directory;

    /**
     *
     * @var multitype:integer
     */
    private $id_cache;

    /**
     *
     * @var multitype:integer
     */
    private $category_id_cache;

    /**
     *
     * @param $parameters ExportParameters
     */
    public function __construct(ExportParameters $parameters)
    {
        parent :: __construct($parameters);

        $this->dom_document = new DOMDocument('1.0', 'UTF-8');
        $this->dom_document->formatOutput = true;
        $this->dom_xpath = new DOMXPath($this->dom_document);
        $this->prepare_file_system();
    }

    /**
     *
     * @return DOMDocument
     */
    public function get_dom_document()
    {
        return $this->dom_document;
    }

    /**
     *
     * @param $dom_document DOMDocument
     */
    public function set_dom_document($dom_document)
    {
        $this->dom_document = $dom_document;
    }

    /**
     *
     * @return DOMXPath
     */
    public function get_dom_xpath()
    {
        return $this->dom_xpath;
    }

    /**
     *
     * @param $dom_xpath DOMXPath
     */
    public function set_dom_xpath($dom_xpath)
    {
        $this->dom_xpath = $dom_xpath;
    }

    public function get_id_cache()
    {
        return $this->id_cache;
    }

    public function set_id_cache($id)
    {
        $this->id_cache[] = $id;
    }

    public function in_id_cache($id)
    {
        return in_array($id, $this->id_cache);
    }

    public function get_category_id_cache()
    {
        return $this->category_id_cache;
    }

    public function set_category_id_cache($category_id)
    {
        $this->category_id_cache[] = $category_id;
    }

    public function in_category_id_cache($category_id)
    {
        return in_array($category_id, $this->category_id_cache);
    }

    public function prepare_file_system()
    {
        $user_id = Session :: get_user_id();

        $this->temporary_directory = Path :: getInstance()->getTemporaryPath() . md5($user_id . '_export') . '/';
        if (! is_dir($this->temporary_directory))
        {
            mkdir($this->temporary_directory, 0777, true);
        }
    }

    /**
     *
     * @return DOMElement
     */
    public function get_export_node()
    {
        $node_list = $this->dom_xpath->query('/export');

        if ($node_list->length == 1)
        {
            return $node_list->item(0);
        }
        else
        {
            return $this->dom_document->appendChild($this->dom_document->createElement('export'));
        }
    }

    /**
     *
     * @return DOMElement
     */
    public function get_content_objects_node()
    {
        $node_list = $this->dom_xpath->query('/export/content_objects');

        if ($node_list->length == 1)
        {
            return $node_list->item(0);
        }
        else
        {
            return $this->get_export_node()->appendChild($this->dom_document->createElement('content_objects'));
        }
    }

    /**
     *
     * @param $content_object_id integer
     * @return DOMElement
     */
    public function get_category_node($category_id)
    {
        $node_list = $this->dom_xpath->query('//category[@id="' . $category_id . '"]');

        if ($node_list->length == 1)
        {
            return $node_list->item(0);
        }
        else
        {
            $content_object_node = $this->get_categories_node()->appendChild(
                $this->dom_document->createElement('category'));
            $content_object_node->appendChild($this->dom_document->createAttribute('id'))->appendChild(
                $this->dom_document->createTextNode($category_id));

            return $content_object_node;
        }
    }

    /**
     *
     * @return DOMElement
     */
    public function get_categories_node()
    {
        $node_list = $this->dom_xpath->query('/export/categories');

        if ($node_list->length == 1)
        {
            return $node_list->item(0);
        }
        else
        {
            return $this->get_export_node()->appendChild($this->dom_document->createElement('categories'));
        }
    }

    public function get_external_instances_node()
    {
        $node_list = $this->dom_xpath->query('/export/external_instances');

        if ($node_list->length == 1)
        {
            return $node_list->item(0);
        }
        else
        {
            return $this->get_export_node()->appendChild($this->dom_document->createElement('external_instances'));
        }
    }

    /**
     *
     * @param $content_object_id integer
     * @return DOMElement
     */
    public function get_content_object_node($content_object_id)
    {
        $node_list = $this->dom_xpath->query('//content_object[@id="' . $content_object_id . '"]');

        if ($node_list->length == 1)
        {
            return $node_list->item(0);
        }
        else
        {
            $content_object_node = $this->get_content_objects_node()->appendChild(
                $this->dom_document->createElement('content_object'));
            $content_object_node->appendChild($this->dom_document->createAttribute('id'))->appendChild(
                $this->dom_document->createTextNode($content_object_id));

            return $content_object_node;
        }
    }

    public function has_external_instance_node($external_instance_id)
    {
        $node_list = $this->dom_xpath->query('//external_instance[@id="' . $external_instance_id . '"]');

        if ($node_list->length == 1)
        {
            return $node_list->item(0);
        }
        else
        {
            return false;
        }
    }

    public function get_external_instance_node($external_instance_id)
    {
        $external_instance_node = $this->has_external_instance_node($external_instance_id);
        if (! $external_instance_node)
        {
            $external_instance_node = $this->get_export_node()->appendChild(
                $this->dom_document->createElement('external_instance'));
            $external_instance_node->appendChild($this->dom_document->createAttribute('id'))->appendChild(
                $this->dom_document->createTextNode($external_instance_id));
            return $external_instance_node;
        }
        return $external_instance_node;
    }

    /**
     *
     * @param $content_object_id integer
     * @return DOMElement
     */
    public function get_content_object_sub_items_node($content_object_id)
    {
        return $this->get_content_object_sub_node(self :: NODE_TYPE_SUB_ITEMS, $content_object_id);
    }

    /**
     *
     * @param $content_object_id integer
     * @return DOMElement
     */
    public function get_content_object_attachments_node($content_object_id)
    {
        return $this->get_content_object_sub_node(self :: NODE_TYPE_ATTACHMENTS, $content_object_id);
    }

    /**
     *
     * @param $content_object_id integer
     * @return DOMElement
     */
    public function get_content_object_includes_node($content_object_id)
    {
        return $this->get_content_object_sub_node(self :: NODE_TYPE_INCLUDES, $content_object_id);
    }

    /**
     *
     * @param $type string
     * @param $content_object_id integer
     * @return DOMElement
     */
    public function get_content_object_sub_node($type, $content_object_id)
    {
        $node_list = $this->dom_xpath->query('//content_object[@id="' . $content_object_id . '"]/' . $type);

        if ($node_list->length == 1)
        {
            return $node_list->item(0);
        }
        else
        {
            return $this->get_content_object_node($content_object_id)->appendChild(
                $this->dom_document->createElement($type));
        }
    }

    public function run()
    {
        $content_object_ids = $this->get_parameters()->get_content_object_ids();

        if (count($content_object_ids) > 0)
        {
            $condition = new InCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID),
                $content_object_ids,
                ContentObject :: get_table_name());
        }
        else
        {
            $condition = null;
        }

        $parameters = new DataClassRetrievesParameters($condition);
        $content_objects = DataManager :: retrieve_active_content_objects(ContentObject :: class_name(), $parameters);

        while ($content_object = $content_objects->next_result())
        {
            if (! $this->in_id_cache($content_object->get_id()))
            {
                $this->process($content_object);
            }
        }

        if ($this->get_parameters()->has_categories())
        {
            $this->process_categories();
        }

        $this->dom_document->save($this->get_xml_path());
        return $this->zip();
    }

    public function zip()
    {
        $zip = Filecompression :: factory();
        $zip_path = $zip->create_archive($this->temporary_directory);
        Filesystem :: remove($this->temporary_directory);
        return $zip_path;
    }

    public function get_xml_path()
    {
        return $this->temporary_directory . 'content_object.xml';
    }

    /**
     *
     * @param $content_object ContentObject
     */
    public function process(ContentObject $content_object)
    {
        // Export of the actual object itself to CPO XML
        ContentObjectExportImplementation :: launch(
            $this,
            $content_object,
            ContentObjectExport :: FORMAT_CPO,
            $this->get_parameters()->get_type());

        $this->set_id_cache($content_object->get_id());

        if ($this->get_parameters()->has_categories() &&
             $content_object->get_owner_id() == $this->get_parameters()->get_user())
        {
            if ($this->get_parameters()->getWorkspace() instanceof PersonalWorkspace)
            {
                $parent_id = $content_object->get_parent_id();
            }
            else
            {
                $contentObjectRelationService = new ContentObjectRelationService(new ContentObjectRelationRepository());
                $contentObjectRelation = $contentObjectRelationService->getContentObjectRelationForWorkspaceAndContentObject(
                    $this->get_parameters()->getWorkspace(),
                    $content_object);

                $parent_id = $contentObjectRelation->getCategoryId();
            }

            if (! $this->in_category_id_cache($parent_id))
            {
                $this->process_category($parent_id);
            }
        }

        if ($content_object->has_versions())
        {
            $content_object_versions = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object_versions(
                $content_object);

            while ($content_object_version = $content_object_versions->next_result())
            {
                if (! $this->in_id_cache($content_object_version->get_id()))
                {
                    $this->process($content_object_version);
                }
            }
        }

        $this->process_helpers($content_object);
        $this->process_complex_children($content_object);
        $this->process_attachments($content_object);
        $this->process_includes($content_object);
    }

    /**
     *
     * @param $content_object ContentObject
     */
    public function process_helpers($helper_object)
    {
        if (in_array($helper_object->get_type(), DataManager :: get_active_helper_types()))
        {
            $content_object = $helper_object->get_reference_object();
            if ($content_object instanceof ContentObject)
            {
                if (! $this->in_id_cache($content_object->get_id()))
                {
                    $this->process($content_object);
                }
            }
        }
    }

    /**
     *
     * @param $source string
     * @param $destination string
     */
    public function add_files($source, $destination)
    {
        Filesystem :: recurse_copy($source, $this->temporary_directory . $destination, true);
    }

    /**
     *
     * @param $content_object ContentObject
     */
    public function process_complex_children(ContentObject $content_object)
    {
        if ($content_object instanceof ComplexContentObjectSupport)
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem :: class_name(),
                    ComplexContentObjectItem :: PROPERTY_PARENT),
                new StaticConditionVariable($content_object->get_id()),
                ComplexContentObjectItem :: get_table_name());
            $children = DataManager :: retrieve_complex_content_object_items(
                ComplexContentObjectItem :: class_name(),
                $condition);

            if ($children->size() > 0)
            {
                while ($child = $children->next_result())
                {
                    $this->add_content_object_sub_item($child);
                    if (! $this->in_id_cache($child->get_ref_object()->get_id()))
                    {
                        $this->process($child->get_ref_object());
                    }
                }
            }
        }
    }

    /**
     *
     * @param $child ComplexContentObjectItem
     */
    public function add_content_object_sub_item(ComplexContentObjectItem $child)
    {
        $sub_items_node = $this->get_content_object_sub_items_node($child->get_parent());

        $sub_item = $sub_items_node->appendChild($this->dom_document->createElement('sub_item'));
        $id_ref = $sub_item->appendChild($this->dom_document->createAttribute('idref'));
        $id_ref->appendChild($this->dom_document->createTextNode($child->get_ref()));
        $id = $sub_item->appendChild($this->dom_document->createAttribute('id'));
        $id->appendChild($this->dom_document->createTextNode($child->get_id()));

        foreach ($child->get_additional_properties() as $property_name => $property_value)
        {
            $attribute = $sub_item->appendChild($this->dom_document->createAttribute($property_name));
            $attribute->appendChild($this->dom_document->createTextNode($property_value));
        }
    }

    /**
     *
     * @param $content_object ContentObject
     */
    public function process_attachments(ContentObject $content_object)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAttachment :: class_name(),
                ContentObjectAttachment :: PROPERTY_CONTENT_OBJECT_ID),
            new StaticConditionVariable($content_object->get_id()));
        $content_object_attachments = \Chamilo\Core\Repository\Storage\DataManager :: retrieves(
            ContentObjectAttachment :: class_name(),
            $condition);

        if ($content_object_attachments->size() > 0)
        {
            while ($content_object_attachment = $content_object_attachments->next_result())
            {
                $this->add_content_object_attachment($content_object_attachment);
                if (! $this->in_id_cache($content_object_attachment->get_attachment_object()->get_id()))
                {
                    $this->process($content_object_attachment->get_attachment_object());
                }
            }
        }
    }

    /**
     *
     * @param $content_object_attachment ContentObjectAttachment
     */
    public function add_content_object_attachment(ContentObjectAttachment $content_object_attachment)
    {
        $attachments_node = $this->get_content_object_attachments_node(
            $content_object_attachment->get_content_object_id());

        $attachment = $attachments_node->appendChild($this->dom_document->createElement('attachment'));
        $type = $attachment->appendChild($this->dom_document->createAttribute('type'));
        $type->appendChild($this->dom_document->createTextNode($content_object_attachment->get_type()));
        $id_ref = $attachment->appendChild($this->dom_document->createAttribute('idref'));
        $id_ref->appendChild($this->dom_document->createTextNode($content_object_attachment->get_attachment_id()));
    }

    /**
     *
     * @param $content_object ContentObject
     */
    public function process_includes(ContentObject $content_object)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectInclude :: class_name(),
                ContentObjectInclude :: PROPERTY_CONTENT_OBJECT_ID),
            new StaticConditionVariable($content_object->get_id()));
        $content_object_includes = \Chamilo\Core\Repository\Storage\DataManager :: retrieves(
            ContentObjectInclude :: class_name(),
            $condition);

        if ($content_object_includes->size() > 0)
        {
            while ($content_object_include = $content_object_includes->next_result())
            {
                $this->add_content_object_include($content_object, $content_object_include->get_include_object());
                if (! $this->in_id_cache($content_object_include->get_include_object()->get_id()))
                {
                    $this->process($content_object_include->get_include_object());
                }
            }
        }
    }

    public function process_external_instance($external_instance_id)
    {
        $external_instance = \Chamilo\Core\Repository\Instance\Storage\DataManager :: retrieve_by_id(
            \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance :: class_name(),
            $external_instance_id);

        if (! $this->has_external_instance_node($external_instance_id))
        {
            $external_instance_node = $this->get_external_instance_node($external_instance_id);

            $id = $external_instance_node->appendChild($this->dom_document->createAttribute('id'));
            $id->appendChild($this->dom_document->createTextNode($external_instance_id));

            $type = $external_instance_node->appendChild($this->dom_document->createAttribute('type'));
            $type->appendChild($this->dom_document->createTextNode($external_instance->get_type()));

            $settings_node = $external_instance_node->appendChild($this->dom_document->createElement('settings'));

            $settings = \Chamilo\Core\Repository\Instance\Manager :: get_instance_identifier(
                $external_instance->get_implementation());

            foreach ($settings as $setting)
            {
                $setting = $external_instance->get_setting($setting);
                $setting_node = $settings_node->appendChild($this->dom_document->createElement('setting'));

                $variable = $setting_node->appendChild($this->dom_document->createElement('variable'));
                $variable->appendChild($this->dom_document->createTextNode($setting->get_variable()));

                $value = $setting_node->appendChild($this->dom_document->createElement('value'));
                $value->appendChild($this->dom_document->createTextNode($setting->get_value()));
            }
        }
    }

    /**
     *
     * @param $included_content_object ContentObject
     */
    public function add_content_object_include(ContentObject $content_object, ContentObject $included_content_object)
    {
        $includes_node = $this->get_content_object_includes_node($content_object->get_id());

        $include = $includes_node->appendChild($this->dom_document->createElement('include'));
        $id_ref = $include->appendChild($this->dom_document->createAttribute('idref'));
        $id_ref->appendChild($this->dom_document->createTextNode($included_content_object->get_id()));
    }

    /*
     * (non-PHPdoc) @see repository.ContentObjectExportController::get_filename()
     */
    public function get_filename()
    {
        return 'content_objects.cpo';
    }

    public function process_category($category_id)
    {
        if ($category_id != 0)
        {
            $category = DataManager :: retrieve_by_id(RepositoryCategory :: class_name(), $category_id);
            $this->set_category_id_cache($category->get_id());

            $category_node = $this->get_category_node($category_id);

            $export_properties = array(RepositoryCategory :: PROPERTY_PARENT, RepositoryCategory :: PROPERTY_NAME);

            foreach ($export_properties as $property)
            {
                $dom_property = $this->get_dom_document()->createElement($property);
                $category_node->appendChild($dom_property);
                $dom_property->appendChild(
                    $this->get_dom_document()->createTextNode($category->get_default_property($property)));
            }
        }
    }

    public function process_categories()
    {
        $cache_category_ids = $this->get_category_id_cache();

        if (count($cache_category_ids) > 1)
        {

            $parents = array();
            if ($cache_category_ids == 0)
            {
                $previous = array(0);
            }
            else
            {
                $category = DataManager :: retrieve_by_id(
                    RepositoryCategory :: class_name(),
                    array_shift($cache_category_ids));
                $previous = $category->get_parent_ids();
            }

            foreach ($cache_category_ids as $cache_category_id)
            {
                $parents[$cache_category_id] = $previous;
                $category = DataManager :: retrieve_by_id(RepositoryCategory :: class_name(), $cache_category_id);
                $parents[$cache_category_id] = $category->get_parent_ids();
                $previous = array_intersect($previous, $parents[$cache_category_id]);
            }

            $root = array_pop($previous);

            if (! $this->in_category_id_cache($root))
            {
                $this->process_category($root);
            }

            foreach ($parents as $cache_category_id => $parent_ids)
            {
                $add = false;
                foreach ($parent_ids as $parent_id)
                {
                    if ($parent_id == $root && ! $add)
                    {
                        $add = true;
                    }

                    if ($add && $parent_id != $root)
                    {

                        if (! $this->in_category_id_cache($parent_id))
                        {
                            $this->process_category($parent_id);
                        }
                    }
                }
            }
        }
        else
        {
            $root = $cache_category_ids[0];
        }
        if ($root != 0)
        {
            $root_node = $this->get_category_node($root);
            $parent_node = $this->dom_xpath->query('parent_id', $root_node)->item(0);
            $new_parent_node = $this->get_dom_document()->createElement('parent_id');
            $new_parent_node->appendChild($this->get_dom_document()->createTextNode(0));
            $root_node->replaceChild($new_parent_node, $parent_node);
        }
    }
}
