<?php
namespace Chamilo\Application\CasStorage\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package Chamilo\Application\CasStorage\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Account extends DataClass
{
    // Properties
    const PROPERTY_EMAIL = 'email';
    const PROPERTY_LAST_NAME = 'last_name';
    const PROPERTY_FIRST_NAME = 'first_name';
    const PROPERTY_PERSON_ID = 'person_id';
    const PROPERTY_PERSON_NUMBER = 'person_number';
    const PROPERTY_PERSON_TYPE = 'person_type';
    const PROPERTY_COMMON_NAME = 'common_name';
    const PROPERTY_DISTINGUISHED_NAME = 'distinguished_name';
    const PROPERTY_ACCOUNT_NAME = 'account_name';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_PASSWORD = 'password';

    // Status
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    public static function get_default_property_names($extendedPropertyNames = array())
    {
        $extendedPropertyNames[] = self::PROPERTY_EMAIL;
        $extendedPropertyNames[] = self::PROPERTY_LAST_NAME;
        $extendedPropertyNames[] = self::PROPERTY_FIRST_NAME;
        $extendedPropertyNames[] = self::PROPERTY_PERSON_ID;
        $extendedPropertyNames[] = self::PROPERTY_PERSON_NUMBER;
        $extendedPropertyNames[] = self::PROPERTY_PERSON_TYPE;
        $extendedPropertyNames[] = self::PROPERTY_COMMON_NAME;
        $extendedPropertyNames[] = self::PROPERTY_DISTINGUISHED_NAME;
        $extendedPropertyNames[] = self::PROPERTY_ACCOUNT_NAME;
        $extendedPropertyNames[] = self::PROPERTY_STATUS;
        $extendedPropertyNames[] = self::PROPERTY_PASSWORD;

        return parent::get_default_property_names($extendedPropertyNames);
    }

    public function getEmail()
    {
        return $this->get_default_property(self::PROPERTY_EMAIL);
    }

    public function setEmail($email)
    {
        $this->set_default_property(self::PROPERTY_EMAIL, $email);
        return $this;
    }

    public function getLastName()
    {
        return $this->get_default_property(self::PROPERTY_LAST_NAME);
    }

    public function setLastName($lastName)
    {
        $this->set_default_property(self::PROPERTY_LAST_NAME, $lastName);
        return $this;
    }

    public function getFirstName()
    {
        return $this->get_default_property(self::PROPERTY_FIRST_NAME);
    }

    public function setFirstName($firstName)
    {
        $this->set_default_property(self::PROPERTY_FIRST_NAME, $firstName);
        return $this;
    }

    public function getPersonId()
    {
        return $this->get_default_property(self::PROPERTY_PERSON_ID);
    }

    public function setPersonId($personId)
    {
        $this->set_default_property(self::PROPERTY_PERSON_ID, $personId);
        return $this;
    }

    public function getPersonNumber()
    {
        return $this->get_default_property(self::PROPERTY_PERSON_NUMBER);
    }

    public function setPersonNumber($personNumber)
    {
        $this->set_default_property(self::PROPERTY_PERSON_NUMBER, $personNumber);
        return $this;
    }

    public function getPersonType()
    {
        return $this->get_default_property(self::PROPERTY_PERSON_TYPE);
    }

    public function setPersonType($personType)
    {
        $this->set_default_property(self::PROPERTY_PERSON_TYPE, $personType);
        return $this;
    }

    public function getCommonName()
    {
        return $this->get_default_property(self::PROPERTY_COMMON_NAME);
    }

    public function setCommonName($commonName)
    {
        $this->set_default_property(self::PROPERTY_COMMON_NAME, $commonName);
        return $this;
    }

    public function getDistinguishedName()
    {
        return $this->get_default_property(self::PROPERTY_DISTINGUISHED_NAME);
    }

    public function setDistinguishedName($distinguishedName)
    {
        $this->set_default_property(self::PROPERTY_DISTINGUISHED_NAME, $distinguishedName);
        return $this;
    }

    public function getAccountName()
    {
        return $this->get_default_property(self::PROPERTY_ACCOUNT_NAME);
    }

    public function setAccountName($accountName)
    {
        $this->set_default_property(self::PROPERTY_ACCOUNT_NAME, $accountName);
        return $this;
    }

    public function getStatus()
    {
        return $this->get_default_property(self::PROPERTY_STATUS);
    }

    public function setStatus($status)
    {
        $this->set_default_property(self::PROPERTY_STATUS, $status);
        return $this;
    }

    public function getPassword()
    {
        return $this->get_default_property(self::PROPERTY_PASSWORD);
    }

    public function setPassword($password)
    {
        $this->set_default_property(self::PROPERTY_PASSWORD, $password);
        return $this;
    }

    /**
     *
     * @return string
     */
    public static function get_table_name()
    {
        return 'external_users';
    }
}
