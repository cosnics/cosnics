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
class ImportUserData extends ImportData
{
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
     * @var bool
     */
    protected $notifyUser;

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
        parent::__construct($rawImportData, $action);

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
        $this->notifyUser = false;
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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return ImportUserResult | \Chamilo\Core\User\Domain\UserImporter\ImportDataResult
     */
    public function getImportUserResult()
    {
        return $this->importDataResult;
    }

    /**
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param string $officialCode
     *
     * @return $this
     */
    public function setOfficialCode($officialCode)
    {
        $this->officialCode = $officialCode;

        return $this;
    }

    /**
     * @param string $language
     *
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param string $active
     *
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @param string $phone
     *
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @param string $activationDate
     *
     * @return $this
     */
    public function setActivationDate($activationDate)
    {
        $this->activationDate = $activationDate;

        return $this;
    }

    /**
     * @param string $expirationDate
     *
     * @return $this
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * @param string $authSource
     *
     * @return $this
     */
    public function setAuthSource($authSource)
    {
        $this->authSource = $authSource;

        return $this;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param ImportUserResult $importUserResult
     *
     * @return $this
     */
    public function setImportUserResult(ImportUserResult $importUserResult)
    {
        $this->importDataResult = $importUserResult;

        return $this;
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
    public function isActiveSet()
    {
        return !is_null($this->getActive());
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
     * @return bool
     */
    public function mustNotifyUser(): bool
    {
        return $this->notifyUser;
    }

    /**
     * @param bool $notifyUser
     */
    public function setNotifyUser(bool $notifyUser)
    {
        $this->notifyUser = $notifyUser;
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
            $this->setNotifyUser(true);
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

        if(!is_null($this->getActive()))
        {
            $nowActive = (bool) $this->getActive() != 0;

            if(!$user->get_active() && $nowActive)
            {
                $this->setNotifyUser(true);
            }

            $user->set_active($nowActive);
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