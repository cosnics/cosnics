<?php
namespace Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication;

use Chamilo\Application\Portfolio\Storage\DataClass\Publication;
use Chamilo\Application\Portfolio\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\ComplexPortfolio;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio;
use Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass\PortfolioItem;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Publication\Location\Locations;
use Chamilo\Core\Repository\Publication\LocationSupport;
use Chamilo\Core\Repository\Publication\PublicationInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
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

/**
 *
 * @package Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Manager implements PublicationInterface
{

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::is_content_object_editable()
     */
    public static function add_publication_attributes_elements($form)
    {
        // TODO: Please implement me !
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::content_object_is_published()
     */

    public static function any_content_object_is_published($object_ids)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_CONTENT_OBJECT_ID),
            $object_ids);
        $parameters = new DataClassCountParameters($condition);
        return DataManager::count(Publication::class_name(), $parameters) > 0;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::any_content_object_is_published()
     */

    public static function content_object_is_published($object_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_CONTENT_OBJECT_ID),
            new StaticConditionVariable($object_id));
        $parameters = new DataClassCountParameters($condition);
        return DataManager::count(Publication::class_name(), $parameters) > 0;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::get_content_object_publication_attributes()
     */

    public static function count_publication_attributes($attributes_type = self :: ATTRIBUTES_TYPE_OBJECT, $identifier = null, $condition = null)
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
                    new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_PUBLISHER_ID),
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
        $attributes->set_publisher_id($record[Publication::PROPERTY_PUBLISHER_ID]);
        $attributes->set_date($record[Publication::PROPERTY_PUBLISHED]);
        $attributes->set_application(\Chamilo\Application\Portfolio\Manager::context());

        $attributes->set_location(Translation::get('TypeName', null, \Chamilo\Application\Portfolio\Manager::context()));

        $url = 'index.php?application=portfolio&amp;go=' . \Chamilo\Application\Portfolio\Manager::ACTION_HOME . '&amp;' .
             \Chamilo\Application\Portfolio\Manager::PARAM_USER_ID . '=' . $record[Publication::PROPERTY_PUBLISHER_ID];

        $attributes->set_url($url);
        $attributes->set_title($record[ContentObject::PROPERTY_TITLE]);
        $attributes->set_content_object_id($record[Publication::PROPERTY_CONTENT_OBJECT_ID]);

        return $attributes;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::get_content_object_publication_attribute()
     */

    public static function delete_content_object_publication($publication_id)
    {
        $publication = DataManager::retrieve_by_id(Publication::class_name(), $publication_id);

        if ($publication instanceof Publication && $publication->delete())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function delete_content_object_publications($object_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_CONTENT_OBJECT_ID),
            new StaticConditionVariable($object_id));
        $parameters = new DataClassRetrievesParameters($condition);

        $publications = DataManager::retrieves(Publication::class_name(), $parameters);

        while ($publication = $publications->next_result())
        {
            if (! $publication->delete())
            {
                return false;
            }
        }

        return true;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::count_publication_attributes()
     */

    public static function get_content_object_publication_attribute($publication_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_ID),
            new StaticConditionVariable($publication_id));
        $record = self::record(
            Publication::class_name(),
            new RecordRetrieveParameters(
                new DataClassProperties(new PropertiesConditionVariable(Publication::class_name())),
                $condition));

        return self::create_publication_attributes_from_record($record);
    }

    public static function get_content_object_publication_attributes($object_id, $type = self :: ATTRIBUTES_TYPE_OBJECT, $condition = null, $count = null,
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
                    new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_PUBLISHER_ID),
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

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::delete_content_object_publications()
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
            ContentObject::class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_CONTENT_OBJECT_ID),
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID)));

        return new Joins($joins);
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::delete_content_object_publication()
     */

    public static function get_content_object_publication_locations($content_object, $user = null)
    {
        $applicationContext = \Chamilo\Application\Portfolio\Manager::context();

        $locations = new Locations(__NAMESPACE__);
        $allowed_types = Portfolio::get_allowed_types();

        $condition = new EqualityCondition(
            new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_PUBLISHER_ID),
            new StaticConditionVariable($user->get_id()));
        $userPublication = DataManager::retrieve(Publication::class_name(), new DataClassRetrieveParameters($condition));

        $type = $content_object->get_type();

        if (in_array($type, $allowed_types) && $userPublication instanceof Publication)
        {
            $locations->add_location(
                new Location(
                    __NAMESPACE__,
                    Translation::get('TypeName', null, $applicationContext),
                    $user->getId(),
                    $userPublication->getId()));
        }

        return $locations;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::get_content_object_publication_locations()
     */

    public static function is_content_object_editable($object_id)
    {
        return true;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::publish_content_object()
     */

    public static function publish_content_object(
        \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject, LocationSupport $location,
        $options = array())
    {
        $publication = DataManager::retrieve_by_id(Publication::class_name(), $location->getPublicationIdentifier());

        if ($publication instanceof Publication && $publication->get_publisher_id() == $location->getUserIdentifier())
        {
            $portfolioContentObject = $publication->get_content_object();
            $portfolioPath = $portfolioContentObject->get_complex_content_object_path();
            $rootNode = $portfolioPath->get_root();

            if ($rootNode->forms_cycle_with($contentObject->getId()))
            {
                return false;
            }

            if (! $contentObject instanceof Portfolio)
            {
                $newObject = ContentObject::factory(PortfolioItem::class_name());
                $newObject->set_owner_id($location->getUserIdentifier());
                $newObject->set_title(PortfolioItem::get_type_name());
                $newObject->set_description(PortfolioItem::get_type_name());
                $newObject->set_parent_id(0);
                $newObject->set_reference($contentObject->getId());
                $newObject->create();
            }
            else
            {
                $newObject = $contentObject;
            }

            if ($newObject instanceof Portfolio)
            {
                $wrapper = new ComplexPortfolio();
            }
            else
            {
                $wrapper = new \Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass\ComplexPortfolioItem();
            }

            $wrapper->set_ref($newObject->get_id());
            $wrapper->set_parent($portfolioContentObject->get_id());
            $wrapper->set_user_id($location->getUserIdentifier());
            $wrapper->set_display_order(
                \Chamilo\Core\Repository\Storage\DataManager::select_next_display_order(
                    $portfolioContentObject->get_id()));

            if (! $wrapper->create())
            {
                return false;
            }
            else
            {
                Event::trigger(
                    'Activity',
                    \Chamilo\Core\Repository\Manager::context(),
                    array(
                        Activity::PROPERTY_TYPE => Activity::ACTIVITY_ADD_ITEM,
                        Activity::PROPERTY_USER_ID => $location->getUserIdentifier(),
                        Activity::PROPERTY_DATE => time(),
                        Activity::PROPERTY_CONTENT_OBJECT_ID => $portfolioContentObject->get_id(),
                        Activity::PROPERTY_CONTENT => $portfolioContentObject->get_title() . ' > ' .
                             $contentObject->get_title()));

                $currentParentsContentObjectIds = $rootNode->get_parents_content_object_ids(true, true);
                $currentParentsContentObjectIds[] = $contentObject->getId();

                $portfolioPath->reset();
                $portfolioPath = $portfolioContentObject->get_complex_content_object_path();
                return $portfolioPath->follow_path_by_content_object_ids($currentParentsContentObjectIds);
            }
        }
        else

        {
            return false;
        }
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::add_publication_attributes_elements()
     */

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

        return DataManager::update_content_object_publication_id($publication_attributes);
    }
}
