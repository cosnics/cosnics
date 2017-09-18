<?php

namespace Chamilo\Core\User\Domain\UserImporter;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Hashing\HashingUtilities;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 * Describes the data to import a single user
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImportUserData
{
    const ACTION_ADD = 'A';
    const ACTION_UPDATE = 'U';
    const ACTION_ADD_UPDATE = 'UA';
    const ACTION_DELETE = 'D';

    /**
     * The imported data as a raw string. It is used to give the user the opportunity to retry the failed imports.
     *
     * @var string
     */
    protected $rawImportData;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $officialCode;

    /**
     * @var string
     */
    protected $language;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $active;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var string
     */
    protected $activationDate;

    /**
     * @var string
     */
    protected $expirationDate;

    /**
     * @var string
     */
    protected $authSource;

    /**
     * @var string
     */
    protected $password;

    /**
     * The user object referenced to the imported user
     *
     * @var User
     */
    protected $user;

    /**
     * @var ImportUserResult
     */
    protected $importUserResult;

    /**
     * @param string $rawImportData
     * @param string $action
     * @param string $username
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $officialCode
     * @param string $language
     * @param string $status
     * @param string $active
     * @param string $phone
     * @param string $activationDate
     * @param string $expirationDate
     * @param string $authSource
     * @param string $password
     */
    public function __construct(
        $rawImportData, $action = null, $username = null, $firstName = null, $lastName = null,
        $email = null, $officialCode = null, $language = null, $status = null, $active = null, $phone = null,
        $activationDate = null, $expirationDate = null, $authSource = null, $password = null
    )
    {
        $this->rawImportData = $rawImportData;
        $this->action = $action;
        $this->username = $username;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->officialCode = $officialCode;
        $this->language = $language;
        $this->status = $status;
        $this->active = $active;
        $this->phone = $phone;
        $this->activationDate = $activationDate;
        $this->expirationDate = $expirationDate;
        $this->authSource = $authSource;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getRawImportData()
    {
        return $this->rawImportData;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getOfficialCode()
    {
        return $this->officialCode;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getActivationDate()
    {
        return $this->activationDate;
    }

    /**
     * @return string
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @return string
     */
    public function getAuthSource()
    {
        return $this->authSource;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return ImportUserResult
     */
    public function getImportUserResult(): ImportUserResult
    {
        return $this->importUserResult;
    }

    /**
     * @param string $rawImportData
     *
     * @return ImportUserData
     */
    public function setRawImportData(string $rawImportData): ImportUserData
    {
        $this->rawImportData = $rawImportData;

        return $this;
    }

    /**
     * @param string $action
     *
     * @return ImportUserData
     */
    public function setAction(string $action): ImportUserData
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @param string $username
     *
     * @return ImportUserData
     */
    public function setUsername(string $username): ImportUserData
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @param string $firstName
     *
     * @return ImportUserData
     */
    public function setFirstName(string $firstName): ImportUserData
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @param string $lastName
     *
     * @return ImportUserData
     */
    public function setLastName(string $lastName): ImportUserData
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @param string $email
     *
     * @return ImportUserData
     */
    public function setEmail(string $email): ImportUserData
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param string $officialCode
     *
     * @return ImportUserData
     */
    public function setOfficialCode(string $officialCode): ImportUserData
    {
        $this->officialCode = $officialCode;

        return $this;
    }

    /**
     * @param string $language
     *
     * @return ImportUserData
     */
    public function setLanguage(string $language): ImportUserData
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @param string $status
     *
     * @return ImportUserData
     */
    public function setStatus(string $status): ImportUserData
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param string $active
     *
     * @return ImportUserData
     */
    public function setActive(string $active): ImportUserData
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @param string $phone
     *
     * @return ImportUserData
     */
    public function setPhone(string $phone): ImportUserData
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @param string $activationDate
     *
     * @return ImportUserData
     */
    public function setActivationDate(string $activationDate): ImportUserData
    {
        $this->activationDate = $activationDate;

        return $this;
    }

    /**
     * @param string $expirationDate
     *
     * @return ImportUserData
     */
    public function setExpirationDate(string $expirationDate): ImportUserData
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * @param string $authSource
     *
     * @return ImportUserData
     */
    public function setAuthSource(string $authSource): ImportUserData
    {
        $this->authSource = $authSource;

        return $this;
    }

    /**
     * @param string $password
     *
     * @return ImportUserData
     */
    public function setPassword(string $password): ImportUserData
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @param User $user
     *
     * @return ImportUserData
     */
    public function setUser(User $user): ImportUserData
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param ImportUserResult $importUserResult
     *
     * @return ImportUserData
     */
    public function setImportUserResult(ImportUserResult $importUserResult): ImportUserData
    {
        $this->importUserResult = $importUserResult;

        return $this;
    }

    /**
     * Returns whether or not this user should be created as a new user
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->getAction() == self::ACTION_ADD;
    }

    /**
     * Returns whether or not this user should be created if the username is not found or updated if the
     * username is found
     *
     * @return bool
     */
    public function isNewOrUpdate()
    {
        return $this->getAction() == self::ACTION_ADD_UPDATE;
    }

    /**
     * Returns whether or not this user should be updated
     *
     * @return bool
     */
    public function isUpdate()
    {
        return $this->getAction() == self::ACTION_UPDATE;
    }

    /**
     * Returns whether or not this user should be deleted
     *
     * @return bool
     */
    public function isDelete()
    {
        return $this->getAction() == self::ACTION_DELETE;
    }

    /**
     * Returns the list of valid actions
     *
     * @return array
     */
    public function getValidActions()
    {
        return [self::ACTION_ADD, self::ACTION_ADD_UPDATE, self::ACTION_UPDATE, self::ACTION_DELETE];
    }

    /**
     * Returns whether or not this imported user has a valid action
     *
     * @return bool
     */
    public function hasValidAction()
    {
        return in_array($this->getAction(), $this->getValidActions());
    }

    /**
     * Sets the action to new
     */
    public function setActionToNew()
    {
        $this->setAction(self::ACTION_ADD);
    }

    /**
     * Sets the action to update
     */
    public function setActionToUpdate()
    {
        $this->setAction(self::ACTION_UPDATE);
    }

    /**
     * Returns the valid statuses
     *
     * @return int[]
     */
    public function getValidStatuses()
    {
        return [User::STATUS_TEACHER, User::STATUS_STUDENT];
    }

    /**
     * Returns whether or not the status
     *
     * @return bool
     */
    public function hasValidStatus()
    {
        return in_array($this->getStatus(), $this->getValidStatuses());
    }

    /**
     * Sets the status for this imported user to student
     */
    public function setStatusToStudent()
    {
        $this->setStatus(User::STATUS_STUDENT);
    }

    /**
     * @return bool
     */
    public function isActiveNotSet()
    {
        return is_null($this->getActive());
    }

    /**
     * Returns the valid languages
     *
     * @return string[]
     */
    public function getValidLanguages()
    {
        return ['nl', 'en'];
    }

    /**
     * Returns whether or not the status
     *
     * @return bool
     */
    public function hasValidLanguage()
    {
        return in_array($this->getLanguage(), $this->getValidLanguages());
    }

    /**
     * Sets the properties for the user associated with this imported data
     *
     * @param HashingUtilities $hashingUtilities
     */
    public function setPropertiesForUser(HashingUtilities $hashingUtilities)
    {
        $user = $this->getUser();
        if (!$user instanceof User)
        {
            throw new \RuntimeException(
                'The current imported user data does not have an associated user object. ' .
                'Please set the user object before calling this method'
            );
        }

        if($this->isNew())
        {
            $user->set_username($this->getUsername());
            $user->set_platformadmin(0);
        }

        if(!empty($this->getFirstName()))
        {
            $user->set_firstname($this->getFirstName());
        }

        if(!empty($this->getLastName()))
        {
            $user->set_lastname($this->getLastName());
        }

        $password = $this->getPassword();
        if (empty($password) && $this->isNew())
        {
            $password = uniqid();
        }

        if(!empty($password))
        {
            $user->set_password($hashingUtilities->hashString($password));
        }

        if(!empty($this->getEmail()))
        {
            $user->set_email($this->getEmail());
        }

        if(!empty($this->getOfficialCode()))
        {
            $user->set_official_code($this->getOfficialCode());
        }

        if(!empty($this->getStatus()))
        {
            $user->set_status($this->getStatus());
        }

        if(!empty($this->getActive()))
        {
            $user->set_active($this->getActive());
        }

        if(!empty($this->getPhone()))
        {
            $user->set_phone($this->getPhone());
        }

        if(!empty($this->getAuthSource()))
        {
            $user->set_auth_source($this->getAuthSource());
        }

        $activationDate = $this->getActivationDate();
        if (!empty($activationDate))
        {
            $activationDate = DatetimeUtilities::time_from_datepicker($activationDate);
            $user->set_activation_date($activationDate);
        }

        $expirationDate = $this->getExpirationDate();
        if (!empty($expirationDate))
        {
            $expirationDate = DatetimeUtilities::time_from_datepicker($expirationDate);
            $user->set_expiration_date($expirationDate);
        }
    }
}