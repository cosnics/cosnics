<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Core\Repository\Publication;

use Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Core\Repository\Publication\Service\PublicationModifier;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataManager;
use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Publication\Location\Location;
use Chamilo\Core\Repository\Publication\Location\Locations;
use Chamilo\Core\Repository\Publication\LocationSupport;
use Chamilo\Core\Repository\Publication\multitype;
use Chamilo\Core\Repository\Publication\PublicationInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
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
use Chamilo\Libraries\Translation\Translation;

class Manager implements PublicationInterface
{
    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::isContentObjectPublished()
     */

    /**
     *
     * @param \libraries\format\FormValidator $form
     */
    public static function add_publication_attributes_elements($form)
    {
        // TODO: Implement add_publication_attributes_elements() method.
    }

    /*
 * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::update_content_object_publication_id()
 */

    public static function isContentObjectPublished($object_id)
    {
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::getContentObjectPublicationsAttributes()
     */

    public static function areContentObjectsPublished($object_ids)
    {
    }

    /**
     * Determines whether the given content object can be edited in the implementing context
     *
     * @param int $object_id
     *
     * @return boolean
     */
    public static function canContentObjectBeEdited($object_id)
    {
        // TODO: Implement canContentObjectBeEdited() method.
    }

    public static function countPublicationAttributes(
        $attributes_type = self::ATTRIBUTES_TYPE_OBJECT, $identifier, $condition = null
    )
    {
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::get_content_object_publication_attribute()
     */

    /**
     * Creates a publication attributes object from a given record
     *
     * @param $record
     *
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
            'index.php?application=Chamilo\Application\Calendar\Extension\Personal&amp;go=view&publication_id=' .
            $attributes->get_id()
        );

        $attributes->set_title($record[ContentObject::PROPERTY_TITLE]);
        $attributes->set_content_object_id($record[Publication::PROPERTY_CONTENT_OBJECT_ID]);
        $attributes->setModifierServiceIdentifier(PublicationModifier::class);

        return $attributes;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::countPublicationAttributes()
     */

    public static function deleteContentObjectPublications($object_id)
    {
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::deleteContentObjectPublications()
     */

    public static function delete_content_object_publication($publication_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_ID),
            new StaticConditionVariable($publication_id)
        );

        return DataManager::deletes(Publication::class_name(), $condition);
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::delete_content_object_publication()
     */

    /**
     *
     * @param \core\repository\ContentObject $content_object
     *
     * @return multitype:mixed
     */
    public static function getContentObjectPublicationLocations($content_object, $user = null)
    {
        // TODO: Implement getContentObjectPublicationLocations() method.
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::getContentObjectPublicationLocations()
     */

    public static function getContentObjectPublicationsAttributes(
        $object_id, $type = self::ATTRIBUTES_TYPE_OBJECT, $condition = null, $count = null, $offset = null,
        $order_properties = null
    )
    {
    }

    public static function get_allowed_content_object_types()
    {
        $registrations = Configuration::getInstance()->getIntegrationRegistrations(
            \Chamilo\Application\Calendar\Extension\Personal\Manager::package(),
            \Chamilo\Core\Repository\Manager::package() . '\ContentObject'
        );
        $types = array();

        foreach ($registrations as $registration)
        {
            $namespace = ClassnameUtilities::getInstance()->getNamespaceParent(
                $registration[Registration::PROPERTY_CONTEXT], 6
            );
            $types[] = $namespace . '\Storage\DataClass\\' .
                ClassnameUtilities::getInstance()->getPackageNameFromNamespace($namespace);
        }

        return $types;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::publish_content_object()
     */

    public static function get_content_object_publication_attribute($publication_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_ID),
            new StaticConditionVariable($publication_id)
        );
        $result = self::retrieve_content_object_publications($condition);

        return self::create_publication_attributes_from_record($result->next_result());
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::add_publication_attributes_elements()
     */

    /**
     * Returns the joins for the content object publication with the content object table
     *
     * @return \libraries\storage\Joins
     */
    protected static function get_content_object_publication_joins()
    {
        $joins = array();

        $joins[] = new Join(
            ContentObject::class_name(), new EqualityCondition(
                new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_CONTENT_OBJECT_ID),
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID)
            )
        );

        return new Joins($joins);
    }

    public static function publish_content_object(
        \Chamilo\Core\Repository\Storage\DataClass\ContentObject $content_object, LocationSupport $location,
        $options = array()
    )
    {
        $publication = new Publication();
        $publication->set_content_object_id($content_object->get_id());
        $publication->set_publisher($content_object->get_owner_id());

        if (!$publication->create())
        {
            return false;
        }
        else
        {
            return $publication;
        }
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
    public static function retrieve_content_object_publications(
        $condition = null, $order_by = array(), $offset = 0, $max_objects = - 1
    )
    {
        $data_class_properties = array();

        $data_class_properties[] = new PropertiesConditionVariable(Publication::class_name());

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class_name(), ContentObject::PROPERTY_TITLE
        );

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION
        );

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class_name(), ContentObject::PROPERTY_TYPE
        );

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class_name(), ContentObject::PROPERTY_CURRENT
        );

        $data_class_properties[] = new PropertyConditionVariable(
            ContentObject::class_name(), ContentObject::PROPERTY_OWNER_ID
        );

        $properties = new DataClassProperties($data_class_properties);

        $parameters = new RecordRetrievesParameters(
            $properties, $condition, $max_objects, $offset, $order_by, self::get_content_object_publication_joins()
        );

        return DataManager::records(Publication::class_name(), $parameters);
    }

    public static function update_content_object_publication_id($publication_attributes)
    {
    }
}
