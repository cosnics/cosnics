<?php
namespace Chamilo\Core\User\Domain\UserImporter;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Hashing\HashingUtilities;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use RuntimeException;

/**
 * Describes the data to import a single user
 *
 * @package Chamilo\Core\User\Domain\UserImporter
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class ImportUserData extends ImportData
{
    protected ?string $activationDate;

    protected bool $active;

    protected ?string $authSource;

    protected ?string $email;

    protected ?string $expirationDate;

    protected ?string $firstName;

    protected ImportUserResult $importUserResult;

    protected ?string $language;

    protected ?string $lastName;

    protected bool $notifyUser;

    protected ?string $officialCode;

    protected ?string $password;

    protected ?string $phone;

    protected int $status;

    /**
     * The user object referenced to the imported user
     */
    protected ?User $user;

    protected string $username;

    public function __construct(
        string $rawImportData, ?string $action = null, ?string $username = null, ?string $firstName = null,
        ?string $lastName = null, ?string $email = null, ?string $officialCode = null, ?string $language = null,
        int $status = User::STATUS_STUDENT, bool $active = false, ?string $phone = null, ?string $activationDate = null,
        ?string $expirationDate = null, ?string $authSource = null, ?string $password = null
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

    public function getActivationDate(): ?string
    {
        return $this->activationDate;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function getAuthSource(): ?string
    {
        return $this->authSource;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getExpirationDate(): ?string
    {
        return $this->expirationDate;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getImportUserResult(): ImportDataResult
    {
        return $this->importDataResult;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getOfficialCode(): ?string
    {
        return $this->officialCode;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @return string[]
     */
    public function getValidActions(): array
    {
        return [self::ACTION_ADD, self::ACTION_ADD_UPDATE, self::ACTION_UPDATE, self::ACTION_DELETE];
    }

    /**
     * @return string[]
     */
    public function getValidLanguages(): array
    {
        return ['nl', 'en'];
    }

    /**
     * @return int[]
     */
    public function getValidStatuses(): array
    {
        return [User::STATUS_TEACHER, User::STATUS_STUDENT];
    }

    public function hasValidLanguage(): bool
    {
        return in_array($this->getLanguage(), $this->getValidLanguages());
    }

    public function hasValidStatus(): bool
    {
        return in_array($this->getStatus(), $this->getValidStatuses());
    }

    public function isActiveSet(): bool
    {
        return !is_null($this->getActive());
    }

    public function mustNotifyUser(): bool
    {
        return $this->notifyUser;
    }

    public function setActivationDate(?string $activationDate): static
    {
        $this->activationDate = $activationDate;

        return $this;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function setAuthSource(?string $authSource): static
    {
        $this->authSource = $authSource;

        return $this;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function setExpirationDate(?string $expirationDate): static
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function setImportUserResult(ImportUserResult $importUserResult): static
    {
        $this->importDataResult = $importUserResult;

        return $this;
    }

    public function setLanguage(?string $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function setNotifyUser(bool $notifyUser): static
    {
        $this->notifyUser = $notifyUser;

        return $this;
    }

    public function setOfficialCode(?string $officialCode): static
    {
        $this->officialCode = $officialCode;

        return $this;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Sets the properties for the user associated with this imported data
     *
     * @throws \Exception
     */
    public function setPropertiesForUser(HashingUtilities $hashingUtilities): void
    {
        $user = $this->getUser();

        if (!$user instanceof User)
        {
            throw new RuntimeException(
                'The current imported user data does not have an associated user object. ' .
                'Please set the user object before calling this method'
            );
        }

        if ($this->isNew())
        {
            $user->set_username($this->getUsername());
            $user->set_platformadmin(false);
        }

        if (!empty($this->getFirstName()))
        {
            $user->set_firstname($this->getFirstName());
        }

        if (!empty($this->getLastName()))
        {
            $user->set_lastname($this->getLastName());
        }

        $password = $this->getPassword();
        if (empty($password) && $this->isNew())
        {
            $password = uniqid();
        }

        if (!empty($password))
        {
            $this->setNotifyUser(true);
            $user->set_password($hashingUtilities->hashString($password));
        }

        if (!empty($this->getEmail()))
        {
            $user->set_email($this->getEmail());
        }

        if (!empty($this->getOfficialCode()))
        {
            $user->set_official_code($this->getOfficialCode());
        }

        if (!empty($this->getStatus()))
        {
            $user->set_status($this->getStatus());
        }

        if (!is_null($this->getActive()))
        {
            if (!$user->get_active() && $this->getActive())
            {
                $this->setNotifyUser(true);
            }

            $user->set_active($this->getActive());
        }

        if (!empty($this->getPhone()))
        {
            $user->set_phone($this->getPhone());
        }

        if (!empty($this->getAuthSource()))
        {
            $user->set_auth_source($this->getAuthSource());
        }

        $activationDate = $this->getActivationDate();

        if (!empty($activationDate))
        {
            $activationDate = DatetimeUtilities::getInstance()->timeFromDatepicker($activationDate);
            $user->set_activation_date($activationDate);
        }

        $expirationDate = $this->getExpirationDate();
        if (!empty($expirationDate))
        {
            $expirationDate = DatetimeUtilities::getInstance()->timeFromDatepicker($expirationDate);
            $user->set_expiration_date($expirationDate);
        }
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function setStatusToStudent(): static
    {
        return $this->setStatus(User::STATUS_STUDENT);
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }
}