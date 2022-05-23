<?php
namespace Chamilo\Core\Repository\Publication\Storage\DataManager;

use ArrayIterator;
use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Publication\PublicationInterface;

/**
 *
 * @package core\repository\user_view
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'repository_';

    public static function areContentObjectsPublished($ids)
    {
        $registrations = Configuration::getInstance()->getIntegrationRegistrations('Chamilo\Core\Repository');

        foreach ($registrations as $registration)
        {
            $manager_class = $registration[Registration::PROPERTY_CONTEXT] . '\Publication\Manager';
            $result = $manager_class::areContentObjectsPublished($ids);

            if ($result)
            {
                return true;
            }
        }

        return false;
    }

    public static function canContentObjectBeEdited($id)
    {
        $registrations = Configuration::getInstance()->getIntegrationRegistrations('Chamilo\Core\Repository');

        foreach ($registrations as $registration)
        {
            $manager_class = $registration[Registration::PROPERTY_CONTEXT] . '\Publication\Manager';
            $result = $manager_class::canContentObjectBeEdited($id);

            if (!$result)
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Count the number of publications for a content object
     *
     * @param int $attributes_type
     * @param int $identifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     */
    public static function countPublicationAttributes(
        $attributes_type = PublicationInterface::ATTRIBUTES_TYPE_OBJECT, $identifier, $condition = null
    )
    {
        $registrations = Configuration::getInstance()->getIntegrationRegistrations('Chamilo\Core\Repository');
        $info = 0;

        foreach ($registrations as $registration)
        {
            $manager_class = $registration[Registration::PROPERTY_CONTEXT] . '\Publication\Manager';
            $info += $manager_class::countPublicationAttributes($attributes_type, $identifier, $condition);
        }

        return $info;
    }

    public static function deleteContentObjectPublications($object)
    {
        $registrations = Configuration::getInstance()->getIntegrationRegistrations('Chamilo\Core\Repository');

        foreach ($registrations as $registration)
        {
            $manager_class = $registration[Registration::PROPERTY_CONTEXT] . '\Publication\Manager';
            $result = $manager_class::deleteContentObjectPublications($object->get_id());

            if (!$result)
            {
                return false;
            }
        }

        return true;
    }

    public static function delete_content_object_publication($application, $publication_id, $publicationContext = null)
    {
        $manager_class = $application . '\Integration\Chamilo\Core\Repository\Publication\Manager';

        return $manager_class::delete_content_object_publication($publication_id, $publicationContext);
    }

    /**
     * Determines where in this application the given learning object has been published.
     *
     * @param int $attributes_type
     * @param int $identifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param int $count
     * @param int $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $order_property
     */
    public static function getContentObjectPublicationsAttributes(
        $identifier, $attributes_type = null, $condition = null, $count = null, $offset = null, $order_property = null
    )
    {
        $registrations = Configuration::getInstance()->getIntegrationRegistrations('Chamilo\Core\Repository');
        $publication_attributes = [];

        foreach ($registrations as $registration)
        {
            $manager_class = $registration[Registration::PROPERTY_CONTEXT] . '\Publication\Manager';

            if (class_exists($manager_class))
            {
                $application_attributes = $manager_class::getContentObjectPublicationsAttributes(
                    $identifier, $attributes_type, $condition
                );

                if (!is_null($application_attributes) && count($application_attributes) > 0)
                {
                    $publication_attributes = array_merge($publication_attributes, $application_attributes);
                }
            }
        }

        // Sort the publication attributes
        if (count($order_property) > 0)
        {
            $order_column = $order_property[0]->get_property();
            $order_direction = $order_property[0]->get_direction();
            $ordering_values = [];

            foreach ($publication_attributes as $key => $publication_attribute)
            {
                $ordering_values[$key] = (string) strtolower(
                    $publication_attribute->getDefaultProperty($order_column)
                );
            }

            switch ($order_direction)
            {
                case SORT_ASC :
                    asort($ordering_values);
                    break;
                case SORT_DESC :
                    arsort($ordering_values);
                    break;
            }

            $ordered_publication_attributes = [];

            foreach ($ordering_values as $key => $value)
            {
                $ordered_publication_attributes[] = $publication_attributes[$key];
            }

            $publication_attributes = $ordered_publication_attributes;
        }

        if (isset($offset))
        {
            if (isset($count))
            {
                $publication_attributes = array_splice($publication_attributes, $offset, $count);
            }
            else
            {
                $publication_attributes = array_splice($publication_attributes, $offset);
            }
        }

        // Return the requested subset
        return new ArrayIterator($publication_attributes);
    }

    /**
     * @param int $id
     * @param string $application
     * @param string $publicationContext
     *
     * @return mixed
     */
    public static function get_content_object_publication_attribute($id, $application, $publicationContext = null)
    {
        $manager_class = $application . '\Integration\Chamilo\Core\Repository\Publication\Manager';

        return $manager_class::get_content_object_publication_attribute($id, $publicationContext);
    }

    public static function isContentObjectPublished($id)
    {
        $registrations = Configuration::getInstance()->getIntegrationRegistrations('Chamilo\Core\Repository');

        foreach ($registrations as $registration)
        {
            $manager_class = $registration[Registration::PROPERTY_CONTEXT] . '\Publication\Manager';
            $result = $manager_class::isContentObjectPublished($id);

            if ($result)
            {
                return true;
            }
        }

        return false;
    }

    public static function update_content_object_publication_id($publication_attributes)
    {
        $manager_class =
            $publication_attributes->get_application() . '\Integration\Chamilo\Core\Repository\Publication\Manager';

        return $manager_class::update_content_object_publication_id($publication_attributes);
    }
}
