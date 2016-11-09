<?php
namespace Chamilo\Application\CasStorage\Account\Storage\DataClass;

use Chamilo\Application\CasStorage\Account\Storage\DataManager;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @author Hans De Bisschop
 */
class Account extends DataClass
{

    /**
     * CasAccount properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_FIRST_NAME = 'firstname';
    const PROPERTY_LAST_NAME = 'lastname';
    const PROPERTY_EMAIL = 'email';
    const PROPERTY_AFFILIATION = 'affiliation';
    const PROPERTY_PASSWORD = 'password';
    const PROPERTY_GROUP = 'group';
    const PROPERTY_VALID_FROM = 'valid_from';
    const PROPERTY_VALID_UNTIL = 'valid_until';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_PERSON_ID = 'person_id';
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_FIRST_NAME,
                self :: PROPERTY_LAST_NAME,
                self :: PROPERTY_EMAIL,
                self :: PROPERTY_AFFILIATION,
                self :: PROPERTY_PASSWORD,
                self :: PROPERTY_GROUP,
                self :: PROPERTY_STATUS,
                self :: PROPERTY_VALID_FROM,
                self :: PROPERTY_VALID_UNTIL,
                self :: PROPERTY_PERSON_ID));
    }

    public function get_data_manager()
    {
        return DataManager :: getInstance();
    }

    public function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    public function get_first_name()
    {
        return $this->get_default_property(self :: PROPERTY_FIRST_NAME);
    }

    public function get_last_name()
    {
        return $this->get_default_property(self :: PROPERTY_LAST_NAME);
    }

    public function get_email()
    {
        return $this->get_default_property(self :: PROPERTY_EMAIL);
    }

    public function get_affiliation()
    {
        return $this->get_default_property(self :: PROPERTY_AFFILIATION);
    }

    public function get_password()
    {
        return $this->get_default_property(self :: PROPERTY_PASSWORD);
    }

    public function get_group()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP);
    }

    public function get_valid_from()
    {
        return $this->get_default_property(self :: PROPERTY_VALID_FROM);
    }

    public function get_valid_until()
    {
        return $this->get_default_property(self :: PROPERTY_VALID_UNTIL);
    }

    public function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    public function get_person_id()
    {
        return $this->get_default_property(self :: PROPERTY_PERSON_ID);
    }

    public function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    public function set_first_name($first_name)
    {
        $this->set_default_property(self :: PROPERTY_FIRST_NAME, $first_name);
    }

    public function set_last_name($last_name)
    {
        $this->set_default_property(self :: PROPERTY_LAST_NAME, $last_name);
    }

    public function set_email($email)
    {
        $this->set_default_property(self :: PROPERTY_EMAIL, $email);
    }

    public function set_affiliation($affiliation)
    {
        $this->set_default_property(self :: PROPERTY_AFFILIATION, $affiliation);
    }

    public function set_password($password)
    {
        $this->set_default_property(self :: PROPERTY_PASSWORD, $password);
    }

    public function set_group($group)
    {
        $this->set_default_property(self :: PROPERTY_GROUP, $group);
    }

    public function set_valid_from($valid_from)
    {
        $this->set_default_property(self :: PROPERTY_VALID_FROM, $valid_from);
    }

    public function set_valid_until($valid_until)
    {
        $this->set_default_property(self :: PROPERTY_VALID_UNTIL, $valid_until);
    }

    public function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    public function set_person_id($person_id)
    {
        $this->set_default_property(self :: PROPERTY_PERSON_ID, $person_id);
    }

    public static function get_table_name()
    {
        return PlatformSetting :: get('table', __NAMESPACE__);
    }

    public function get_status_icon()
    {
        switch ($this->get_status())
        {
            case self :: STATUS_ENABLED :
                $path = Theme :: getInstance()->getImagePath(
                    'Chamilo\Application\CasStorage\Account',
                    self :: PROPERTY_STATUS . '/enabled');
                break;
            case self :: STATUS_DISABLED :
                $path = Theme :: getInstance()->getImagePath(
                    'Chamilo\Application\CasStorage\Account',
                    self :: PROPERTY_STATUS . '/disabled');
                break;
        }

        return '<img src="' . $path . '" />';
    }

    public function is_enabled()
    {
        return $this->get_status() == self :: STATUS_ENABLED;
    }
}
