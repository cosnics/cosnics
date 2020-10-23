<?php
namespace Chamilo\Core\Repository\Common\Import\Cpo;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Common\Import\ContentObjectImportController;
use Chamilo\Core\Repository\Common\Import\ContentObjectImportImplementation;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\ContentObjectAttachment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObjectInclude;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\File\Compression\Filecompression;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use DOMDocument;
use DOMXPath;
use InvalidArgumentException;

class CpoContentObjectImportController extends ContentObjectImportController
{
    const NODE_TYPE_SUB_ITEMS = 'sub_items';
    const NODE_TYPE_ATTACHMENTS = 'attachments';
    const NODE_TYPE_INCLUDES = 'includes';
    const FORMAT = 'cpo';

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
     * @var multitype:multitype
     */
    private $id_cache;

    /**
     *
     * @var multitype:boolean
     */
    private $object_number_created;

    /**
     *
     * @var multitype:multitype
     */
    private $version;

    /**
     *
     * @param $content_object_ids multitype:integer
     */
    public function __construct($parameters)
    {
        parent::__construct($parameters);
        
        $this->dom_document = new DOMDocument('1.0', 'UTF-8');
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

    public function set_cache_id($classname, $property, $old_id, $new_id)
    {
        $this->id_cache[$classname][$property][$old_id] = $new_id;
    }

    public function set_content_object_id_cache_id($old_id, $new_id)
    {
        $this->set_cache_id(ContentObject::class, ContentObject::PROPERTY_ID, $old_id, $new_id);
    }

    public function set_category_id_cache_id($old_id, $new_id)
    {
        $this->set_cache_id(RepositoryCategory::class, RepositoryCategory::PROPERTY_ID, $old_id, $new_id);
    }

    public function set_content_object_object_number_cache_id($old_object_number, $new_object_number)
    {
        $this->set_cache_id(
            ContentObject::class,
            ContentObject::PROPERTY_OBJECT_NUMBER, 
            $old_object_number, 
            $new_object_number);
    }

    public function set_complex_content_object_item_id_cache_id($old_item_id, $new_item_id)
    {
        $this->set_cache_id(
            ComplexContentObjectItem::class,
            ComplexContentObjectItem::PROPERTY_ID, 
            $old_item_id, 
            $new_item_id);
    }

    public function set_external_instance_id_cache_id($old_external_instance_id, $new_external_instance_id)
    {
        $this->set_cache_id(
            Instance::class,
            Instance::PROPERTY_ID,
            $old_external_instance_id, 
            $new_external_instance_id);
    }

    public function get_cache_id($classname, $property, $old_id)
    {
        return $this->id_cache[$classname][$property][$old_id];
    }

    public function get_cache()
    {
        return $this->id_cache;
    }

    public function get_cache_ids($classname, $property)
    {
        return $this->id_cache[$classname][$property];
    }

    public function get_content_object_id_cache_id($old_id)
    {
        return $this->get_cache_id(ContentObject::class, ContentObject::PROPERTY_ID, $old_id);
    }

    public function get_category_id_cache_id($old_id)
    {
        $cachedCategory = $this->get_cache_id(RepositoryCategory::class, RepositoryCategory::PROPERTY_ID, $old_id);
        return empty($cachedCategory) ? 0 : $cachedCategory;
    }

    public function get_content_object_object_number_cache_id($old_object_number)
    {
        return $this->get_cache_id(
            ContentObject::class,
            ContentObject::PROPERTY_OBJECT_NUMBER, 
            $old_object_number);
    }

    public function get_complex_content_object_item_id_cache_id($old_item_id)
    {
        return $this->get_cache_id(
            ComplexContentObjectItem::class,
            ComplexContentObjectItem::PROPERTY_ID, 
            $old_item_id);
    }

    public function get_external_instance_id_cache_id($old_external_instance_id)
    {
        return $this->get_cache_id(
            Instance::class,
            Instance::PROPERTY_ID,
            $old_external_instance_id);
    }

    public function get_object_number_created($id)
    {
        return $this->object_number_created[$id];
    }

    public function set_object_number_created($id)
    {
        $this->object_number_created[$id] = true;
    }

    public function get_version($id)
    {
        return $this->version[$id];
    }

    public function set_version($object_number, $creation_date)
    {
        $this->version[$object_number] = $creation_date;
    }

    public function run()
    {
        if(empty($this->get_parameters()->get_file()))
        {
            throw new NoObjectSelectedException(Translation::get('FileName', null, Utilities::COMMON_LIBRARIES));
        }

        if (in_array($this->get_parameters()->get_file()->get_extension(), self::get_allowed_extensions()))
        {
            $this->temporary_directory = $this->unzip();
            $xml_path = $this->temporary_directory . '/content_object.xml';
            if (file_exists($xml_path))
            {
                $this->dom_document->load($xml_path);
                $this->dom_xpath = new DOMXPath($this->dom_document);
                $this->process_categories();
                $content_object_list = $this->dom_xpath->query('/export/content_objects/content_object');
                foreach ($content_object_list as $content_object_node)
                {
                    if (! $this->get_content_object_id_cache_id($content_object_node->getAttribute('id')))
                    {
                        $this->process_content_object($content_object_node);
                    }
                }
                $this->add_message(Translation::get('ObjectImported'), self::TYPE_CONFIRM);
            }
            else
            {
                $this->add_message(Translation::get('NoCpoFile'), self::TYPE_WARNING);
            }
            
            Filesystem::remove($this->temporary_directory);
        }
        else
        {
            $this->add_message(
                Translation::get(
                    'UnsupportedFileFormat', 
                    array('TYPES' => implode(', ', self::get_allowed_extensions()))), 
                self::TYPE_ERROR);
        }
        
        return $this->get_cache_ids(ContentObject::class, ContentObject::PROPERTY_ID);
    }

    public function process_categories()
    {
        $root_categories = $this->dom_xpath->query('//category[parent_id=0]');
        foreach ($root_categories as $root_category)
        {
            $this->process_category($root_category, $this->get_parameters()->get_category());
        }
    }

    public function process_category($node, $parent_id)
    {
        $category = new RepositoryCategory();
        $base_name = $this->dom_xpath->query('name', $node)->item(0)->nodeValue;
        $category->set_name(
            DataManager::create_unique_category_name($this->get_parameters()->getWorkspace(), $parent_id, $base_name));
        $category->set_parent($parent_id);
        $category->set_type_id($this->get_parameters()->getWorkspace()->getId());
        $category->set_type($this->get_parameters()->getWorkspace()->getWorkspaceType());
        
        if (! $category->create())
        {
            return false;
        }
        else
        {
            $this->set_category_id_cache_id($node->getAttribute('id'), $category->get_id());
            $child_nodes = $this->dom_xpath->query('//category[parent_id=' . $node->getAttribute('id') . ']');
            foreach ($child_nodes as $child_node)
            {
                $this->process_category($child_node, $category->get_id());
            }
        }
    }

    public function process_helpers($content_object_node)
    {
        $content_object_type = $this->determine_content_object_type(
            $this->dom_xpath->query('general/type', $content_object_node)->item(0)->nodeValue);
        
        if (in_array($content_object_type, DataManager::get_active_helper_types()))
        {
            $content_object_reference_id = convert_uudecode(
                $this->dom_xpath->query('extended/reference_id', $content_object_node)->item(0)->nodeValue);
            if (! $this->get_content_object_id_cache_id($content_object_reference_id))
            {
                $content_object_reference_list = $this->dom_xpath->query(
                    '/export/content_objects/content_object[@id="' . $content_object_reference_id . '"]');
                if ($content_object_reference_list->length == 1)
                {
                    $this->process_content_object($content_object_reference_list->item(0));
                }
            }
        }
    }

    public function update_helpers($content_object_node, $content_object)
    {
        $content_object_type = $this->determine_content_object_type(
            $this->dom_xpath->query('general/type', $content_object_node)->item(0)->nodeValue);
        
        if (in_array($content_object_type, DataManager::get_active_helper_types()))
        {
            $content_object_reference_id = $this->dom_xpath->query('extended/reference_id', $content_object_node)->item(
                0)->nodeValue;
            $content_object->set_reference(
                $this->get_content_object_id_cache_id(convert_uudecode($content_object_reference_id)));
        }
    }

    public function process_attachments($content_object_node)
    {
        $attachment_list = $this->dom_xpath->query('attachments/attachment', $content_object_node);
        foreach ($attachment_list as $attachment_node)
        {
            $id_ref = $attachment_node->getAttribute('idref');
            
            if (! $this->get_content_object_id_cache_id($id_ref))
            {
                $content_object_node_list = $this->dom_xpath->query(
                    '/export/content_objects/content_object[@id="' . $id_ref . '"]');
                if ($content_object_node_list->length == 1)
                {
                    $this->process_content_object($content_object_node_list->item(0));
                }
            }
        }
    }

    public function create_attachments($content_object_node)
    {
        $attachment_list = $this->dom_xpath->query('attachments/attachment', $content_object_node);
        foreach ($attachment_list as $attachment_node)
        {
            $content_object_attachment = new ContentObjectAttachment();
            $content_object_attachment->set_content_object_id(
                $this->get_content_object_id_cache_id($content_object_node->getAttribute('id')));
            $content_object_attachment->set_attachment_id(
                $this->get_content_object_id_cache_id($attachment_node->getAttribute('idref')));
            $content_object_attachment->set_type($attachment_node->getAttribute('type'));
            $content_object_attachment->create();
        }
    }

    public function process_includes($content_object_node)
    {
        $include_list = $this->dom_xpath->query('includes/include', $content_object_node);
        foreach ($include_list as $include_node)
        {
            $id_ref = $include_node->getAttribute('idref');
            
            if (! $this->get_content_object_id_cache_id($id_ref))
            {
                $content_object_node_list = $this->dom_xpath->query(
                    '/export/content_objects/content_object[@id="' . $id_ref . '"]');
                if ($content_object_node_list->length == 1)
                {
                    $this->process_content_object($content_object_node_list->item(0));
                }
            }
        }
    }

    public function create_includes($content_object_node)
    {
        $include_list = $this->dom_xpath->query('includes/include', $content_object_node);
        foreach ($include_list as $include_node)
        {
            $content_object_include = new ContentObjectInclude();
            $content_object_include->set_content_object_id(
                $this->get_content_object_id_cache_id($content_object_node->getAttribute('id')));
            $content_object_include->set_include_id(
                $this->get_content_object_id_cache_id($include_node->getAttribute('idref')));
            $content_object_include->create();
        }
    }

    public function process_sub_items($content_object_node)
    {
        $sub_item_list = $this->dom_xpath->query('sub_items/sub_item', $content_object_node);
        foreach ($sub_item_list as $sub_item_node)
        {
            $id_ref = $sub_item_node->getAttribute('idref');
            
            if (! $this->get_content_object_id_cache_id($id_ref))
            {
                $content_object_node_list = $this->dom_xpath->query(
                    '/export/content_objects/content_object[@id="' . $id_ref . '"]');
                if ($content_object_node_list->length == 1)
                {
                    $this->process_content_object($content_object_node_list->item(0));
                }
            }
        }
    }

    public function create_sub_items($content_object_node)
    {
        $sub_item_list = $this->dom_xpath->query('sub_items/sub_item', $content_object_node);
        foreach ($sub_item_list as $key => $sub_item_node)
        {
            $id = $sub_item_node->getAttribute('id');
            
            if (! $this->get_complex_content_object_item_id_cache_id($id))
            {
                $id_ref = $sub_item_node->getAttribute('idref');
                $complex_content_object_item = ComplexContentObjectItem::factory(
                    $this->determine_content_object_type(
                        $this->dom_xpath->query(
                            '/export/content_objects/content_object[@id="' . $id_ref . '"]/general/type')->item(0)->nodeValue));
                
                $complex_content_object_item->set_ref($this->get_content_object_id_cache_id($id_ref));
                $complex_content_object_item->set_user_id(Session::get_user_id());
                $complex_content_object_item->set_parent(
                    $this->get_content_object_id_cache_id($content_object_node->getAttribute('id')));
                $complex_content_object_item->set_display_order($key + 1);
                foreach ($complex_content_object_item->get_additional_property_names() as $additional_property)
                {
                    $complex_content_object_item->set_additional_property(
                        $additional_property, 
                        $sub_item_node->getAttribute($additional_property));
                }
                $complex_content_object_item->create();
                $this->set_complex_content_object_item_id_cache_id($id, $complex_content_object_item->get_id());
            }
        }
    }

    public function process_external_sync($content_object_node)
    {
        $external_sync_node_list = $this->dom_xpath->query('external_sync', $content_object_node);
        
        if ($external_sync_node_list->length == 1)
        {
            $external_sync_node = $external_sync_node_list->item(0);
            $external_id = $external_sync_node->getAttribute('external_instance');
            if (! $this->get_external_instance_id_cache_id($external_id))
            {
                $external_instance_node_list = $this->dom_xpath->query(
                    '/export/external_instance[@id="' . $external_id . '"]');
                if ($external_instance_node_list->length == 1)
                {
                    
                    $external_instance_node = $external_instance_node_list->item(0);
                    $conditions = array();
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(Instance::class, Instance::PROPERTY_IMPLEMENTATION),
                        new StaticConditionVariable($external_instance_node->getAttribute('type')));
                    $condition = new AndCondition($conditions);
                    
                    $external_instances = \Chamilo\Core\Repository\Instance\Storage\DataManager::retrieves(
                        Instance::class,
                        new DataClassRetrievesParameters($condition));
                    foreach($external_instances as $external_instance)
                    {
                        $setting_node_list = $this->dom_xpath->query('setting', $external_instance_node);
                        $is_matching_external_instance = true;
                        foreach ($setting_node_list as $setting_node)
                        {
                            $variable = $this->dom_xpath->query('variable', $setting_node)->item(0)->nodeValue;
                            $value = $this->dom_xpath->query('value', $setting_node)->item(0)->nodeValue;
                            
                            if ($external_instance->get_setting($variable) != $value)
                            {
                                $is_matching_external_instance = false;
                                break;
                            }
                        }
                        if (! $is_matching_external_instance)
                        {
                            continue;
                        }
                        else
                        {
                            $this->set_external_instance_id_cache_id($external_id, $external_instance->get_id());
                            break;
                        }
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return true;
            }
        }
        else
        {
            return true;
        }
    }

    public function set_external_sync($content_object_node, $content_object)
    {
        $external_sync_node_list = $this->dom_xpath->query('external_sync', $content_object_node);
        
        if ($external_sync_node_list->length == 1)
        {
            $external_sync_node = $external_sync_node_list->item(0);
            $external_id = $external_sync_node->getAttribute('external_instance');
            
            if ($this->get_external_instance_id_cache_id($external_id))
            {
                $external_sync = new SynchronizationData();
                $external_sync->set_content_object_id($content_object->get_id());
                $external_sync->set_content_object_timestamp($content_object->get_modification_date());
                $external_sync->set_external_id($this->get_external_instance_id_cache_id($external_id));
                $external_sync->set_external_object_id($external_sync_node->getAttribute('id'));
                $external_sync->set_external_object_timestamp($external_sync_node->getAttribute('timestamp'));
                
                $content_object->set_synchronization_data($external_sync);
                return $content_object;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return true;
        }
    }

    public function create_external_sync($content_object)
    {
        $external_sync = $content_object->get_synchronization_data();
        
        if ($external_sync instanceof SynchronizationData)
        {
            $external_sync->set_content_object_id($content_object->get_id());
            $external_sync->set_content_object_timestamp($content_object->get_modification_date());
            return $external_sync->create();
        }
        else
        {
            return true;
        }
    }

    /**
     * Creates the tags for the given content object
     * 
     * @param \DOMNode $content_object_node
     * @param ContentObject $content_object
     */
    public function create_content_object_tags($content_object_node, $content_object)
    {
        $tags_node = $this->dom_xpath->query('general/tags', $content_object_node)->item(0);
        if ($tags_node)
        {
            $tags_content = $tags_node->nodeValue;
            $tags = explode(',', $tags_content);
            DataManager::set_tags_for_content_objects(
                $tags, 
                array($content_object->get_id()), 
                $content_object->get_owner_id());
        }
    }

    public function process_content_object($content_object_node)
    {
	    $configuration = Configuration::getInstance();

        $content_object_parameter = new CpoContentObjectImportParameters($content_object_node);
        
        $this->process_attachments($content_object_node);
        $this->process_includes($content_object_node);
        $this->process_sub_items($content_object_node);
        $this->process_helpers($content_object_node);
        $this->process_external_sync($content_object_node);

        $content_object = ContentObjectImportImplementation::launch(
            $this, 
            $this->determine_content_object_type(
                $this->dom_xpath->query('general/type', $content_object_node)->item(0)->nodeValue), 
            $content_object_parameter);
        
        $external_sync = $this->set_external_sync($content_object_node, $content_object);
        
        $this->update_helpers($content_object_node, $content_object);

        if (($configuration->get_setting(array('Chamilo\Core\Repository', 'description_required')) == '1') && $content_object->get_description() == '') {
	        $content_object->set_description($content_object->get_title());
        }

        $content_object->create();

        $this->process_workspace_category($content_object_node, $content_object);
        
        $this->set_content_object_id_cache_id($content_object_node->getAttribute('id'), $content_object->get_id());
        
        $this->create_attachments($content_object_node);
        $this->create_includes($content_object_node);
        $this->create_sub_items($content_object_node);
        $this->create_external_sync($content_object);
        $this->create_content_object_tags($content_object_node, $content_object);
        
        ContentObjectImportImplementation::post_process(
            $this, 
            $this->determine_content_object_type(
                $this->dom_xpath->query('general/type', $content_object_node)->item(0)->nodeValue), 
            $content_object_parameter, 
            $content_object);
    }

    public function process_workspace_category($contentObjectNode, $contentObject)
    {
        if ($this->get_parameters()->getWorkspace() instanceof Workspace)
        {
            $parentId = $this->dom_xpath->query('general/parent_id', $contentObjectNode)->item(0)->nodeValue;
            
            if ($parentId != 0)
            {
                $parentId = $this->get_category_id_cache_id($parentId);
            }
            else
            {
                $parentId = $this->get_parameters()->get_category();
            }
            
            $contentObjectRelationService = new ContentObjectRelationService(new ContentObjectRelationRepository());
            $contentObjectRelationService->createContentObjectRelation(
                $this->get_parameters()->getWorkspace()->getId(), 
                $contentObject->getId(), 
                $parentId);
        }
    }

    public function unzip()
    {
        $unzip = Filecompression::factory();
        return $unzip->extract_file($this->get_parameters()->get_file()->get_path());
    }

    public function get_xml_path()
    {
        return $this->temporary_directory . '/content_object.xml';
    }

    public function get_data_path()
    {
        return $this->temporary_directory . '/data/';
    }

    /**
     * Parses the content object type from a given xpath element.
     * Is backwards compatible with the cpo exports from
     * version 3.x
     * 
     * @param string $xpath_value
     *
     * @return string
     */
    public function determine_content_object_type($xpath_value)
    {
        $configuration = Configuration::getInstance();
        
        /**
         * Backwards Compatibility
         */
        if (strpos($xpath_value, '\\') === false)
        {
            $context = 'Chamilo\Core\Repository\ContentObject\\' .
                 (string) StringUtilities::getInstance()->createString($xpath_value)->upperCamelize();
        }
        else
        {
            $context = ClassnameUtilities::getInstance()->getNamespaceParent($xpath_value, 3);
        }
        
        $registration = $configuration->get_registration($context);
        
        if ($registration[Registration::PROPERTY_TYPE] != 'Chamilo\Core\Repository\ContentObject')
        {
            throw new InvalidArgumentException(
                sprintf('The imported value (%s) is not of type Chamilo\Core\Repository\ContentObject', $xpath_value));
        }
        
        return $registration[Registration::PROPERTY_CONTEXT] . '\Storage\DataClass\\' .
               ClassnameUtilities::getInstance()->getPackageNameFromNamespace($registration[Registration::PROPERTY_CONTEXT]);
    }

    public static function get_allowed_extensions()
    {
        return array('cpo');
    }

    public static function is_available()
    {
        return true;
    }

    /**
     *
     * @return integer
     */
    public function determine_parent_id($parent_id)
    {
        if ($this->get_parameters()->getWorkspace() instanceof PersonalWorkspace)
        {
            return is_null($parent_id) ? 0 : $parent_id;
        }
        else
        {
            return 0;
        }
    }
}
