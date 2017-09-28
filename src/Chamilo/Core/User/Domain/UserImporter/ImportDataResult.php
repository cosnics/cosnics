<?php

namespace Chamilo\Core\User\Domain\UserImporter;

/**
 * Describes the result of a single import action
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImportDataResult
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
     * Reference to the ImportData
     *
     * @var ImportData
     */
    protected $importData;

    /**
     * ImportUserResult constructor.
     *
     * @param ImportData $importData
     */
    public function __construct(ImportData $importData)
    {
        $importData->setImportDataResult($this);

        $this->importData = $importData;
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
     * @return $this
     */
    public function setStatus(int $status)
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
     * @return $this
     */
    public function setMessages(array $messages)
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * @return ImportData
     */
    public function getImportData()
    {
        return $this->importData;
    }

    /**
     * @param ImportData $importData
     *
     * @return $this
     */
    public function setImportData(ImportData $importData)
    {
        $this->importData = $importData;

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