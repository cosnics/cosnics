<?php

namespace Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Repository\Publication;

use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Admin\Announcement\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Storage\DataClass\SystemAnnouncement;
use Chamilo\Core\Repository\Publication\Location\Location;
use Chamilo\Core\Repository\Publication\Location\Locations;
use Chamilo\Core\Repository\Publication\LocationSupport;
use Chamilo\Core\Repository\Publication\PublicationInterface;
use Chamilo\Libraries\Translation\Translation;

class Manager implements PublicationInterface
{

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::canContentObjectBeEdited()
     */
    public static function add_publication_attributes_elements($form)
    {
        // TODO: Please implement me !
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::isContentObjectPublished()
     */

    public static function areContentObjectsPublished($object_ids)
    {
        return DataManager::areContentObjectsPublished($object_ids);
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::areContentObjectsPublished()
     */

    public static function canContentObjectBeEdited($object_id)
    {
        // TODO: Please implement me !
        return true;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::getContentObjectPublicationsAttributes()
     */

    public static function countPublicationAttributes(
        $attributes_type = self::ATTRIBUTES_TYPE_OBJECT, $identifier, $condition = null
    )
    {
        return DataManager::countPublicationAttributes($attributes_type, $identifier, $condition);
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::get_content_object_publication_attribute()
     */

    public static function deleteContentObjectPublications($object_id)
    {
        return DataManager::deleteContentObjectPublications($object_id);
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::countPublicationAttributes()
     */

    public static function delete_content_object_publication($publication_id)
    {
        return DataManager::delete_content_object_publication($publication_id);
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::deleteContentObjectPublications()
     */

    public static function getContentObjectPublicationLocations($content_object, $user = null)
    {
        $applicationContext = \Chamilo\Core\Admin\Announcement\Manager::context();

        $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User::class_name(),
            (int) \Chamilo\Libraries\Platform\Session\Session::get_user_id()
        );

        $locations = new Locations(__NAMESPACE__);

        if ($user->is_platform_admin())
        {
            $allowed_types = array(SystemAnnouncement::class_name());

            $type = $content_object->get_type();

            if (in_array($type, $allowed_types))
            {
                $locations->add_location(
                    new Location(
                        $applicationContext, Translation::get('SystemAnnouncements', null, $applicationContext)
                    )
                );
            }
        }

        return array($locations);
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::delete_content_object_publication()
     */

    public static function getContentObjectPublicationsAttributes(
        $object_id, $type = self::ATTRIBUTES_TYPE_OBJECT, $condition = null, $count = null, $offset = null,
        $order_properties = null
    )
    {
        return DataManager::getContentObjectPublicationsAttributes(
            $object_id, $type, $type, $offset, $count, $order_properties
        );
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::getContentObjectPublicationLocations()
     */

    public static function get_content_object_publication_attribute($publication_id)
    {
        return DataManager::get_content_object_publication_attribute($publication_id);
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::publish_content_object()
     */

    public static function isContentObjectPublished($object_id)
    {
        // TODO: Please implement me !
        return false;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::add_publication_attributes_elements()
     */

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

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::update_content_object_publication_id()
     */

    public static function update_content_object_publication_id($publication_attributes)
    {
        // TODO: Please implement me !
        return true;
    }
}
