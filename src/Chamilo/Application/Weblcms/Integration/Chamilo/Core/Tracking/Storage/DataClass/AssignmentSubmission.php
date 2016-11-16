<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

class AssignmentSubmission extends \Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker
{
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_SUBMITTER_ID = 'submitter_id';
    const PROPERTY_DATE_SUBMITTED = 'date_submitted';
    const PROPERTY_SUBMITTER_TYPE = 'submitter_type';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_IP_ADDRESS = 'ip_address';
    const SUBMITTER_TYPE_USER = 0;
    const SUBMITTER_TYPE_COURSE_GROUP = 1;
    const SUBMITTER_TYPE_PLATFORM_GROUP = 2;

    public function validate_parameters(array $parameters = array())
    {
        $this->set_publication_id($parameters[self::PROPERTY_PUBLICATION_ID]);
        $this->set_content_object_id($parameters[self::PROPERTY_CONTENT_OBJECT_ID]);
        $this->set_submitter_id($parameters[self::PROPERTY_SUBMITTER_ID]);
        $this->set_date_submitted($parameters[self::PROPERTY_DATE_SUBMITTED]);
        $this->set_submitter_type($parameters[self::PROPERTY_SUBMITTER_TYPE]);
        $this->set_user_id($parameters[self::PROPERTY_USER_ID]);
        $this->set_ip_address($parameters[self::PROPERTY_IP_ADDRESS]);
    }

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_PUBLICATION_ID, 
                self::PROPERTY_CONTENT_OBJECT_ID, 
                self::PROPERTY_SUBMITTER_ID, 
                self::PROPERTY_DATE_SUBMITTED, 
                self::PROPERTY_SUBMITTER_TYPE, 
                self::PROPERTY_USER_ID, 
                self::PROPERTY_IP_ADDRESS));
    }

    public function get_publication_id()
    {
        return $this->get_default_property(self::PROPERTY_PUBLICATION_ID);
    }

    public function set_publication_id($publication_id)
    {
        $this->set_default_property(self::PROPERTY_PUBLICATION_ID, $publication_id);
    }

    public function get_content_object_id()
    {
        return $this->get_default_property(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    public function set_content_object_id($content_object_id)
    {
        $this->set_default_property(self::PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    public function get_submitter_id()
    {
        return $this->get_default_property(self::PROPERTY_SUBMITTER_ID);
    }

    public function set_submitter_id($submitter_id)
    {
        $this->set_default_property(self::PROPERTY_SUBMITTER_ID, $submitter_id);
    }

    public function get_date_submitted()
    {
        return $this->get_default_property(self::PROPERTY_DATE_SUBMITTED);
    }

    public function set_date_submitted($date_submitted)
    {
        $this->set_default_property(self::PROPERTY_DATE_SUBMITTED, $date_submitted);
    }

    public function get_submitter_type()
    {
        return $this->get_default_property(self::PROPERTY_SUBMITTER_TYPE);
    }

    public function set_submitter_type($submitter_type)
    {
        $this->set_default_property(self::PROPERTY_SUBMITTER_TYPE, $submitter_type);
    }

    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    public function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }

    public function get_ip_address()
    {
        return $this->get_default_property(self::PROPERTY_IP_ADDRESS);
    }

    public function set_ip_address($ip_address)
    {
        $this->set_default_property(self::PROPERTY_IP_ADDRESS, $ip_address);
    }

    public function get_content_object()
    {
        try
        {
            return \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $this->get_content_object_id());
        }
        catch (\Exception $ex)
        {
            return null;
        }
    }
}
