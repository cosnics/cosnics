<?php

namespace Chamilo\Core\Repository\Publication\Storage\DataClass;

use Chamilo\Core\Repository\Publication\Storage\DataManager\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Instances of this class group generic information about a publication of an object within an application.
 *
 * @package Chamilo\Core\Repository\Publication\Storage\DataClass
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class Attributes extends DataClass
{
    const PROPERTY_TITLE = 'title';
    const PROPERTY_APPLICATION = 'application';
    const PROPERTY_LOCATION = 'location';
    const PROPERTY_DATE = 'date';
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_PUBLISHER_ID = 'publisher_id';
    const PROPERTY_URL = 'url';
    const PROPERTY_PUBLICATION_CONTEXT = 'publication_context';
    const PROPERTY_MODIFIER_SERVICE_ID = 'modifier_service_id';

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_TITLE;
        $extendedPropertyNames[] = self::PROPERTY_APPLICATION;
        $extendedPropertyNames[] = self::PROPERTY_LOCATION;
        $extendedPropertyNames[] = self::PROPERTY_DATE;
        $extendedPropertyNames[] = self::PROPERTY_CONTENT_OBJECT_ID;
        $extendedPropertyNames[] = self::PROPERTY_PUBLISHER_ID;
        $extendedPropertyNames[] = self::PROPERTY_URL;
        $extendedPropertyNames[] = self::PROPERTY_PUBLICATION_CONTEXT;
        $extendedPropertyNames[] = self::PROPERTY_MODIFIER_SERVICE_ID;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * @return string
     */
    public function get_title()
    {
        return $this->getDefaultProperty(self::PROPERTY_TITLE);
    }

    /**
     * @param string $title
     */
    public function set_title($title)
    {
        $this->setDefaultProperty(self::PROPERTY_TITLE, $title);
    }

    /**
     * @return string
     */
    public function get_application()
    {
        return $this->getDefaultProperty(self::PROPERTY_APPLICATION);
    }

    /**
     * @param string $application
     */
    public function set_application($application)
    {
        $this->setDefaultProperty(self::PROPERTY_APPLICATION, $application);
    }

    /**
     * @return string
     */
    public function get_location()
    {
        return $this->getDefaultProperty(self::PROPERTY_LOCATION);
    }

    /**
     * @param string $location
     */
    public function set_location($location)
    {
        $this->setDefaultProperty(self::PROPERTY_LOCATION, $location);
    }

    /**
     * @return integer
     */
    public function get_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_DATE);
    }

    /**
     * @param integer $date
     */
    public function set_date($date)
    {
        $this->setDefaultProperty(self::PROPERTY_DATE, $date);
    }

    /**
     * @return integer
     */
    public function get_content_object_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * @param integer $content_object_id
     */
    public function set_content_object_id($content_object_id)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    /**
     * @return integer
     */
    public function get_publisher_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLISHER_ID);
    }

    /**
     * @param integer $publisher_id
     */
    public function set_publisher_id($publisher_id)
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLISHER_ID, $publisher_id);
    }

    /**
     * @return string
     */
    public function get_url()
    {
        return $this->getDefaultProperty(self::PROPERTY_URL);
    }

    /**
     * @param string $url
     */
    public function set_url($url)
    {
        $this->setDefaultProperty(self::PROPERTY_URL, $url);
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function get_content_object()
    {
        return \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class,
            $this->get_content_object_id()
        );
    }

    /**
     * @param string $publicationContext
     */
    public function setPublicationContext($publicationContext)
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLICATION_CONTEXT, $publicationContext);
    }

    /**
     * @return string
     */
    public function getPublicationContext()
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLICATION_CONTEXT);
    }

    /**
     * @param string $modifierServiceIdentifier
     */
    public function setModifierServiceIdentifier($modifierServiceIdentifier)
    {
        $this->setDefaultProperty(self::PROPERTY_MODIFIER_SERVICE_ID, $modifierServiceIdentifier);
    }

    /**
     * @return string
     */
    public function getModifierServiceIdentifier()
    {
        return $this->getDefaultProperty(self::PROPERTY_MODIFIER_SERVICE_ID);
    }

    /**
     * @return bool
     */
    public function update(): bool
    {
        $success = DataManager::update_content_object_publication_id($this);
        if (!$success)
        {
            return false;
        }

        return true;
    }

    /**
     * * BACKWARDS COMPATIBILITY WRAPPER METHODS **
     */

    /**
     * @param integer $id
     *
     * @deprecated Use set_publisher_id($id)
     */
    public function set_publisher_user_id($id)
    {
        $this->set_publisher_id($id);
    }

    /**
     * @return integer
     * @deprecated Use get_publisher_id()
     */
    public function get_publisher_user_id()
    {
        return $this->get_publisher_id();
    }

    /**
     * @param integer $id
     *
     * @deprecated Use set_content_object_id($id)
     */
    public function set_publication_object_id($id)
    {
        $this->set_content_object_id($id);
    }

    /**
     * @return integer
     * @deprecated Use get_content_object_id()
     */
    public function get_publication_object_id()
    {
        return $this->get_content_object_id();
    }

    /**
     * @param integer $date
     *
     * @deprecated Use set_date($date)
     */
    public function set_publication_date($date)
    {
        $this->set_date($date);
    }

    /**
     * @return integer
     * @deprecated Use get_date()
     */
    public function get_publication_date()
    {
        return $this->get_date();
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_publication_attributes';
    }
}