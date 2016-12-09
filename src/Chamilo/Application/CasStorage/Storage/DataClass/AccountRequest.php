<?php
namespace Chamilo\Application\CasStorage\Storage\DataClass;

use Chamilo\Application\CasStorage\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @author Hans De Bisschop
 */
class AccountRequest extends DataClass
{
    const TABLE_NAME = 'request';
    
    /**
     * AccountRequest properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_FIRST_NAME = 'first_name';
    const PROPERTY_LAST_NAME = 'last_name';
    const PROPERTY_EMAIL = 'email';
    const PROPERTY_AFFILIATION = 'affiliation';
    const PROPERTY_MOTIVATION = 'motivation';
    const PROPERTY_REQUESTER_ID = 'requester_id';
    const PROPERTY_REQUEST_DATE = 'requested';
    const PROPERTY_VALID_FROM = 'valid_from';
    const PROPERTY_VALID_UNTIL = 'valid_until';
    const PROPERTY_STATUS = 'status';
    const STATUS_PENDING = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_REJECTED = 3;

    /**
     * Get the default properties
     * 
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_FIRST_NAME, 
                self::PROPERTY_LAST_NAME, 
                self::PROPERTY_EMAIL, 
                self::PROPERTY_AFFILIATION, 
                self::PROPERTY_MOTIVATION, 
                self::PROPERTY_REQUESTER_ID, 
                self::PROPERTY_REQUEST_DATE, 
                self::PROPERTY_STATUS, 
                self::PROPERTY_VALID_FROM, 
                self::PROPERTY_VALID_UNTIL));
    }

    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    public function get_first_name()
    {
        return $this->get_default_property(self::PROPERTY_FIRST_NAME);
    }

    public function get_last_name()
    {
        return $this->get_default_property(self::PROPERTY_LAST_NAME);
    }

    public function get_email()
    {
        return $this->get_default_property(self::PROPERTY_EMAIL);
    }

    public function get_affiliation()
    {
        return $this->get_default_property(self::PROPERTY_AFFILIATION);
    }

    public function get_motivation()
    {
        return $this->get_default_property(self::PROPERTY_MOTIVATION);
    }

    public function get_requester_id()
    {
        return $this->get_default_property(self::PROPERTY_REQUESTER_ID);
    }

    public function get_request_date()
    {
        return $this->get_default_property(self::PROPERTY_REQUEST_DATE);
    }

    public function get_valid_from()
    {
        return $this->get_default_property(self::PROPERTY_VALID_FROM);
    }

    public function get_valid_until()
    {
        return $this->get_default_property(self::PROPERTY_VALID_UNTIL);
    }

    public function get_status()
    {
        return $this->get_default_property(self::PROPERTY_STATUS);
    }

    public function set_first_name($first_name)
    {
        $this->set_default_property(self::PROPERTY_FIRST_NAME, $first_name);
    }

    public function set_last_name($last_name)
    {
        $this->set_default_property(self::PROPERTY_LAST_NAME, $last_name);
    }

    public function set_email($email)
    {
        $this->set_default_property(self::PROPERTY_EMAIL, $email);
    }

    public function set_affiliation($affiliation)
    {
        $this->set_default_property(self::PROPERTY_AFFILIATION, $affiliation);
    }

    public function set_motivation($motivation)
    {
        $this->set_default_property(self::PROPERTY_MOTIVATION, $motivation);
    }

    public function set_requester_id($requester_id)
    {
        $this->set_default_property(self::PROPERTY_REQUESTER_ID, $requester_id);
    }

    public function set_request_date($request_date)
    {
        $this->set_default_property(self::PROPERTY_REQUEST_DATE, $request_date);
    }

    public function set_valid_from($valid_from)
    {
        $this->set_default_property(self::PROPERTY_VALID_FROM, $valid_from);
    }

    public function set_valid_until($valid_until)
    {
        $this->set_default_property(self::PROPERTY_VALID_UNTIL, $valid_until);
    }

    public function set_status($status)
    {
        $this->set_default_property(self::PROPERTY_STATUS, $status);
    }

    public function get_status_icon()
    {
        switch ($this->get_status())
        {
            case self::STATUS_ACCEPTED :
                $path = Theme::getInstance()->getImagePath('Chamilo\Application\CasStorage', 'Status/Accepted');
                break;
            case self::STATUS_PENDING :
                $path = Theme::getInstance()->getImagePath('Chamilo\Application\CasStorage', 'Status/Pending');
                break;
            case self::STATUS_REJECTED :
                $path = Theme::getInstance()->getImagePath('Chamilo\Application\CasStorage', 'Status/Rejected');
                break;
        }
        
        return '<img src="' . $path . '" />';
    }

    public function is_pending()
    {
        return $this->get_status() == self::STATUS_PENDING;
    }

    public function is_rejected()
    {
        return $this->get_status() == self::STATUS_REJECTED;
    }

    public function get_requester_user()
    {
        $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            User::class_name(), 
            (int) $this->get_requester_id());
        return ($user instanceof User ? $user : '');
    }
    
    // function generate_cas_account()
    // {
    // $cas_account = new CasAccount();
    // $cas_account->set_first_name($this->get_first_name());
    // $cas_account->set_last_name($this->get_last_name());
    // $cas_account->set_email($this->get_email());
    // $cas_account->set_affiliation($this->get_affiliation());
    // $cas_account->set_group('-');
    // $cas_account->set_password(md5(Text :: generate_password()));
    // $cas_account->set_status(CasAccount :: STATUS_ENABLED);
    // return $cas_account->create();
    // }
}
