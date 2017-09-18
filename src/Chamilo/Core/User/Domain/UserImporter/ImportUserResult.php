<?php

namespace Chamilo\Core\User\Domain\UserImporter;

/**
 * Describes the result of a single import action
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImportUserResult
{
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 2;

    /**
     * @var int
     */
    protected $status;

    /**
     * Detailed logging / debugging messages.
     *
     * @var string[]
     */
    protected $messages;

    /**
     * Reference to the ImportUserData
     *
     * @var ImportUserData
     */
    protected $importUserData;

    /**
     * ImportUserResult constructor.
     *
     * @param ImportUserData $importUserData
     */
    public function __construct(ImportUserData $importUserData)
    {
        $importUserData->setImportUserResult($this);

        $this->importUserData = $importUserData;
        $this->messages = [];
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return ImportUserResult
     */
    public function setStatus(int $status): ImportUserResult
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param string[] $messages
     *
     * @return ImportUserResult
     */
    public function setMessages(array $messages): ImportUserResult
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * @return ImportUserData
     */
    public function getImportUserData(): ImportUserData
    {
        return $this->importUserData;
    }

    /**
     * @param ImportUserData $importUserData
     *
     * @return ImportUserResult
     */
    public function setImportUserData(ImportUserData $importUserData): ImportUserResult
    {
        $this->importUserData = $importUserData;

        return $this;
    }

    /**
     * Adds a message to the list of messages
     *
     * @param string $message
     */
    public function addMessage($message)
    {
        $this->messages[] = $message;
    }

    /**
     * Sets the status to failed
     */
    public function setFailed()
    {
        $this->setStatus(self::STATUS_FAILED);
    }

    /**
     * Sets the status to success
     */
    public function setSuccessful()
    {
        $this->setStatus(self::STATUS_SUCCESS);
    }

    /**
     * Returns whether or not the import has failed
     *
     * @return bool
     */
    public function hasFailed()
    {
        return $this->getStatus() == self::STATUS_FAILED;
    }

    /**
     * Returns whether or not the import is successful
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->getStatus() == self::STATUS_SUCCESS;
    }

    /**
     * Returns whether or not the import is completed
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->hasFailed() || $this->isSuccessful();
    }

}