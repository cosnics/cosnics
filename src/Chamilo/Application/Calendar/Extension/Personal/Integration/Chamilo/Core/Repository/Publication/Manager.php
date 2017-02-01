<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Core\Repository\Publication;

use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataManager;
use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Publication\Location\Location;
use Chamilo\Core\Repository\Publication\Location\Locations;
use Chamilo\Core\Repository\Publication\LocationSupport;
use Chamilo\Core\Repository\Publication\PublicationInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class Manager implements PublicationInterface
{

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::is_content_object_editable()
     */
    public static function is_content_object_editable($object_id)
    {
        return true;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::content_object_is_published()
     */
    public static function content_object_is_published($object_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_CONTENT_OBJECT_ID), 
            new StaticConditionVariable($object_id));
        
        $count = DataManager::count(Publication::class_name(), new DataClassCountParameters($condition));
        
        return $count >= 1;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::any_content_object_is_published()
     */
    public static function any_content_object_is_published($object_ids)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_CONTENT_OBJECT_ID), 
            $object_ids);
        
        $count = DataManager::count(Publication::class_name(), new DataClassCountParameters($condition));
        
        return $count >= 1;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::get_content_object_publication_attributes()
     */
    public static function get_content_object_publication_attributes($object_id, $type = self::ATTRIBUTES_TYPE_OBJECT, $condition = null, $count = null, 
        $offset = null, $order_properties = null)
    {
        switch ($type)
        {
            case PublicationInterface::ATTRIBUTES_TYPE_OBJECT :
                $publication_condition = new EqualityCondition(
                    new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_CONTENT_OBJECT_ID), 
                    new StaticConditionVariable($object_id));
                break;
            case PublicationInterface::ATTRIBUTES_TYPE_USER :
                $publication_condition = new EqualityCondition(
                    new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_PUBLISHER), 
                    new StaticConditionVariable($object_id));
                break;
            default :
                return array();
        }
        
        if ($condition instanceof Condition)
        {
            $condition = new AndCondition(array($condition, $publication_condition));
        }
        else
        {
            $condition = $publication_condition;
        }
        
        $result = self::retrieve_content_object_publications($condition, $order_properties, $offset, $count);
        
        $publication_attributes = array();
        
        while ($record = $result->next_result())
        {
            $publication_attributes[] = self::create_publication_attributes_from_record($record);
        }
        
        return $publication_attributes;
    }

    /**
     * Retrieves content object publications joined with the repository content object table
     * 
     * @param \libraries\storage\Condition $condition
     * @param \libraries\ObjectTableOrder[] $order_by
     * @param int $offset
     * @param int $max_objects
     *
     * @return \libraries\storage\ResultSet
     */
    public static function retrieve_content_object_publications($condition = null, $order_by = array(), $offset = 0, 
        $max_objects = -1)
    {
        $data_class_properties = array();
        
        $data_class_properties[] = new PropertiesConditionVariable(Publication::class_name());
        
        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class_name(), 
            ContentObject::PROPERTY_TITLE);
        
        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class_name(), 
            ContentObject::PROPERTY_DESCRIPTION);
        
        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class_name(), 
            ContentObject::PROPERTY_TYPE);
        
        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class_name(), 
            ContentObject::PROPERTY_CURRENT);
        
        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class_name(), 
            ContentObject::PROPERTY_OWNER_ID);
        
        $properties = new DataClassProperties($data_class_properties);
        
        $parameters = new RecordRetrievesParameters(
            $properties, 
            $condition, 
            $max_objects, 
            $offset, 
            $order_by, 
            self::get_content_object_publication_joins());
        
        return DataManager::records(Publication::class_name(), $parameters);
    }

    /**
     * Returns the joins for the content object publication with the content object table
     * 
     * @return \libraries\storage\Joins
     */
    protected static function get_content_object_publication_joins()
    {
        $joins = array();
        
        $joins[] = new Join(
            ContentObject::class_name(), 
            new EqualityCondition(
                new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_CONTENT_OBJECT_ID), 
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID)));
        
        return new Joins($joins);
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::get_content_object_publication_attribute()
     */
    public static function get_content_object_publication_attribute($publication_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_ID), 
            new StaticConditionVariable($publication_id));
        $result = self::retrieve_content_object_publications($condition);
        
        return self::create_publication_attributes_from_record($result->next_result());
    }

    /**
     * Creates a publication attributes object from a given record
     * 
     * @param $record
     * @return \core\repository\publication\storage\data_class\Attributes
     */
    protected static function create_publication_attributes_from_record($record)
    {
        $attributes = new \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes();
        
        $attributes->set_id($record[Publication::PROPERTY_ID]);
        $attributes->set_publisher_id($record[Publication::PROPERTY_PUBLISHER]);
        $attributes->set_date($record[Publication::PROPERTY_PUBLISHED]);
        $attributes->set_application(\Chamilo\Application\Calendar\Extension\Personal\Manager::context());
        $attributes->set_location(Translation::get('TypeName', null, \Chamilo\Application\Calendar\Manager::context()));
        $attributes->set_url(
            'index.php?application=Chamilo\Application\Calendar&amp;go=view&personal_calendar=' . $attributes->get_id());
        
        $attributes->set_title($record[ContentObject::PROPERTY_TITLE]);
        $attributes->set_content_object_id($record[Publication::PROPERTY_CONTENT_OBJECT_ID]);
        
        return $attributes;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::count_publication_attributes()
     */
    public static function count_publication_attributes($attributes_type = self::ATTRIBUTES_TYPE_OBJECT, $identifier, $condition = null)
    {
        switch ($attributes_type)
        {
            case PublicationInterface::ATTRIBUTES_TYPE_OBJECT :
                $publication_condition = new EqualityCondition(
                    new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_CONTENT_OBJECT_ID), 
                    new StaticConditionVariable($identifier));
                break;
            case PublicationInterface::ATTRIBUTES_TYPE_USER :
                $publication_condition = new EqualityCondition(
                    new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_PUBLISHER), 
                    new StaticConditionVariable($identifier));
                break;
            default :
                return 0;
        }
        
        if ($condition instanceof Condition)
        {
            $condition = new AndCondition(array($condition, $publication_condition));
        }
        else
        {
            $condition = $publication_condition;
        }
        
        $parameters = new DataClassCountParameters($condition, self::get_content_object_publication_joins());
        
        return DataManager::count(Publication::class_name(), $parameters);
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::delete_content_object_publications()
     */
    public static function delete_content_object_publications($object_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_CONTENT_OBJECT_ID), 
            new StaticConditionVariable($object_id));
        
        return DataManager::deletes(Publication::class_name(), $condition);
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::delete_content_object_publication()
     */
    public static function delete_content_object_publication($publication_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_ID), 
            new StaticConditionVariable($publication_id));
        
        return DataManager::deletes(Publication::class_name(), $condition);
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::get_content_object_publication_locations()
     */
    public static function get_content_object_publication_locations($content_object, $user = null)
    {
        $applicationContext = \Chamilo\Application\Calendar\Extension\Personal\Manager::context();
        
        $locations = new Locations(__NAMESPACE__);
        $allowed_types = self::get_allowed_content_object_types();
        
        $type = $content_object->get_type();
        
        if (in_array($type, $allowed_types))
        {
            $locations->add_location(
                new Location($applicationContext, Translation::get('TypeName', null, $applicationContext)));
        }
        
        return $locations;
    }

    public static function get_allowed_content_object_types()
    {
        $registrations = Configuration::getInstance()->getIntegrationRegistrations(
            \Chamilo\Application\Calendar\Extension\Personal\Manager::package(), 
            \Chamilo\Core\Repository\Manager::package() . '\ContentObject');
        $types = array();
        
        foreach ($registrations as $registration)
        {
            $namespace = ClassnameUtilities::getInstance()->getNamespaceParent(
                $registration[Registration::PROPERTY_CONTEXT], 
                6);
            $types[] = $namespace . '\Storage\DataClass\\' .
                 ClassnameUtilities::getInstance()->getPackageNameFromNamespace($namespace);
        }
        
        return $types;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::publish_content_object()
     */
    public static function publish_content_object(
        \Chamilo\Core\Repository\Storage\DataClass\ContentObject $content_object, LocationSupport $location, 
        $options = array())
    {
        $publication = new Publication();
        $publication->set_content_object_id($content_object->get_id());
        $publication->set_publisher($content_object->get_owner_id());
        
        if (! $publication->create())
        {
            return false;
        }
        else
        {
            return $publication;
        }
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::add_publication_attributes_elements()
     */
    public static function add_publication_attributes_elements($form)
    {
        // TODO: Please implement me !
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::update_content_object_publication_id()
     */
    public static function update_content_object_publication_id($publication_attributes)
    {
        $publication = DataManager::retrieve_by_id(Publication::class_name(), $publication_attributes->get_id());
        
        if ($publication instanceof Publication)
        {
            $publication->set_content_object_id($publication_attributes->get_content_object_id());
            return $publication->update();
        }
        else
        {
            return false;
        }
    }
}
