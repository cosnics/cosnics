<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

class SubmissionFeedback extends \Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker
{
    const PROPERTY_SUBMISSION_ID = 'submission_id';
    const PROPERTY_CREATED = 'created';
    const PROPERTY_MODIFIED = 'modified';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';

    public function validate_parameters(array $parameters = array())
    {
        $this->set_submission_id($parameters[self :: PROPERTY_SUBMISSION_ID]);
        $this->set_created($parameters[self :: PROPERTY_CREATED]);
        $this->set_modified($parameters[self :: PROPERTY_MODIFIED]);
        $this->set_user_id($parameters[self :: PROPERTY_USER_ID]);
        $this->set_content_object_id($parameters[self :: PROPERTY_CONTENT_OBJECT_ID]);
    }

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_SUBMISSION_ID,
                self :: PROPERTY_CREATED,
                self :: PROPERTY_MODIFIED,
                self :: PROPERTY_USER_ID,
                self :: PROPERTY_CONTENT_OBJECT_ID));
    }

    public function get_submission_id()
    {
        return $this->get_default_property(self :: PROPERTY_SUBMISSION_ID);
    }

    public function set_submission_id($submission_id)
    {
        $this->set_default_property(self :: PROPERTY_SUBMISSION_ID, $submission_id);
    }

    public function get_created()
    {
        return $this->get_default_property(self :: PROPERTY_CREATED);
    }

    public function set_created($created)
    {
        $this->set_default_property(self :: PROPERTY_CREATED, $created);
    }

    public function get_modified()
    {
        return $this->get_default_property(self :: PROPERTY_MODIFIED);
    }

    public function set_modified($modified)
    {
        $this->set_default_property(self :: PROPERTY_MODIFIED, $modified);
    }

    public function get_content_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }

    public function set_content_object_id($content_object_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    public function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    public function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    public function get_content_object()
    {
        try
        {
            return \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $this->get_content_object_id());
        }
        catch (\Exception $ex)
        {
            return null;
        }
    }
}
