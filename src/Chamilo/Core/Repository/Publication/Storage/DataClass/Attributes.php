<?php
namespace Chamilo\Core\Repository\Publication\Storage\DataClass;

use Chamilo\Core\Repository\Publication\Storage\DataManager\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package repository.lib
 */
/**
 * Instances of this class group generic information about a publication of an object within an application.
 *
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

    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self::PROPERTY_TITLE;
        $extended_property_names[] = self::PROPERTY_APPLICATION;
        $extended_property_names[] = self::PROPERTY_LOCATION;
        $extended_property_names[] = self::PROPERTY_DATE;
        $extended_property_names[] = self::PROPERTY_CONTENT_OBJECT_ID;
        $extended_property_names[] = self::PROPERTY_PUBLISHER_ID;
        $extended_property_names[] = self::PROPERTY_URL;

        return parent::get_default_property_names($extended_property_names);
    }

    public function get_title()
    {
        return $this->get_default_property(self::PROPERTY_TITLE);
    }

    public function set_title($title)
    {
        $this->set_default_property(self::PROPERTY_TITLE, $title);
    }

    public function get_application()
    {
        return $this->get_default_property(self::PROPERTY_APPLICATION);
    }

    public function set_application($application)
    {
        $this->set_default_property(self::PROPERTY_APPLICATION, $application);
    }

    public function get_location()
    {
        return $this->get_default_property(self::PROPERTY_LOCATION);
    }

    public function set_location($location)
    {
        $this->set_default_property(self::PROPERTY_LOCATION, $location);
    }

    public function get_date()
    {
        return $this->get_default_property(self::PROPERTY_DATE);
    }

    public function set_date($date)
    {
        $this->set_default_property(self::PROPERTY_DATE, $date);
    }

    public function get_content_object_id()
    {
        return $this->get_default_property(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    public function set_content_object_id($content_object_id)
    {
        $this->set_default_property(self::PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    public function get_publisher_id()
    {
        return $this->get_default_property(self::PROPERTY_PUBLISHER_ID);
    }

    public function set_publisher_id($publisher_id)
    {
        $this->set_default_property(self::PROPERTY_PUBLISHER_ID, $publisher_id);
    }

    public function get_url()
    {
        return $this->get_default_property(self::PROPERTY_URL);
    }

    public function set_url($url)
    {
        $this->set_default_property(self::PROPERTY_URL, $url);
    }

    public function get_content_object()
    {
        return \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(),
            $this->get_content_object_id());
    }

    public function update()
    {
        $success = DataManager::update_content_object_publication_id($this);
        if (! $success)
        {
            return false;
        }
        return true;
    }

    /**
     * * BACKWARDS COMPATIBILITY WRAPPER METHODS **
     */

    /**
     *
     * @deprecated
     *
     *
     */
    public function set_publisher_user_id($id)
    {
        $this->set_publisher_id($id);
    }

    /**
     *
     * @deprecated
     *
     *
     */
    public function get_publisher_user_id()
    {
        return $this->get_publisher_id();
    }

    /**
     *
     * @deprecated
     *
     *
     */
    public function set_publication_object_id($id)
    {
        $this->set_content_object_id($id);
    }

    /**
     *
     * @deprecated
     *
     *
     */
    public function get_publication_object_id()
    {
        return $this->get_content_object_id();
    }

    /**
     *
     * @deprecated
     *
     *
     */
    public function set_publication_date($date)
    {
        $this->set_date($date);
    }

    /**
     *
     * @deprecated
     *
     *
     */
    public function get_publication_date()
    {
        return $this->get_date();
    }
}