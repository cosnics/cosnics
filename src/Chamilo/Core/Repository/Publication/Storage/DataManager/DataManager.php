<?php
namespace Chamilo\Core\Repository\Publication\Storage\DataManager;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

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

    /**
     * Determines where in this application the given learning object has been published.
     * 
     * @param int $attributes_type
     * @param int $identifier
     * @param \libraries\storage\Condition $condition
     * @param int $count
     * @param int $offset
     * @param multitype:ObjectTableOrder $order_property
     * @return multitype:mixed
     */
    public static function get_content_object_publication_attributes($identifier, $attributes_type = null, $condition = null, 
        $count = null, $offset = null, $order_property = null)
    {
        $registrations = Configuration::getInstance()->getIntegrationRegistrations('Chamilo\Core\Repository');
        $publication_attributes = array();
        
        foreach ($registrations as $registration)
        {
            $manager_class = $registration[Registration::PROPERTY_CONTEXT] . '\Publication\Manager';
            
            if (class_exists($manager_class))
            {
                $application_attributes = $manager_class::get_content_object_publication_attributes(
                    $identifier, 
                    $attributes_type, 
                    $condition);
                
                if (! is_null($application_attributes) && count($application_attributes) > 0)
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
            $ordering_values = array();
            
            foreach ($publication_attributes as $key => $publication_attribute)
            {
                $ordering_values[$key] = (string) strtolower(
                    $publication_attribute->get_default_property($order_column));
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
            
            $ordered_publication_attributes = array();
            
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
        return new ArrayResultSet($publication_attributes);
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

    /**
     * Count the number of publications for a content object
     * 
     * @param int $attributes_type
     * @param int $identifier
     * @param \libraries\storage\Condition $condition
     * @return multitype:mixed
     */
    public static function count_publication_attributes($attributes_type = self :: ATTRIBUTES_TYPE_OBJECT, $identifier, $condition = null)
    {
        $registrations = Configuration::getInstance()->getIntegrationRegistrations('Chamilo\Core\Repository');
        $info = 0;
        
        foreach ($registrations as $registration)
        {
            $manager_class = $registration[Registration::PROPERTY_CONTEXT] . '\Publication\Manager';
            $info += $manager_class::count_publication_attributes($attributes_type, $identifier, $condition);
        }
        
        return $info;
    }

    public static function update_content_object_publication_id($publication_attributes)
    {
        $manager_class = $publication_attributes->get_application() .
             '\Integration\Chamilo\Core\Repository\Publication\Manager';
        return $manager_class::update_content_object_publication_id($publication_attributes);
    }

    public static function delete_content_object_publications($object)
    {
        $registrations = Configuration::getInstance()->getIntegrationRegistrations('Chamilo\Core\Repository');
        
        foreach ($registrations as $registration)
        {
            $manager_class = $registration[Registration::PROPERTY_CONTEXT] . '\Publication\Manager';
            $result = $manager_class::delete_content_object_publications($object->get_id());
            
            if (! $result)
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

    public static function content_object_is_published($id)
    {
        $registrations = Configuration::getInstance()->getIntegrationRegistrations('Chamilo\Core\Repository');
        
        foreach ($registrations as $registration)
        {
            $manager_class = $registration[Registration::PROPERTY_CONTEXT] . '\Publication\Manager';
            $result = $manager_class::content_object_is_published($id);
            
            if ($result)
            {
                return true;
            }
        }
        
        return false;
    }

    public static function any_content_object_is_published($ids)
    {
        $registrations = Configuration::getInstance()->getIntegrationRegistrations('Chamilo\Core\Repository');
        
        foreach ($registrations as $registration)
        {
            $manager_class = $registration[Registration::PROPERTY_CONTEXT] . '\Publication\Manager';
            $result = $manager_class::any_content_object_is_published($ids);
            
            if ($result)
            {
                return true;
            }
        }
        
        return false;
    }

    public static function is_content_object_editable($id)
    {
        $registrations = Configuration::getInstance()->getIntegrationRegistrations('Chamilo\Core\Repository');
        
        foreach ($registrations as $registration)
        {
            $manager_class = $registration[Registration::PROPERTY_CONTEXT] . '\Publication\Manager';
            $result = $manager_class::is_content_object_editable($id);
            
            if (! $result)
            {
                return false;
            }
        }
        
        return true;
    }
}
