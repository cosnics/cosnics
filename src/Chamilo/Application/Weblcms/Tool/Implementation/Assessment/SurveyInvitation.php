<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment;

/**
 *
 * @package application.lib.weblcms.tool.assessment
 */
class SurveyInvitation
{
    const PROPERTY_ID = 'id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_SURVEY_ID = 'survey_id';
    const PROPERTY_INVITATION_CODE = 'invitation_code';
    const PROPERTY_EMAIL = 'email';
    const PROPERTY_VALID = 'valid';
    const TABLE_NAME = 'survey_invitation';

    private $default_properties;

    public function __construct($id = null, $default_properties = array())
    {
        $this->set_id($id);
        $this->default_properties = $default_properties;
    }

    public function get_default_property_names()
    {
        return array(
            self::PROPERTY_ID,
            self::PROPERTY_USER_ID,
            self::PROPERTY_SURVEY_ID,
            self::PROPERTY_INVITATION_CODE,
            self::PROPERTY_EMAIL,
            self::PROPERTY_VALID);
    }

    public function set_default_property($name, $value)
    {
        $this->default_properties[$name] = $value;
    }

    public function get_default_property($name)
    {
        return $this->default_properties[$name];
    }

    public function get_default_properties()
    {
        return $this->default_properties;
    }

    public function set_default_properties($properties)
    {
        $this->default_properties = $properties;
    }

    public function get_id()
    {
        return $this->get_default_property(self::PROPERTY_ID);
    }

    public function set_id($value)
    {
        $this->set_default_property(self::PROPERTY_ID, $value);
    }

    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    public function set_user_id($value)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $value);
    }

    public function get_survey_id()
    {
        return $this->get_default_property(self::PROPERTY_SURVEY_ID);
    }

    public function set_survey_id($value)
    {
        $this->set_default_property(self::PROPERTY_SURVEY_ID, $value);
    }

    public function get_invitation_code()
    {
        return $this->get_default_property(self::PROPERTY_INVITATION_CODE);
    }

    public function set_invitation_code($value)
    {
        $this->set_default_property(self::PROPERTY_INVITATION_CODE, $value);
    }

    public function get_valid()
    {
        return $this->get_default_property(self::PROPERTY_VALID);
    }

    public function set_valid($value)
    {
        $this->set_default_property(self::PROPERTY_VALID, $value);
    }

    public function get_email()
    {
        return $this->get_default_property(self::PROPERTY_EMAIL);
    }

    public function set_email($value)
    {
        $this->set_default_property(self::PROPERTY_EMAIL, $value);
    }
}
