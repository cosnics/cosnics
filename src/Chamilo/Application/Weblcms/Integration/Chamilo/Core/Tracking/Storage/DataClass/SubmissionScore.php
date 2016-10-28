<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

class SubmissionScore extends \Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker
{
    const PROPERTY_SCORE = 'score';
    const PROPERTY_SUBMISSION_ID = 'submission_id';
    const PROPERTY_CREATED = 'created';
    const PROPERTY_MODIFIED = 'modified';
    const PROPERTY_USER_ID = 'user_id';

    public function validate_parameters(array $parameters = array())
    {
        $this->set_score($parameters[self :: PROPERTY_SCORE]);
        $this->set_submission_id($parameters[self :: PROPERTY_SUBMISSION_ID]);
        $this->set_created($parameters[self :: PROPERTY_CREATED]);
        $this->set_modified($parameters[self :: PROPERTY_MODIFIED]);
        $this->set_user_id($parameters[self :: PROPERTY_USER_ID]);
    }

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_SCORE,
                self :: PROPERTY_SUBMISSION_ID,
                self :: PROPERTY_CREATED,
                self :: PROPERTY_MODIFIED,
                self :: PROPERTY_USER_ID));
    }

    public function get_score()
    {
        return $this->get_default_property(self :: PROPERTY_SCORE);
    }

    public function set_score($score)
    {
        $this->set_default_property(self :: PROPERTY_SCORE, $score);
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

    public function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    public function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }
}
