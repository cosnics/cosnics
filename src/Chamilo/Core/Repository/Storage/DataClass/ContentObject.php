<?php

namespace Chamilo\Core\Repository\Storage\DataClass;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Core\Repository\Common\ContentObjectDifference;
use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPath;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregator;
use Chamilo\Core\Repository\Service\TemplateRegistrationConsulter;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\UUID;
use Exception;

/**
 *
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class ContentObject extends CompositeDataClass
{

    const ATTACHMENT_ALL = 'all';
    const ATTACHMENT_NORMAL = 'normal';

    const CURRENT_MULTIPLE = 2;
    const CURRENT_OLD = 0;
    const CURRENT_SINGLE = 1;

    const PARAM_SECURITY_CODE = 'security_code';

    const PROPERTIES_ADDITIONAL = 'additional_properties';
    const PROPERTY_COMMENT = 'comment';
    const PROPERTY_CONTENT_HASH = 'content_hash';
    const PROPERTY_CREATION_DATE = 'created';
    const PROPERTY_CURRENT = 'current';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_MODIFICATION_DATE = 'modified';
    const PROPERTY_OBJECT_NUMBER = 'object_number';
    const PROPERTY_OWNER_ID = 'owner_id';
    const PROPERTY_PARENT_ID = 'parent_id';
    const PROPERTY_STATE = 'state';
    const PROPERTY_TEMPLATE_REGISTRATION_ID = 'template_registration_id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_TYPE = 'type';

    const STATE_AUTOSAVE = 8;
    const STATE_BACKUP = 16;
    const STATE_INACTIVE = 1;
    const STATE_NORMAL = 2;
    const STATE_RECYCLED = 4;

    /**
     * objects attached to this object.
     */
    private $attachments = [];

    private $attachment_ids = [];

    /**
     * Objects included into this object.
     */
    private $includes;

    /**
     * The state that this object had when it was retrieved.
     * Used to determine if the state of its children should be
     * updated upon updating the object.
     */
    private $oldState;

    /**
     *
     * @var \Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData
     */
    private $synchronization_data;

    /**
     *
     * @var ComplexContentObjectPath
     */
    private $complex_content_object_path;

    /**
     *
     * @var TemplateRegistration
     */
    private $template_registration;

    /**
     * Creates a new object.
     *
     * @param $id int The numeric ID of the object. May be omitted if creating a new object.
     * @param $defaultProperties array The default properties of the object. Associative array.
     * @param $additionalProperties array The properties specific for this type of object. Associative array. Null if
     *        they are unknown at construction of the object; in this case, they will be retrieved when needed.
     */
    public function __construct($default_properties = [], $additional_properties = null)
    {
        parent::__construct($default_properties);
        $this->set_additional_properties($additional_properties);
        $this->oldState = $default_properties[self::PROPERTY_STATE];
    }

    public function __tostring(): string
    {
        return get_class($this) . '#' . $this->get_id() . ' (' . $this->get_title() . ')';
    }

    /**
     * Attaches the object with the given ID to this object.
     *
     * @param $id int The ID of the object to attach.
     */
    public function attach_content_object($id, $type = self::ATTACHMENT_NORMAL)
    {
        if ($this->is_attached_to($id, $type))
        {
            return true;
        }
        else
        {
            $attachment = new ContentObjectAttachment();
            $attachment->set_attachment_id($id);
            $attachment->set_content_object_id($this->get_id());
            $attachment->set_type($type);

            return $attachment->create();
        }
    }

    public function attach_content_objects($ids = [], $type = self::ATTACHMENT_NORMAL)
    {
        if (is_null($ids))
        {
            return true;
        }

        if (!is_array($ids))
        {
            $ids = array($ids);
        }

        foreach ($ids as $id)
        {
            if (!$this->attach_content_object($id, $type))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Creates a security code.
     * The following values are used because they only change in special cases (copy, import).
     * This is important because it is hardcoded into some fields with included content e.g. description was used: If a
     * change was made to the description of an included object, the security code in the including object wouldn't
     * match anymore unless replaced.
     *
     * @return string
     */
    public function calculate_security_code()
    {
        return sha1($this->get_id() . ':' . $this->get_creation_date());
    }

    /**
     * Converts a class name to the corresponding object type name.
     *
     * @param $class string The class name.
     *
     * @return string The type name.
     */
    static public function class_to_type($class)

    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace($class, true);
    }

    public function count_attachers($only_version = false)
    {
        // if ($only_version)
        // {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_ATTACHMENT_ID
            ), new StaticConditionVariable($this->get_id())
        );

        $join = new Join(
            ContentObject::class, new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_CONTENT_OBJECT_ID
                ), new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID)
            )
        );

        $parameters = new DataClassCountParameters($condition, new Joins(array($join)));

        return DataManager::count(ContentObjectAttachment::class, $parameters);
    }

    public function count_attachments()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($this->get_id())
        );

        $join = new Join(
            ContentObject::class, new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_CONTENT_OBJECT_ID
                ), new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID)
            )
        );

        $parameters = new DataClassCountParameters($condition, new Joins(array($join)));

        return DataManager::count(ContentObjectAttachment::class, $parameters);
    }

    public function count_children()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
            ), new StaticConditionVariable($this->get_id()), ComplexContentObjectItem::getTableName()
        );

        return DataManager::count_complex_content_object_items(
            ComplexContentObjectItem::class, new DataClassCountParameters($condition)
        );
    }

    public function count_includers($only_version = false)
    {
        // if ($only_version)
        // {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectInclude::class, ContentObjectInclude::PROPERTY_INCLUDE_ID
            ), new StaticConditionVariable($this->get_id())
        );

        $join = new Join(
            ContentObject::class, new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectInclude::class, ContentObjectInclude::PROPERTY_CONTENT_OBJECT_ID
                ), new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID)
            )
        );

        $parameters = new DataClassCountParameters($condition, new Joins(array($join)));

        return DataManager::count(ContentObjectInclude::class, $parameters);
    }

    public function count_includes()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectInclude::class, ContentObjectInclude::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($this->get_id())
        );

        $join = new Join(
            ContentObject::class, new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectInclude::class, ContentObjectInclude::PROPERTY_CONTENT_OBJECT_ID
                ), new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID)
            )
        );

        $parameters = new DataClassCountParameters($condition, new Joins(array($join)));

        return DataManager::count(ContentObjectInclude::class, $parameters);
    }

    public function count_parents()
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_REF
            ), new StaticConditionVariable($this->get_id()), ComplexContentObjectItem::getTableName()
        );

        $helper_types = DataManager::get_active_helper_types();

        foreach ($helper_types as $helper_type)
        {
            $subselect_condition = new EqualityCondition(
                new PropertyConditionVariable($helper_type, 'reference_id'),
                new StaticConditionVariable($this->get_id())
            );
            $conditions[] = new SubselectCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_REF
                ), new PropertyConditionVariable($helper_type::class_name(), $helper_type::PROPERTY_ID), null,
                $subselect_condition
            );
        }

        $condition = new OrCondition($conditions);

        return DataManager::count_complex_content_object_items(
            ComplexContentObjectItem::class, new DataClassCountParameters($condition)
        );
    }

    public function count_publications()
    {
        return $this->getPublicationAggregator()->countPublicationAttributes(
            PublicationAggregator::ATTRIBUTES_TYPE_OBJECT, $this->getId()
        );
    }

    public function create($create_in_batch = false)
    {
        $content_object = $this;

        // TRANSACTION
        $success = DataManager::transactional(
            function ($c) use ($create_in_batch, $content_object) { // checks wether to create a new content object or
                // version:
                // if the ID is set, we create a new version,
                // otherwise a new CO.
                $orig_id = $content_object->get_id();
                $version = isset($orig_id);

                $now = time();
                $content_object->set_creation_date($now);
                $content_object->set_modification_date($now);

                if (!$content_object->get_template_registration_id())
                {
                    $default_template_registration =
                        $this->getTemplateRegistrationConsulter()->getTemplateRegistrationDefaultByType(
                            $content_object->package()
                        );

                    $content_object->set_template_registration_id($default_template_registration->get_id());
                }

                if ($version)
                { // id changes in create new version, so location needs to be fetched
                    // now
                    $content_object->set_current(ContentObject::CURRENT_MULTIPLE);
                }
                else
                {
                    $content_object->set_object_number(UUID::v4());
                    $content_object->set_current(ContentObject::CURRENT_SINGLE);
                }

                if (!call_user_func_array(
                    array($content_object, '\Chamilo\Libraries\Storage\DataClass\DataClass::create'), []
                ))
                {

                    return false;
                }

                if ($version)
                {
                    $conditions = [];
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(
                            ContentObject::class, ContentObject::PROPERTY_OBJECT_NUMBER
                        ), new StaticConditionVariable($content_object->get_object_number())
                    );
                    $conditions[] = new NotCondition(
                        new EqualityCondition(
                            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                            new StaticConditionVariable($content_object->get_id())
                        )
                    );
                    $condition = new AndCondition($conditions);
                    $parameters = new DataClassRetrievesParameters($condition);
                    $objects = DataManager::retrieve_content_objects($content_object::class_name(), $parameters);
                    foreach ($objects as $object)
                    {
                        $object->set_current(ContentObject::CURRENT_OLD);
                        $object->update(false);
                    }
                }

                return true;
            }
        );

        return $success;
    }

    public function create_all()
    {
        $now = time();

        if (!$this->get_creation_date())
        {
            $this->set_creation_date($now);
        }

        if (!$this->get_modification_date())
        {
            $this->set_modification_date($now);
        }

        $this->set_object_number(UUID::v4());

        if (!parent::create())
        {
            return false;
        }

        return true;
    }

    /**
     * Instructs the data manager to delete the object.
     */
    public function delete($only_version = false)
    {
        $versions = $this->get_content_object_versions();
        foreach ($versions as $version)
        {
            if (!$this->getPublicationAggregator()->canContentObjectBeUnlinked($version))
            {
                return false;
            }
        }

        $content_object = $this;

        // TRANSACTION
        $success = DataManager::transactional(
            function ($c) use ($only_version, $content_object) {
                if ($only_version)
                {
                    if (!$content_object->version_delete())
                    {
                        return false;
                    }

                    $this->getDataClassRepositoryCache()->reset();
                    $count = DataManager::count_content_object_versions($content_object);

                    if ($count > 0)
                    {
                        $new_latest_content_object =
                            DataManager::retrieve_best_candidate_for_most_recent_content_object_version(
                                $content_object->get_object_number()
                            );

                        $new_latest_content_object->set_current(
                            ($count > 1 ? $content_object::CURRENT_MULTIPLE : $content_object::CURRENT_SINGLE)
                        );

                        $success = $new_latest_content_object->update();

                        return $success;
                    }

                    return true;
                }
                else
                {
                    $versions = $content_object->get_content_object_versions();

                    foreach ($versions as $version)
                    {
                        if (!$version->delete(true))
                        {
                            return false;
                        }
                    }

                    return true;
                }
            }
        );

        return $success;
    }

    public function delete_assisting_content_objects()
    {
        $assisting_types = DataManager::get_active_helper_types();
        $failures = 0;

        foreach ($assisting_types as $type)
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable($type::class_name(), 'reference_id'),
                new StaticConditionVariable($this->get_id())
            );
            $assisting_objects = DataManager::retrieve_active_content_objects($type, $condition);

            foreach ($assisting_objects as $assisting_object)
            {
                $conditions = [];
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_REF
                    ), new StaticConditionVariable($assisting_object->get_id())
                );
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
                    ), new StaticConditionVariable($assisting_object->get_id()),
                    ComplexContentObjectItem::getTableName()
                );

                $condition = new OrCondition($conditions);

                $items = DataManager::retrieve_complex_content_object_items(
                    ComplexContentObjectItem::class, $condition
                );
                foreach ($items as $item)
                {
                    if (!$item->delete())
                    {
                        $failures ++;
                    }
                }

                if (!$assisting_object->delete())
                {
                    $failures ++;
                }
            }
        }

        return ($failures == 0);
    }

    public function delete_links()
    {
        $versions = $this->get_content_object_versions();
        foreach ($versions as $version)
        {
            if (!$this->getPublicationAggregator()->canContentObjectBeUnlinked($version))
            {
                return false;
            }
        }

        // Delete link with workspaces
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation::class, WorkspaceContentObjectRelation::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($this->get_object_number())
        );

        if (!DataManager::deletes(WorkspaceContentObjectRelation::class, $condition))
        {
            return false;
        }

        // Delete attachment links of the object
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_ATTACHMENT_ID
            ), new StaticConditionVariable($this->get_id())
        );

        if (!DataManager::deletes(ContentObjectAttachment::class, $condition))
        {
            return false;
        }

        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_REF
            ), new StaticConditionVariable($this->get_id())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
            ), new StaticConditionVariable($this->get_id()), ComplexContentObjectItem::getTableName()
        );

        $condition = new OrCondition($conditions);

        $items = DataManager::retrieve_complex_content_object_items(ComplexContentObjectItem::class, $condition);
        foreach ($items as $item)
        {
            if (!$item->delete())
            {
                return false;
            }
        }

        $includes = $this->get_includes();
        foreach ($includes as $include)
        {
            if (!$include->delete())
            {
                return false;
            }
        }

        if ($this->getPublicationAggregator()->deleteContentObjectPublications($this) &&
            $this->delete_assisting_content_objects())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Removes the object with the given ID from this object's attachment list.
     *
     * @param $id int The ID of the object to remove from the attachment list.
     *
     * @return boolean True if the attachment was removed, false if it did not exist.
     */
    public function detach_content_object($id, $type = self::ATTACHMENT_NORMAL)
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($this->get_id())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_ATTACHMENT_ID
            ), new StaticConditionVariable($id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_TYPE
            ), new StaticConditionVariable($type)
        );
        $condition = new AndCondition($conditions);

        $attachment = DataManager::retrieve(
            ContentObjectAttachment::class, new DataClassRetrieveParameters($condition)
        );

        if ($attachment instanceof ContentObjectAttachment)
        {
            return $attachment->delete();
        }
        else
        {
            return false;
        }
    }

    public function detach_content_objects($ids = [], $type = self::ATTACHMENT_NORMAL)
    {
        if (!is_array($ids))
        {
            $ids = array($ids);
        }

        foreach ($ids as $id)
        {
            if (!$this->detach_content_object($id, $type))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Removes the object with the given ID from this object's include list.
     *
     * @param $id int The ID of the object to remove from the include list.
     *
     * @return boolean True if the include was removed, false if it did not exist.
     */
    public function exclude_content_object($id)
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectInclude::class, ContentObjectInclude::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($this->get_id())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectInclude::class, ContentObjectInclude::PROPERTY_INCLUDE_ID
            ), new StaticConditionVariable($id)
        );
        $condition = new AndCondition($conditions);

        $include = DataManager::retrieve(
            ContentObjectInclude::class, new DataClassRetrieveParameters($condition)
        );

        if ($include instanceof ContentObjectInclude)
        {
            return $include->delete();
        }
        else
        {
            return false;
        }
    }

    /**
     * @return \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache
     */
    protected function getDataClassRepositoryCache()
    {
        return $this->getService(
            DataClassRepositoryCache::class
        );
    }

    /**
     * @return string
     */
    public function getDefaultGlyphNamespace()
    {
        return self::package();
    }

    /**
     * @param integer $size
     * @param boolean $isAvailable
     * @param string[] $extraClasses
     *
     * @return \Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function getGlyph($size = IdentGlyph::SIZE_SMALL, $isAvailable = true, $extraClasses = [])
    {
        $templateRegistration = $this->get_template_registration();

        if ($templateRegistration instanceof TemplateRegistration && !$templateRegistration->get_default())
        {
            $glyphTitle = 'TypeName' . $templateRegistration->get_name();
        }
        else
        {
            $glyphTitle = null;
        }

        return self::glyph($this->getGlyphNamespace(), $size, $isAvailable, $glyphTitle, $extraClasses);
    }

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function getGlyphNamespace()
    {
        $templateRegistration = $this->get_template_registration();

        if ($templateRegistration instanceof TemplateRegistration && !$templateRegistration->get_default())
        {
            return $templateRegistration->get_content_object_type() . '\Template\\' . $templateRegistration->get_name();
        }
        else
        {
            return $this->getDefaultGlyphNamespace();
        }
    }

    /**
     * @return \Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface
     * @throws \Exception
     */
    public function getPublicationAggregator()
    {
        return $this->getService(PublicationAggregator::class);
    }

    /**
     * @param string $serviceName
     *
     * @return object
     * @throws \Exception
     */
    protected function getService(string $serviceName)
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            $serviceName
        );
    }

    /**
     * @return string
     */
    static public function getStorageSpaceProperty()
    {
        return null;
    }

    /**
     * @return \Chamilo\Core\Repository\Service\TemplateRegistrationConsulter
     * @throws \Exception
     */
    public function getTemplateRegistrationConsulter()
    {
        return $this->getService(TemplateRegistrationConsulter::class);
    }

    /**
     * Helper function to retrieve a virtual path by a given category id
     *
     * @param $categoryId
     *
     * @return string
     */
    protected function getVirtualPathByCategoryId($categoryId, $rootPath = null)
    {
        if (!$rootPath)
        {
            $rootPath = $this->get_owner_fullname();
        }

        $virtual_path = [];

        while ($categoryId != 0)
        {
            $category = DataManager::retrieve_by_id(RepositoryCategory::class, $categoryId);
            $categoryId = $category->get_parent();
            array_unshift($virtual_path, Filesystem::create_safe_name($category->get_name()));
        }

        array_unshift($virtual_path, Filesystem::create_safe_name($rootPath));

        return implode(DIRECTORY_SEPARATOR, $virtual_path) . DIRECTORY_SEPARATOR;
    }

    /**
     * Retrieves the virtual path of this content object in a given workspace
     *
     * @param WorkspaceInterface $workspace
     *
     * @return string
     * @throws \Exception
     */
    public function getVirtualPathInWorkspace(WorkspaceInterface $workspace)
    {
        if ($workspace instanceof PersonalWorkspace)
        {
            return $this->getVirtualPathByCategoryId($this->get_parent_id());
        }

        $contentObjectRelationService = new ContentObjectRelationService(new ContentObjectRelationRepository());
        $contentObjectRelation = $contentObjectRelationService->getContentObjectRelationForWorkspaceAndContentObject(
            $workspace, $this
        );

        if (!$contentObjectRelation)
        {
            throw new Exception('ContentObject not found in given workspace');
        }

        return $this->getVirtualPathByCategoryId($contentObjectRelation->getCategoryId(), $workspace->getTitle());
    }

    public static function get_active_status_types()
    {
        return array(self::STATE_NORMAL, self::STATE_RECYCLED, self::STATE_AUTOSAVE, self::STATE_BACKUP);
    }

    /**
     * Retrieves this object's ancestors.
     *
     * @return array The ancestors, all objects.
     */
    public function get_ancestors()
    {
        $ancestors = [];
        $aid = $this->get_parent_id();
        while ($aid > 0)
        {
            $ancestor = DataManager::retrieve_by_id(ContentObject::class, $aid);
            $ancestors[] = $ancestor;
            $aid = $ancestor->get_parent_id();
        }

        return $ancestors;
    }

    /**
     * Returns the object ids attached to this object.
     *
     * @return array The objects.
     */
    public function get_attached_content_object_ids($type = self::ATTACHMENT_NORMAL)
    {
        if (!is_array($this->attachment_ids[$type]))
        {
            $conditions = [];
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_CONTENT_OBJECT_ID
                ), new StaticConditionVariable($this->get_id())
            );
            if ($type != self::ATTACHMENT_ALL)
            {
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_TYPE
                    ), new StaticConditionVariable($type)
                );
            }
            $condition = new AndCondition($conditions);

            $parameters = new DataClassDistinctParameters(
                $condition, new DataClassProperties(
                    array(
                        new PropertyConditionVariable(
                            ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_ATTACHMENT_ID
                        )
                    )
                )
            );
            $this->attachment_ids[$type] = DataManager::distinct(ContentObjectAttachment::class, $parameters);
        }

        return $this->attachment_ids[$type];
    }

    public function get_attachers($order_by = [], $offset = null, $count = null)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_ATTACHMENT_ID
            ), new StaticConditionVariable($this->get_id())
        );

        $join = new Join(
            ContentObjectAttachment::class, new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_CONTENT_OBJECT_ID
                ), new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID)
            )
        );

        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_by, new Joins(array($join)));

        return DataManager::retrieve_content_objects(ContentObject::class, $parameters);
    }

    /**
     * @param string $type
     * @param array $order_by
     * @param null $offset
     * @param null $count
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject[]
     */
    public function get_attachments(
        $type = self::ATTACHMENT_NORMAL, $order_by = [], $offset = null, $count = null
    )
    {
        if (!is_array($this->attachments[$type]))
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_CONTENT_OBJECT_ID
                ), new StaticConditionVariable($this->get_id())
            );

            $join = new Join(
                ContentObjectAttachment::class, new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_ATTACHMENT_ID
                    ), new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID)
                )
            );

            $parameters = new DataClassRetrievesParameters(
                $condition, $count, $offset, $order_by, new Joins(array($join))
            );
            $this->attachments[$type] = DataManager::retrieve_content_objects(ContentObject::class, $parameters);
        }

        return $this->attachments[$type];
    }

    /**
     *
     * @param $content_object_id integer
     *
     * @return ContentObject An object inheriting from ContentObject
     */
    public static function get_by_id($content_object_id)
    {
        return DataManager::retrieve_by_id(ContentObject::class, $content_object_id);
    }

    public function get_children($order_by = [], $offset = null, $count = null)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
            ), new StaticConditionVariable($this->get_id()), ComplexContentObjectItem::getTableName()
        );
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_by);

        return DataManager::retrieve_complex_content_object_items(ComplexContentObjectItem::class, $parameters);
    }

    /**
     * Returns the comment of this object version.
     *
     * @return string The version.
     */
    public function get_comment()
    {
        return $this->getDefaultProperty(self::PROPERTY_COMMENT);
    }

    /**
     *
     * @return ComplexContentObjectPath
     */
    public function get_complex_content_object_path()
    {
        if (!isset($this->complex_content_object_path))
        {
            $this->complex_content_object_path = ComplexContentObjectPath::factory(self::context(), $this);
        }

        return $this->complex_content_object_path;
    }

    public function get_content_hash()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTENT_HASH);
    }

    /**
     * Returns the edition of this object
     *
     * @return int the number of the version.
     */
    public function get_content_object_edition()
    {
        return array_search($this->id, DataManager::get_version_ids($this)) + 1;
    }

    public static function get_content_object_type_namespace($type)
    {
        if (strpos($type, '\\') !== false)
        {
            return $type;
        }
        else
        {
            return 'Chamilo\Core\Repository\ContentObject\\' . $type;
        }
    }

    public function get_content_object_versions($include_last = true)
    {
        if (!is_array($this->versions))
        {
            $this->versions = DataManager::retrieve_content_object_versions($this);
        }

        if ($include_last)
        {
            return $this->versions;
        }
        else
        {
            return array_slice($this->versions, 1, sizeof($this->versions) - 1);
        }
    }

    /**
     * Returns the date when this object was created, as returned by PHP's time() function.
     *
     * @return int The creation date.
     */
    public function get_creation_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_CREATION_DATE);
    }

    public function get_current()
    {
        return $this->getDefaultProperty(self::PROPERTY_CURRENT);
    }

    /**
     * Get the default properties of all objects.
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array(
                self::PROPERTY_OWNER_ID,
                self::PROPERTY_TYPE,
                self::PROPERTY_TITLE,
                self::PROPERTY_DESCRIPTION,
                self::PROPERTY_PARENT_ID,
                self::PROPERTY_TEMPLATE_REGISTRATION_ID,
                self::PROPERTY_CREATION_DATE,
                self::PROPERTY_MODIFICATION_DATE,
                self::PROPERTY_OBJECT_NUMBER,
                self::PROPERTY_STATE,
                self::PROPERTY_COMMENT,
                self::PROPERTY_CONTENT_HASH,
                self::PROPERTY_CURRENT
            )
        );
    }

    /**
     * Returns the description of this object.
     *
     * @return string The description.
     */
    public function get_description()
    {
        return $this->getDefaultProperty(self::PROPERTY_DESCRIPTION);
    }

    /**
     * Returns the difference of this object with a given object based on it's id.
     *
     * @param $id int The ID of the object to compare with.
     *
     * @return array The difference.
     */
    public function get_difference($id)
    {
        $version = DataManager::retrieve_by_id(ContentObject::class, $id);

        return ContentObjectDifference::factory($this, $version);
    }

    /**
     * Get all properties of this type of object that should be taken into account to calculate the used disk space.
     * @return mixed The property names. Either a string, an array of strings, or null if no properties affect disk
     *         quota.
     * @deprecated Use ContentObject::getStorageSpaceProperty() now
     */
    static public function get_disk_space_properties()
    {
        return static::getStorageSpaceProperty();
    }

    /**
     * Returns the names of the properties which are UI-wise filled by the integrated html editor
     *
     * @return string[]
     */
    static public function get_html_editors($html_editors = [])

    {
        $html_editors[] = self::PROPERTY_DESCRIPTION;

        return $html_editors;
    }

    /**
     * @param int $size
     * @param bool $isAvailable
     * @param string[] $extraClasses
     *
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function get_icon_image(
        $size = IdentGlyph::SIZE_SMALL, $isAvailable = true, $extraClasses = []
    )
    {
        return $this->getGlyph($size, $isAvailable, $extraClasses)->render();
    }

    public static function get_inactive_status_types()
    {
        return array(
            self::STATE_NORMAL + self::STATE_INACTIVE,
            self::STATE_RECYCLED + self::STATE_INACTIVE,
            self::STATE_AUTOSAVE + self::STATE_INACTIVE,
            self::STATE_BACKUP + self::STATE_INACTIVE
        );
    }

    public function get_includers($order_by = [], $offset = null, $count = null)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectInclude::class, ContentObjectInclude::PROPERTY_INCLUDE_ID
            ), new StaticConditionVariable($this->get_id())
        );

        $join = new Join(
            ContentObjectInclude::class, new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectInclude::class, ContentObjectInclude::PROPERTY_CONTENT_OBJECT_ID
                ), new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID)
            )
        );

        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_by, new Joins(array($join)));

        return DataManager::retrieve_content_objects(ContentObject::class, $parameters);
    }

    /**
     *
     * @param array $order_by
     * @param null $offset
     * @param null $count
     *
     * @return ContentObject[]
     */
    public function get_includes($order_by = [], $offset = null, $count = null)
    {
        if (!is_array($this->includes))
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectInclude::class, ContentObjectInclude::PROPERTY_CONTENT_OBJECT_ID
                ), new StaticConditionVariable($this->get_id())
            );

            $join = new Join(
                ContentObjectInclude::class, new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectInclude::class, ContentObjectInclude::PROPERTY_INCLUDE_ID
                    ), new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID)
                )
            );

            $parameters = new DataClassRetrievesParameters(
                $condition, $count, $offset, $order_by, new Joins(array($join))
            );
            $this->includes = DataManager::retrieve_content_objects(ContentObject::class, $parameters);
        }

        return $this->includes;
    }

    public function get_latest_version()
    {
        return DataManager::retrieve_most_recent_content_object_version($this);
    }

    public function get_latest_version_id()
    {
        return $this->get_latest_version()->get_id();
    }

    /**
     *
     * @return array:
     */
    public static function get_managers()
    {
        return [];
    }

    /**
     * Returns the date when this object was last modified, as returned by PHP's time() function.
     *
     * @return int The modification time.
     */
    public function get_modification_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_MODIFICATION_DATE);
    }

    /**
     * Returns the version number.
     *
     * @return int The version number.
     */
    public function get_object_number()
    {
        return $this->getDefaultProperty(self::PROPERTY_OBJECT_NUMBER);
    }

    /**
     *
     * @return User
     */
    public function get_owner()
    {
        if (!isset($this->owner))
        {
            $this->owner = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                User::class, (int) $this->get_owner_id()
            );
        }

        return $this->owner;
    }

    public function get_owner_fullname()
    {
        $owner = $this->get_owner();

        if ($owner instanceof User)
        {
            return $owner->get_fullname();
        }

        return Translation::getInstance()->getTranslation('UserUnknown', null, Manager::context());
    }

    // create a version

    /**
     * Returns the ID of this object's owner.
     *
     * @return int The ID.
     */
    public function get_owner_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_OWNER_ID);
    }

    public function get_packages_from_filesystem()
    {
        $types = [];

        $directories = Filesystem::get_directory_content(
            Path::getInstance()->namespaceToFullPath('Chamilo\Core\Repository\ContentObject'),
            Filesystem::LIST_DIRECTORIES, true
        );

        foreach ($directories as $directory)
        {
            $directory_name_split = explode('Chamilo\Core\Repository\ContentObject\\', $directory);
            $namespace = self::get_content_object_type_namespace($directory_name_split[1]);

            if (Package::exists($namespace))
            {
                $types[] = $namespace;
            }
        }

        return $types;
    }

    /**
     * Returns the numeric identifier of the object's parent object.
     *
     * @return int The identifier.
     */
    public function get_parent_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_PARENT_ID);
    }

    public function get_parents($order_by = [], $offset = null, $count = null)
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_REF
            ), new StaticConditionVariable($this->get_id())
        );

        $helper_types = DataManager::get_active_helper_types();

        foreach ($helper_types as $helper_type)
        {
            $subselect_condition = new EqualityCondition(
                new PropertyConditionVariable($helper_type, 'reference_id'),
                new StaticConditionVariable($this->get_id())
            );
            $conditions[] = new SubselectCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_REF
                ), new PropertyConditionVariable($helper_type::class_name(), $helper_type::PROPERTY_ID), null,
                $subselect_condition
            );
        }

        $condition = new OrCondition($conditions);
        $parameters = new DataClassRetrievesParameters($condition, $count, $offset, $order_by);

        return DataManager::retrieve_complex_content_object_items(ComplexContentObjectItem::class, $parameters);
    }

    public function get_publications($count, $offset, $order_by)
    {
        return $this->getPublicationAggregator()->getContentObjectPublicationsAttributes(
            PublicationAggregator::ATTRIBUTES_TYPE_OBJECT, $this->getId(), null, $count, $offset, $order_by
        );
    }

    static public function get_searchable_property_names()
    {
        return [];
    }

    /**
     * Returns the state of this object.
     *
     * @return int The state.
     */
    public function get_state()
    {
        return $this->getDefaultProperty(self::PROPERTY_STATE);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData
     */
    public function get_synchronization_data()
    {
        if (!isset($this->synchronization_data))
        {
            $sync_condition = new EqualityCondition(
                new PropertyConditionVariable(
                    SynchronizationData::class, SynchronizationData::PROPERTY_CONTENT_OBJECT_ID
                ), new StaticConditionVariable($this->get_id())
            );

            $this->synchronization_data =
                \Chamilo\Core\Repository\Instance\Storage\DataManager::retrieve_synchronization_data_set(
                    $sync_condition
                )->current();
        }

        return $this->synchronization_data;
    }

    // XXX: Keep this around? Override? Make useful?

    public function set_synchronization_data($external_sync)
    {
        $this->synchronization_data = $external_sync;
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'repository_content_object';
    }

    public function get_template_registration()
    {
        if (!isset($this->template_registration))
        {
            $this->template_registration =
                $this->getTemplateRegistrationConsulter()->getTemplateRegistrationByIdentifier(
                    (int) $this->get_template_registration_id()
                );

            if (!$this->template_registration instanceof TemplateRegistration)
            {
                throw new ObjectNotExistException(Translation::get('TemplateRegistration'));
            }
        }

        return $this->template_registration;
    }

    /**
     * Returns the id of the template for the content object
     *
     * @return int
     */
    public function get_template_registration_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_TEMPLATE_REGISTRATION_ID);
    }

    /**
     * Returns the title of this object.
     *
     * @return string The title.
     */
    public function get_title()
    {
        return $this->getDefaultProperty(self::PROPERTY_TITLE);
    }

    public function get_type_string()
    {
        $template_registration = $this->get_template_registration();
        $type_string = $template_registration instanceof TemplateRegistration ? 'TypeName' .
            (string) StringUtilities::getInstance()->createString($template_registration->get_name())->upperCamelize() :
            null;

        return static::type_string($this::context(), $type_string);
    }

    /**
     * Returns the number of versions of the object
     */
    public function get_version_count()
    {
        return count(DataManager::get_version_ids($this));
    }

    /**
     * Retrieves a virtual path for this content object
     *
     * @return string
     * @deprecated @use getVirtualPathInWorkspace
     */
    public function get_virtual_path()
    {
        return $this->getVirtualPathByCategoryId($this->get_parent_id());
    }

    /**
     * @param string $glyphNamespace
     * @param integer $size
     * @param boolean $isAvailable
     * @param string $title
     * @param string[] $extraClasses
     *
     * @return \Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph
     */
    public static function glyph(
        $glyphNamespace, $size = IdentGlyph::SIZE_SMALL, $isAvailable = true, $title = null, $extraClasses = []
    )
    {
        return new NamespaceIdentGlyph($glyphNamespace, true, false, !$isAvailable, $size, $extraClasses, $title);
    }

    /**
     * Checks if the given ID is the ID of one of this object's ancestors.
     *
     * @param $ancestor_id int
     *
     * @return boolean True if the ID belongs to an ancestor, false otherwise.
     */
    public function has_ancestor($ancestor_id)
    {
        $aid = $this->get_parent_id();
        while ($aid > 0)
        {
            if ($aid == $ancestor_id)
            {
                return true;
            }
            $ancestor = DataManager::retrieve_by_id(ContentObject::class, $aid);
            $aid = $ancestor->get_parent_id();
        }

        return false;
    }

    public function has_attachers($only_version = false)
    {
        return $this->count_attachers($only_version) > 0;
    }

    public function has_attachments()
    {
        return $this->count_attachments() > 0;
    }

    public function has_children()
    {
        return $this->count_children() > 0;
    }

    public function has_description()
    {
        $description = $this->get_description();

        $isEmpty = ($description == '<p>&#160;</p>' || count($description) == 0);
        $isBlank = StringUtilities::getInstance()->createString($description)->isBlank();

        return !$isEmpty && !$isBlank;
    }

    public function has_includers($only_version = false)
    {
        return $this->count_includers($only_version) > 0;
    }

    public function has_includes()
    {
        return $this->count_includes() > 0;
    }

    public function has_parents()
    {
        return $this->count_parents() > 0;
    }

    public function has_publications()
    {
        return $this->count_publications() > 0;
    }

    /**
     * Checks if this object has versions
     *
     * @return boolean
     */
    public function has_versions()
    {
        return ($this->get_current() != 1);
    }

    /**
     * @param string $glyphNamespace
     * @param integer $size
     * @param boolean $isAvailable
     * @param string $title
     * @param string[] $extraClasses
     *
     * @return string
     */
    public static function icon_image(
        $glyphNamespace, $size = IdentGlyph::SIZE_SMALL, $isAvailable = true, $title = null, $extraClasses = []
    )
    {
        return self::glyph($glyphNamespace, $size, $isAvailable, $title, $extraClasses)->render();
    }

    /**
     * Includes the object with the given ID in this object.
     *
     * @param $id int The ID of the object to include.
     */
    public function include_content_object($id)
    {
        if ($this->is_included_in($id))
        {
            return true;
        }
        else
        {
            $include = new ContentObjectInclude();
            $include->set_include_id($id);
            $include->set_content_object_id($this->get_id());

            return $include->create();
        }
    }

    /**
     * Is the object attached to object with the identifier as passed on
     *
     * @param $object_id int
     *
     * @return boolean
     */
    public function is_attached_to($object_id, $type = ContentObject::ATTACHMENT_NORMAL)
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_ATTACHMENT_ID
            ), new StaticConditionVariable($object_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($this->get_id())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_TYPE
            ), new StaticConditionVariable($type)
        );
        $condition = new AndCondition($conditions);

        return DataManager::count(ContentObjectAttachment::class, new DataClassCountParameters($condition)) > 0;
    }

    /**
     * Recursive method to check all the attachments and includes of a content object to see if the check content object
     * is attached or included in the given content object
     *
     * @param $content_object_id int
     * @param $check_content_object_id int
     *
     * @return boolean
     */
    public function is_attached_to_or_included_in($object_id)
    {
        if ($this->is_attached_to($object_id))
        {
            return true;
        }

        if ($this->is_included_in($object_id))
        {
            return true;
        }

        $attachments = $this->get_attachments();
        foreach ($attachments as $attachment)
        {
            if ($attachment->is_attached_to_or_included_in($object_id))
            {
                return true;
            }
        }

        $includes = $this->get_includes();
        foreach ($includes as $include)
        {
            if ($include->is_attached_to_or_included_in($object_id))
            {
                return true;
            }
        }

        return false;
    }

    public static function is_available($type)
    {
        $namespace = ClassnameUtilities::getInstance()->getNamespaceParent(
            ClassnameUtilities::getInstance()->getNamespaceFromClassname($type), 2
        );

        // Type should be registered to be available
        if (!Configuration::getInstance()->isRegisteredAndActive($namespace))
        {
            return false;
        }

        if (!$type::is_type_available())
        {
            return false;
        }

        return true;
    }

    /**
     * Determines whether this object is a complex object
     *
     * @return boolean True if the LO is a CLO
     * @deprecated Use instanceof ComplexContentObjectSupport directly from now on
     */
    public function is_complex_content_object()
    {
        return $this instanceof ComplexContentObjectSupport;
    }

    public function is_current()
    {
        return ($this->get_current() != 0);
    }

    public function is_external()
    {
        $is_external = $this->get_synchronization_data();

        return isset($is_external);
    }

    public function is_included_in($object_id)
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectInclude::class, ContentObjectInclude::PROPERTY_INCLUDE_ID
            ), new StaticConditionVariable($object_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectInclude::class, ContentObjectInclude::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($this->get_id())
        );
        $condition = new AndCondition($conditions);

        return DataManager::count(ContentObjectInclude::class, new DataClassCountParameters($condition)) > 0;
    }

    /**
     * Checks whether the current version of the object is the latest version
     *
     * @deprecated Use is_current() now
     */
    public function is_latest_version()
    {
        return $this->is_current();
    }

    public function is_not_allowed_shared_objects()
    {
        return false;
    }

    public static function is_type_available()
    {
        return true;
    }

    /**
     * Determines whether this object can have versions.
     *
     * @return boolean True if the object is versionable, false otherwise.
     * @deprecated Use instanceof Versionable directly from now on
     */
    public function is_versionable()
    {
        return $this instanceof Versionable;
    }

    public function move($new_parent_id)
    {
        $this->set_parent_id($new_parent_id);

        return DataManager::moveContentObjectToNewParent($this, $new_parent_id);
    }

    /**
     * placeholder: if a specific type of content object stores include links outside the fields, this function
     * processes them e.g: documents: in the html file
     */
    public function process_additional_include_links($pattern, $replacement_string)
    {
        return true;
    }

    public function recycle()
    {
        $this->set_modification_date(time());
        $this->set_state(self::STATE_RECYCLED);

        return parent::update();
    }

    /**
     * Sets the comment of this object version.
     *
     * @param $comment string The comment.
     */
    public function set_comment($comment)
    {
        $this->setDefaultProperty(self::PROPERTY_COMMENT, $comment);
    }

    public function set_content_hash($content_hash)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTENT_HASH, $content_hash);
    }

    /**
     * Sets the date when this object was created.
     *
     * @param $created int The creation date, as returned by time().
     */
    public function set_creation_date($created)
    {
        $this->setDefaultProperty(self::PROPERTY_CREATION_DATE, $created);
    }

    public function set_current($current)
    {
        $this->setDefaultProperty(self::PROPERTY_CURRENT, $current);
    }

    /**
     * Sets the description of this object.
     *
     * @param $description string The description.
     */
    public function set_description($description)
    {
        $this->setDefaultProperty(self::PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Sets the date when this object was modified.
     *
     * @param $modified int The modification date, as returned by time().
     */
    public function set_modification_date($modified)
    {
        $this->setDefaultProperty(self::PROPERTY_MODIFICATION_DATE, $modified);
    }

    /**
     * Sets the object number of this object.
     *
     * @param $object_number int The Object Number.
     */
    public function set_object_number($object_number)
    {
        $this->setDefaultProperty(self::PROPERTY_OBJECT_NUMBER, $object_number);
    }

    /**
     * Sets the ID of this object's owner.
     *
     * @param $id int The ID.
     */
    public function set_owner_id($owner)
    {
        $this->setDefaultProperty(self::PROPERTY_OWNER_ID, $owner);
    }

    /**
     * Sets the ID of this object's parent object.
     *
     * @param $parent int The ID.
     */
    public function set_parent_id($parent)
    {
        $this->setDefaultProperty(self::PROPERTY_PARENT_ID, $parent);
    }

    /**
     * Sets this object's state to any of the STATE_* constants.
     *
     * @param $state int The state.
     *
     * @return boolean True upon success, false upon failure.
     */
    public function set_state($state)
    {
        return $this->setDefaultProperty(self::PROPERTY_STATE, $state);
    }

    /**
     * Sets the template id of the content object
     *
     * @param int $template_registration_id
     */
    public function set_template_registration_id($template_registration_id)
    {
        $this->setDefaultProperty(self::PROPERTY_TEMPLATE_REGISTRATION_ID, $template_registration_id);
    }

    /**
     * Sets the title of this object.
     *
     * @param $title string The title.
     */
    public function set_title($title)
    {
        $this->setDefaultProperty(self::PROPERTY_TITLE, $title);
    }

    /**
     * Determines whether this object supports attachments, i.e.
     * whether other objects may be attached to it.
     *
     * @return boolean True if attachments are supported, false otherwise.
     * @deprecated Use instanceof AttachmentSupport directly from now on
     */
    public function supports_attachments()
    {
        return $this instanceof AttachmentSupport;
    }

    /**
     * Empty the lazy load variables to trigger a new retrieve
     *
     * @param $type String
     */
    public function truncate_attachment_cache($type = self::ATTACHMENT_NORMAL)
    {
        unset($this->attachment_ids[$type]);
        unset($this->attachments[$type]);
    }

    public function truncate_attachments($type = self::ATTACHMENT_NORMAL)
    {
        // Reset the cache
        $this->truncate_attachment_cache($type);

        // Delete all types of attachments from persistent storage (only the
        // links, not the actual objects)
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($this->get_id())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_TYPE
            ), new StaticConditionVariable($type)
        );
        $condition = new AndCondition($conditions);
        $attachments = $this->get_attachments($type);
        foreach ($attachments as $attachment)
        {
            if (!$attachment->delete())
            {
                return false;
            }
        }

        return true;
    }

    public static function type_exists($type)
    {
        $path = Path::getInstance()->namespaceToFullPath(
            'Chamilo\Core\Repository\ContentObject\\' .
            (string) StringUtilities::getInstance()->createString($type)->upperCamelize()
        );

        if (file_exists($path) && is_dir($path))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function type_string($context, $type_string = null)
    {
        // For the Default template fall back to the default translation so each content object doesn't have to
        // repeat a TypeNameDefault translation string
        $type_string = $type_string == 'TypeNameDefault' ? 'TypeName' : $type_string;

        return Translation::get($type_string ?: 'TypeName', null, $context);
    }

    /**
     * Converts a object type name to the corresponding class name.
     *
     * @param $type string The type name.
     *
     * @return string The class name.
     */
    static public function type_to_class($type)

    {
        return self::get_content_object_type_namespace($type) . '\\' .
            (string) StringUtilities::getInstance()->createString($type)->upperCamelize();
    }

    /**
     * Instructs the data manager to update the object, making any modifications permanent.
     * Also sets the object's
     * modification date to the current time if the update is a true update. A true update is an update that implicates
     * a change to a property that affects the object itself; changing the object's category, for instance, should not
     * change the last modification date.
     *
     * @param $trueUpdate boolean True if the update is a true update (default), false otherwise.
     *
     * @return boolean True if the update succeeded, false otherwise
     * .
     * @throws \Exception
     */
    public function update($trueUpdate = true)
    {
        $versions = $this->get_content_object_versions();
        foreach ($versions as $version)
        {
            if (!$this->getPublicationAggregator()->canContentObjectBeEdited($version->getId()))
            {
                return false;
            }
        }

        if ($trueUpdate)
        {
            $this->set_modification_date(time());
        }
        $success = parent::update();
        if (!$success)
        {
            return false;
        }

        /*
         * We return true here regardless of the result of the child update, since the object itself did get updated.
         */

        return true;
    }

    /**
     * Function that updates the embedded links in fields like description
     *
     * @param $mapping array Each key(old_id) is mapped to its new object (an object is needed to calculate the security
     *        code)
     */
    public function update_include_links(array $mapping)
    {
        if (count($this->get_includes()) == 0)
        {
            return;
        }

        $fields = static::get_html_editors();

        foreach ($mapping as $old_id => $new_object)
        {
            $pattern = '/core\.php\?go=document_downloader&amp;display=1&amp;object=' . $old_id .
                '(&amp;security_code=[^\&]+)?&amp;application=repository/';

            $security_code = $new_object->calculate_security_code();

            $replacement_string = 'core.php?go=document_downloader&amp;display=1&amp;object=' . $new_object->get_id() .
                '&amp;security_code=' . $security_code . '&amp;application=repository';

            foreach ($fields as $field)
            {
                $value = $this->getDefaultProperty($field);
                $value = preg_replace($pattern, $replacement_string, $value);
                $this->setDefaultProperty($field, $value);
            }

            $this->process_additional_include_links($pattern, $replacement_string);
        }

        $this->update();
    }

    public function version()
    {
        return $this->create();
    }

    public function version_delete()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectAttachment::class, ContentObjectAttachment::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($this->get_id())
        );

        if (!DataManager::deletes(ContentObjectAttachment::class, $condition))
        {
            return false;
        }

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectInclude::class, ContentObjectInclude::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($this->get_id())
        );

        if (!DataManager::deletes(ContentObjectInclude::class, $condition))
        {
            return false;
        }

        $external_sync = $this->get_synchronization_data();
        if ($external_sync instanceof SynchronizationData)
        {
            if (!$external_sync->delete())
            {
                return false;
            }
        }

        return parent::delete();
    }
}
