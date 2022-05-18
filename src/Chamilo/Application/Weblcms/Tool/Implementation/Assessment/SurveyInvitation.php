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

    public function __construct($id = null, $default_properties = [])
    {
        $this->set_id($id);
        $this->default_properties = $default_properties;
    }

    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return array(
            self::PROPERTY_ID,
            self::PROPERTY_USER_ID,
            self::PROPERTY_SURVEY_ID,
            self::PROPERTY_INVITATION_CODE,
            self::PROPERTY_EMAIL,
            self::PROPERTY_VALID);
    }

    public function setDefaultProperty($name, $value)
    {
        $this->default_properties[$name] = $value;
    }

    public function getDefaultProperty($name)
    {
        return $this->default_properties[$name];
    }

    public function getDefaultProperties()
    {
        return $this->default_properties;
    }

    public function setDefaultProperties($properties)
    {
        $this->default_properties = $properties;
    }

    public function get_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_ID);
    }

    public function set_id($value)
    {
        $this->setDefaultProperty(self::PROPERTY_ID, $value);
    }

    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function set_user_id($value)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $value);
    }

    public function get_survey_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_SURVEY_ID);
    }

    public function set_survey_id($value)
    {
        $this->setDefaultProperty(self::PROPERTY_SURVEY_ID, $value);
    }

    public function get_invitation_code()
    {
        return $this->getDefaultProperty(self::PROPERTY_INVITATION_CODE);
    }

    public function set_invitation_code($value)
    {
        $this->setDefaultProperty(self::PROPERTY_INVITATION_CODE, $value);
    }

    public function get_valid()
    {
        return $this->getDefaultProperty(self::PROPERTY_VALID);
    }

    public function set_valid($value)
    {
        $this->setDefaultProperty(self::PROPERTY_VALID, $value);
    }

    public function get_email()
    {
        return $this->getDefaultProperty(self::PROPERTY_EMAIL);
    }

    public function set_email($value)
    {
        $this->setDefaultProperty(self::PROPERTY_EMAIL, $value);
    }
}
